<?php
/**
 * Acaldeira_CsvExport
 *
 * @category    Acaldeira
 * @package     Acaldeira_CsvExport
 * @copyright   Copyright (c) 2017 Acaldeira. (http://www.acaldeira.com.br)
 */
class Acaldeira_CsvExport_Block_Adminhtml_Report_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Acaldeira_CsvExport_Block_Adminhtml_Report_Edit constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'accsvexport';
        $this->_controller = 'adminhtml_report';

        $this->_updateButton('save', 'label', __('Save Report'));
        $this->_updateButton('delete', 'label', __('Delete Report'));

    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        if( Mage::registry('report_data') && Mage::registry('report_data')->getId() ) {
            return __("Edit Report '%s'", Mage::registry('report_data')->getName());
        } else {
            return __('Add Report');
        }
    }
}