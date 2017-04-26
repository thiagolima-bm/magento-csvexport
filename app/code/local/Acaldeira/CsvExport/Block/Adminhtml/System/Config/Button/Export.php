<?php
/**
 * Acaldeira_CsvExport
 *
 * @category    Acaldeira
 * @package     Acaldeira_CsvExport
 * @copyright   Copyright (c) 2017 Acaldeira. (http://www.Acaldeira.com)
 */
class Acaldeira_CsvExport_Block_Adminhtml_System_Config_Button_Export
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected $_mode = '';
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {

        $buttonBlock = $element->getForm()->getParent()->getLayout()->createBlock('adminhtml/widget_button');

        $disabled = '';
        if ($this->_getHelper()->isBeingProcessed($this->_mode)) {
            $disabled = 'disabled';
        }

        $data = array(
            'label' => $this->_getHelper()->__('Generate'),
            'onclick' => 'csvExport' . $this->getProcess() . '()',
            'class' => $disabled,
        );

        $url = Mage::helper('adminhtml')->getUrl("adminhtml/csvexport_csv/generatemode", array('mode' => $this->_mode));
        $html = $buttonBlock->setData($data)->toHtml();

        if ($disabled)
            return $html;

        $html .= "

        <script type=\"text/javascript\">
        //<![CDATA[
            function csvExport" . $this->getProcess() . "() {
                var process" . $this->getProcess() . " = '" . $this->getProcess() . "';
                setLocation('{$url}process/' +  encodeURIComponent(process" . $this->getProcess() . "));
            }
        //]] >
        </script>
        ";


        return $html;
    }

    /**
     * @return Acaldeira_CsvExport_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('accsvexport');
    }

    /**
     * @return string
     */
    public function getProcess()
    {
        $arrayClassName = explode('_', get_class($this));
        return strtolower(end($arrayClassName));
    }
}