<?php
/**
 * Acaldeira_CsvExport
 *
 * @category    Acaldeira
 * @package     Acaldeira_CsvExport
 * @copyright   Copyright (c) 2017 Acaldeira. (http://www.Acaldeira.com)
 */
use Acaldeira_CsvExport_Model_Exporter as Exporter;
class Acaldeira_CsvExport_Helper_Data extends Mage_Core_Helper_Abstract
{

    const XML_PATH_BASE      = 'accsvexport/%s/%s';

    /**
     * @return string
     */
    public function getExportDir()
    {
        return $this->_getBaseDir() . DS . 'export' . DS . $this->getFolder();
    }

    /**
     * @return string
     */
    public function getDelimiter()
    {
        return $this->getConf('general', 'delimiter');
    }

    /**
     * @return string
     */
    public function getEnclosure()
    {
        return $this->getConf('general', 'enclosure');
    }

    /**
     * @return string
     */
    public function getCustomerCsvName()
    {
        return $this->getConf('customer', 'filename');
    }

    /**
     * @return string
     */
    public function getOrdersCsvName()
    {
        return $this->getConf('order', 'filename');
    }

    /**
     * @return string
     */
    public function getCustomerTemplate()
    {
        return $this->getConf('customer', 'template');
    }

    /**
     * @return string
     */
    public function getCatalogTemplate()
    {
        return $this->getConf('catalog', 'template');
    }

    /**
     * @return string
     */
    public function getCustomerHeader()
    {
        return $this->getConf('customer', 'header');
    }

    /**
     * @return string
     */
    public function getOrderTemplate()
    {
        return $this->getConf('order', 'template');
    }

    /**
     * @return string
     */
    public function getOrderHeader()
    {
        return $this->getConf('order', 'header');
    }

    /**
     * @return string
     */
    public function getCatalogHeader()
    {
        return $this->getConf('catalog', 'header');
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool) $this->getConf('general', 'enabled');
    }

    /**
     * @param string $group
     * @param string $field
     * @param null $store
     * @return string
     */
    public function getConf($group = "", $field = "", $store = null) {
        if(!$store) $store = Mage::app()->getStore();
        return Mage::getStoreConfig(sprintf(self::XML_PATH_BASE, $group, $field), $store);
    }

    /**
     * @return string
     */
    protected function _getBaseDir()
    {
        return Mage::getBaseDir('base') . DS . 'var';
    }

    /**
     * @param $filePath
     * @return string
     */
    public function fileTime($filePath)
    {

        if (file_exists($filePath)) {
            $fileName = basename($filePath);
            return $this->__("The last modification of %s was: %s", $fileName, date ("F d Y H:i:s.", filemtime($filePath)));
        }
    }

    /**
     * @param $mode
     * @return string
     */
    public function getCsvFileName($mode)
    {
        $csvFileName = '';
        switch ($mode) {
            case Exporter::MODE_CUSTOMER:
                $csvFileName = $this->getCustomerCsvName();
                break;
            case Exporter::MODE_ORDER:
                $csvFileName = $this->getOrdersCsvName();
                break;
            case Exporter::MODE_CATALOG:
                $csvFileName = $this->getCatalogCsvName();
                break;
        }
        return $csvFileName;
    }

    /**
     * @param $mode
     */
    public function createMode($mode)
    {
        $searchDir = $this->getExportDir();
        $fileIo = new Varien_Io_File;
        $fileIo->write($searchDir . DS . $mode, "1");
    }

    /**
     * @param $mode
     */
    public function deleteMode($mode)
    {
        $searchDir = $this->getExportDir();
        foreach (scandir($searchDir) as $file) {
            if (is_file($searchDir . DS . $file) && $file == $mode) {
                unlink($searchDir . DS . $file);
                break;
            }
        }
    }

    /**
     * Used to avoid double processing
     *
     * @param $mode
     */
    public function createProcessing($mode)
    {
        $mode.= '.processing';
        $this->createMode($mode);
    }

    /**
     * Used to avoid double processing
     *
     * @param $mode
     */
    public function deleteProcessing($mode)
    {
        $mode.= '.processing';
        $this->deleteMode($mode);
    }

    /**
     * @param $mode
     * @return bool|string
     */
    public function isBeingProcessed($mode)
    {
        $mode.= '.processing';
        $fileIo = new Varien_Io_File;
        $searchDir = $this->getExportDir();
        return $fileIo->read($searchDir . DS .$mode);
    }

    /**
     * @param $mode
     * @return bool|string
     */
    public function isModeQueued($mode)
    {
        $fileIo = new Varien_Io_File;
        $searchDir = $this->getExportDir();
        return $fileIo->read($searchDir . DS .$mode);
    }

    /**
     * @return string
     */
    public function getCatalogCsvName()
    {
        return $this->getConf('catalog', 'filename');
    }

    /**
     * @return string
     */
    public function getFolder()
    {
        return $this->getConf('general', 'folder');
    }

    /**
     * @return bool
     */
    public function isCsvExporterEnabled()
    {
        return (bool) $this->getConf('general', 'enabled');
    }

}