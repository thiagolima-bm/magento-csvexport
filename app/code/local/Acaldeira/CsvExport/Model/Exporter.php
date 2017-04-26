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

class Acaldeira_CsvExport_Model_Exporter
{
    const MODE_CUSTOMER     = 'mode.customer';
    const MODE_ORDER        = 'mode.order';
    const MODE_CATALOG      = 'mode.catalog';

    protected $_template    = '';
    protected $_header      = '';
    protected $_csvName     = '';
    protected $_csvFile     = '';
    protected $_csvData     = array();

    /**
     *
     */
    public function run()
    {
        if (!$this->_getHelper()->isEnabled())
            return;

        try {
            $mode = $this->_searchMode();

            if ($mode) {

                if ($this->_getHelper()->isBeingProcessed($mode))
                    return;

                $this->_getHelper()->createProcessing($mode);

                $this->_initConfig($mode);
                $_csvFile = new Varien_File_Csv();
                $_csvFile->setDelimiter($this->_getHelper()->getDelimiter());
                $_csvFile->setEnclosure($this->_getHelper()->getEnclosure());
                $this->_setCsvHeader();
                $this->_setCsvBody($mode);
                $_csvFile->saveData($this->_csvName, $this->_csvData);
                $this->_getHelper()->deleteProcessing($mode);
                $this->_getHelper()->deleteMode($mode);
            }
        } catch (Exception $e) {

            echo $e->getMessage() . PHP_EOL;

            $this->getLogger()->log('accsvexport', $e->getMessage());
        }

    }

    /**
     *
     * @param $mode
     */
    private function _initConfig($mode)
    {
        switch ($mode) {
            case self::MODE_CUSTOMER:
                $this->_template = $this->_getHelper()->getCustomerTemplate();
                $this->_header = $this->_getHelper()->getCustomerHeader();
                $this->_csvName = $this->_getHelper()->getExportDir() . DS . $this->_getHelper()->getCustomerCsvName();
                break;
            case self::MODE_ORDER:
                $this->_template = $this->_getHelper()->getOrderTemplate();
                $this->_header = $this->_getHelper()->getOrderHeader();
                $this->_csvName = $this->_getHelper()->getExportDir() . DS . $this->_getHelper()->getOrdersCsvName();
                break;
            case self::MODE_CATALOG:
                $this->_template = $this->_getHelper()->getCatalogTemplate();
                $this->_header = $this->_getHelper()->getCatalogHeader();
                $this->_csvName = $this->_getHelper()->getExportDir() . DS . $this->_getHelper()->getCatalogCsvName();
                break;
            default:

        }
    }

    /**
     *
     * @return string
     */
    private function _searchMode()
    {
        $searchDir = $this->_getHelper()->getExportDir();
        $mode = '';
        foreach (scandir($searchDir) as $file) {
            if (stripos($file, 'mode') !== FALSE) {
                switch ($file) {
                    case self::MODE_CUSTOMER:
                        $mode = self::MODE_CUSTOMER;
                        break;
                    case self::MODE_ORDER:
                        $mode = self::MODE_ORDER;
                        break;
                    case self::MODE_CATALOG:
                        $mode = self::MODE_CATALOG;
                        break;
                }
            }
        }
        return $mode;
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
     *
     * @param $csvData array
     */
    private function _setCsvHeader($csvData)
    {
        $this->_csvData[] = explode($this->_getHelper()->getDelimiter(), $this->_header);
    }

    /**
     *
     * @param $mode String
     */
    private function _setCsvBody($mode)
    {
        $csvData = array();
        switch ($mode) {
            case self::MODE_CUSTOMER:
                $this->_setCustomerBody();
                break;
            case self::MODE_ORDER:
                $this->_setOrderBody();
                break;
            case self::MODE_CATALOG:
                $this->_setCatalogBody();
                break;
        }
    }

    /**
     *
     */
    private function _setCustomerBody()
    {
        $_collection = Mage::getModel('customer/customer')
            ->getCollection()
            ->addAttributeToSelect('*')
        ;

        $_templateProcessor = Mage::getModel('core/email_template');
        $_templateProcessor->setTemplateText($this->_template);
        $this->_iteratePagination($_collection, 'customer', $_templateProcessor);
    }

    /**
     *
     */
    private function _setOrderBody()
    {
        $_collection = Mage::getModel('sales/order')
            ->getCollection()
        ;

        $_templateProcessor = Mage::getModel('core/email_template');
        $_templateProcessor->setTemplateText($this->_template);
        $this->_iteratePagination($_collection, 'order', $_templateProcessor);
    }

    /**
     *
     */
    private function _setCatalogBody()
    {
        $_collection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('*')
        ;
        $_templateProcessor = Mage::getModel('core/email_template');
        $_templateProcessor->setTemplateText($this->_template);
        $this->_iteratePagination($_collection, 'product', $_templateProcessor);
    }

    /**
     * @param $collection
     * @param $objectName
     * @param $templateProcessor
     */
    protected function _iteratePagination($collection, $objectName, $templateProcessor)
    {
        $collection->setPageSize(100);
        $pages = $collection->getLastPageNumber();
        $currentPage = 1;

        do {
            $collection->setCurPage($currentPage);
            $collection->load();

            foreach ($collection as $_item) {

                $data = $templateProcessor->getProcessedTemplate(array($objectName => $_item));
                $this->_csvData[] = explode($this->_getHelper()->getDelimiter(), $data);
            }

            $currentPage++;
            //clear collection and free memory
            $collection->clear();
        } while ($currentPage <= $pages);
    }
}