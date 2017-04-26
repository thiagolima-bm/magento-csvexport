<?php
/**
 * Acaldeira_CsvExport
 *
 * @category    Acaldeira
 * @package     Acaldeira_CsvExport
 * @copyright   Copyright (c) 2017 Acaldeira. (http://www.Acaldeira.com)
 */
class Acaldeira_CsvExport_Block_Adminhtml_System_Config_File_Info extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected $_mode = '';

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '';
        $fileName = $this->_getHelper()->getCsvFileName($this->_mode);
        $filePath = $this->_getHelper()->getExportDir();
        $info = $this->_getHelper()->fileTime($filePath . DS . $fileName);
        if ($info) {
            $html .= $info;
            $url = Mage::helper('adminhtml')->getUrl("adminhtml/csvexport_csv/download", array('mode' => $this->_mode));
            $html .= "<a target='_blank' href=$url>Download</a>";
        } elseif ($this->_getHelper()->isModeQueued($this->_mode)) {
            $html .=  $this->_getHelper()->__('The file %s will be generated soon, please wait the next process start', $fileName);
        } elseif ($this->_getHelper()->isBeingProcessed($this->_mode)) {
            $html .=  $this->_getHelper()->__('The file %s is being generated, please wait the process finish', $fileName);
        } else {
            $html .=  $this->_getHelper()->__('No file found');
        }
        return $html;
    }

    private function _getHelper()
    {
        return Mage::helper('accsvexport');
    }
}