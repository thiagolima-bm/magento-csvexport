<?php
/**
 * Acaldeira_CsvExport
 *
 * @category    Acaldeira
 * @package     Acaldeira_CsvExport
 * @copyright   Copyright (c) 2017 Acaldeira. (http://www.Acaldeira.com)
 */
class Acaldeira_CsvExport_Model_Cron
{
    const JOB_CODE = 'accsvexport_process_reporters_run';

    /**
     * @param $schedule Mage_Cron_Model_Schedule
     * @return $this
     */
    public function generatecrons($schedule)
    {
        if (!Mage::helper('accsvexport')->isCsvExporterEnabled()) {
            return;
        }

        $reports = Mage::getModel('accsvexport/report')
            ->getCollection()
            ->addFieldToFilter('is_active', true)
        ;
        $jobsXML = "<jobs>";
        foreach ($reports as $report) {

            $jobCode = self::JOB_CODE;
            $customData = Mage::helper('core')->jsonEncode(array("report_id" => $report->getId()));
            $cronExpr = $report->getCronExpr();
            $jobsXML .= "<$jobCode>";
            $jobsXML .= "<schedule><custom_data>$customData</custom_data>";
            $jobsXML .= "<cron_expr>$cronExpr</cron_expr></schedule>";
            $jobsXML .= "</$jobCode>";
        }

        $jobsXML .= "</jobs>";
        $config = new Mage_Core_Model_Config_Element($jobsXML);
        $schedules = $this->getPendingSchedules();
        $exists = array();

        foreach ($schedules->getIterator() as $_schedule) {
            $exists[$_schedule->getJobCode().'/'.$_schedule->getScheduledAt()] = 1;
        }

        $this->_generateJobs($config->children(), $exists);
    }

    /**
     * Generate jobs for config information
     *
     * @param   $jobs
     * @param   array $exists
     * @return  Mage_Cron_Model_Observer
     */
    protected function _generateJobs($jobs, $exists)
    {
        $scheduleAheadFor = Mage::getStoreConfig(Mage_Cron_Model_Observer::XML_PATH_SCHEDULE_GENERATE_EVERY)*60;
        $schedule = Mage::getModel('cron/schedule');

        foreach ($jobs as $jobCode => $jobConfig) {
            $cronExpr = null;
            if ($jobConfig->schedule->config_path) {
                $cronExpr = Mage::getStoreConfig((string)$jobConfig->schedule->config_path);
            }
            if (empty($cronExpr) && $jobConfig->schedule->cron_expr) {
                $cronExpr = (string)$jobConfig->schedule->cron_expr;
            }
            if (!$cronExpr || $cronExpr == 'always') {
                continue;
            }
            $customData = null;
            if ($jobConfig->schedule->custom_data) {
                $customData = (string)$jobConfig->schedule->custom_data;
            }

            $now = time();
            $timeAhead = $now + $scheduleAheadFor;
            $schedule->setJobCode($jobCode)
                ->setCronExpr($cronExpr)
                ->setCustomData($customData)
                ->setStatus(Mage_Cron_Model_Schedule::STATUS_PENDING);

            for ($time = $now; $time < $timeAhead; $time += 60) {
                $ts = strftime('%Y-%m-%d %H:%M:00', $time);
                if (!empty($exists[$jobCode.'/'.$ts])) {
                    // already scheduled
                    continue;
                }
                if (!$schedule->trySchedule($time)) {
                    // time does not match cron expression
                    continue;
                }
                $schedule->unsScheduleId()->save();
            }
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPendingSchedules()
    {
        if (!$this->_pendingSchedules) {
            $this->_pendingSchedules = Mage::getModel('cron/schedule')->getCollection()
                ->addFieldToFilter('status', Mage_Cron_Model_Schedule::STATUS_PENDING)
                ->addFieldToFilter('job_code', self::JOB_CODE)
                ->orderByScheduledAt()
                ->load();
        }
        return $this->_pendingSchedules;
    }
}
