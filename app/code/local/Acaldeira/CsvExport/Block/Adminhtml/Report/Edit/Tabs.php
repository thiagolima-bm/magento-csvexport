<?php
/**
 * Acaldeira_CsvExport
 *
 * @category    Acaldeira
 * @package     Acaldeira_CsvExport
 * @copyright   Copyright (c) 2017 Acaldeira. (http://www.acaldeira.com.br)
 */
class Acaldeira_CsvExport_Block_Adminhtml_Report_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Acaldeira_CsvExport_Block_Adminhtml_Report_Edit_Tabs constructor.
     * @param array $args
     */
    public function __construct(array $args)
    {
        parent::__construct($args);
        $this->setId('report_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Report'));
    }

    /**
     * @return Mage_Core_Block_Abstract
     * @throws Exception
     */
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => __('Report Info'),
            'title'     => __('Report Info'),
            'content'   => $this->getLayout()->createBlock('accsvexport/adminhtml_report_edit_tab_form')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }

}