<?php
/**
 * Acaldeira_CsvExport
 *
 * @category    Acaldeira
 * @package     Acaldeira_CsvExport
 * @copyright   Copyright (c) 2017 Acaldeira. (http://www.acaldeira.com.br)
 */
class Acaldeira_CsvExport_Block_Adminhtml_Report_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>$this->_getHelper()->__('General')));

        $fieldset->addField('name', 'text', array(
            'label'     => $this->_getHelper()->__('CSV Name'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'name',
        ));

        $fieldset->addField('view_name', 'text', array(
            'label'     => $this->_getHelper()->__('View Name'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'view_name',
            'note'      => 'name of the view or table in database',
        ));

        $block = $this->getLayout()->createBlock('accsvexport/adminhtml_report_edit_tab_button_loadfields');

        $tableFieldOptions = [];
        if ($data = Mage::registry('report_data')->getData()) {
            foreach (explode(',', $data['fields']) as $value) {
                $tableFieldOptions[] = array(
                    'value' => $value,
                    'label' => $value
                );
            }
        }

        $fieldset->addField('table_fields', 'multiselect', array(
            'label'     => $this->_getHelper()->__('Fields'),
            'name'      => 'fields',
            'after_element_html' => $block->toHtml(),
            'values' => $tableFieldOptions,
        ));

        $fieldset->addField('cron_expr', 'text', array(
            'label'     => $this->_getHelper()->__('Cron Expr'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'cron_expr',
            'note'      => 'e.g.: 0 0 * * *',
        ));

        $fieldset->addField('description', 'textarea', array(
            'label'     => $this->_getHelper()->__('Description'),
            'name'      => 'description',
        ));

        $fieldset->addField('is_active', 'select', array(
            'label'     => $this->_getHelper()->__('Is active'),
            'title'     => $this->_getHelper()->__('Is active'),
            'name'      => 'is_active',
            'options'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray()
        ));

        if ($data = Mage::registry('report_data')) {
            $data['table_fields'] = explode(',', $data['fields']);
            $form->setValues($data);
        }

        return parent::_prepareForm();
    }

    /**
     * @return Acaldeira_CsvExport_Helper_Data
     */
    private function _getHelper()
    {
        return Mage::helper('accsvexport');
    }

}