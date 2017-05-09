<?php
/**
 * Acaldeira_CsvExport
 *
 * @category    Acaldeira
 * @package     Acaldeira_CsvExport
 * @copyright   Copyright (c) 2017 Acaldeira. (http://www.Acaldeira.com)
 */
set_time_limit(0);
ini_set('memory_limit', '-1');


class Acaldeira_CsvExport_Model_Reporter
{
    const CRON_REPORT_SCHEDULER_LAST_RUN_AT         = 'cron_accsvexport_process_reporters';
    const CRON_REPORT_ID_LAST_RUN_AT                = 'cron_report_%d_last_run_at';
    const CRON_REPORT_SCHEDULER_MIN_INTERVAL        = 'accsvexport/report/cron_min_interval';

    protected $mainQuery;
    protected $limit = 100;
    protected $pages;
    protected $resource;
    protected $dataSet = array();
    protected $io;
    protected $filename;

    public function __construct($query = "", $limit = 100)
    {
        $this->resource = Mage::getSingleton('core/resource');
        if ($query) {
            $this->mainQuery = $query;
        }

        $this->limit = $limit;
    }

    /**
     * @param $query
     */
    public function setQuery($query)
    {
        $this->mainQuery = $query;
    }

    /**
     * @param int $limit
     */
    public function setLimit($limit = 100)
    {
        $this->limit = $limit;
    }

    public function setFilename($filename = "")
    {
        $this->filename = $filename;
    }


    /**
     * @param string $callback
     */
    public function iterate($callback = "")
    {
        $total = $this->getCountTotal();
        $pages = ceil($total / $this->limit);
        $mainQuery = $this->mainQuery;

        for($i = 0; $i < $pages; $i++) {

            $offset = $this->limit * $i;
            $limit = $this->limit;
            $data = $this->loadQuery("SELECT * FROM ($mainQuery) AS TT1 LIMIT $limit OFFSET $offset");
            call_user_func_array(array($this, $callback), array($data));
        }
    }

    /**
     * @param $query
     * @return mixed
     */
    public function fetchAll($query)
    {
        $readConnection = $this->resource->getConnection('core_read');
        return $readConnection->fetchAll($query);
    }

    /**
     * @return mixed
     */
    public function getCountTotal()
    {
        $mainQuery = $this->mainQuery;
        $readConnection = $this->resource->getConnection('core_read');
        $countQuery = "SELECT COUNT(*) FROM ($mainQuery) AS TT1";
        return $readConnection->fetchOne($countQuery);
    }

    /**
     * @param string $query
     * @return mixed
     */
    private function loadQuery($query = "")
    {
        $readConnection = $this->resource->getConnection('core_read');
        return $readConnection->fetchAll($query);
    }

    /**
     * @return array
     */
    public function createCsv()
    {
        $this->io = new Varien_Io_File();
        $path = $this->_getHelper()->getExportDir();
        $name = md5(microtime());
        $file = $path . DS . $this->filename;
        $this->io->setAllowCreateFolders(true);
        $this->io->open(array('path' => $path));
        $this->io->streamOpen($file, 'w+');
        $this->io->streamLock(true);
        $this->iterate('writeCsvData');
        $this->io->streamUnlock();
        $this->io->close();

        return array(
            'type'  => 'filename',
            'value' => $file,
            'rm'    => true // can delete file after use
        );
    }

    /**
     * @param $data
     */
    protected function writeCsvData($data)
    {
        foreach ($data as $item) {
            $this->io->streamWriteCsv($item);
        }
    }


    /**
     *
     * @return Acaldeira_CsvExport_Helper_Data|Mage_Core_Helper_Abstract
     */
    private function _getHelper()
    {
        return Mage::helper('accsvexport');
    }

    /**
     * @param $schedule Mage_Cron_Model_Schedule
     * @return $this
     */
    public function run($schedule)
    {
        try {

            $jobCustomData = Mage::helper('core')->jsonDecode($schedule->getCustomData());

            if (!is_array($jobCustomData)) {
                return;
            }

            $report = Mage::getModel('accsvexport/report')->load($jobCustomData['report_id']);

            /* @var $report Acaldeira_CsvExport_Model_Report */
            if ($reportId = $report->getId()) {

                /**
                 * check if can run again (min 15 minutes)
                 */
                $lastRun = Mage::app()->loadCache(sprintf(self::CRON_REPORT_ID_LAST_RUN_AT, $reportId));
                if ($lastRun > time() - Mage::getStoreConfig(self::CRON_REPORT_SCHEDULER_MIN_INTERVAL)*60) {
                    return $this;
                }

                $this->setQuery("SELECT * FROM ".$report->getViewName());
                $this->setFilename($report->getName());
                $this->createCsv();

                /**
                 * save time schedules generation was ran with no expiration
                 */
                Mage::app()->saveCache(time(), sprintf(self::CRON_REPORT_ID_LAST_RUN_AT, $reportId), array('cron_report'), null);
            }

        } catch (Exception $e) {

            Mage::logException($e);
        }

    }

}




