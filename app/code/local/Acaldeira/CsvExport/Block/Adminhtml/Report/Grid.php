<?php
/**
 * Acaldeira_CsvExport
 *
 * @category    Acaldeira
 * @package     Acaldeira_CsvExport
 * @copyright   Copyright (c) 2017 Acaldeira. (http://www.acaldeira.com.br)
 */
class Acaldeira_CsvExport_Block_Adminhtml_Report_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Acaldeira_CsvExport_Block_Adminhtml_Report_Grid constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->setId('reportmanagerGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare grid collection object
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('accsvexport/report')->getCollection();
        $this->setCollection($collection);
        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => __('ID'),
            'index'     => 'entity_id',
            'type'      => 'int'
        ));

        $this->addColumn('name', array(
            'header'    => __('CSV Name'),
            'index'     => 'name',
            'type'      => 'text'
        ));

        $this->addColumn('view_name', array(
            'header'    => __('View Name'),
            'index'     => 'view_name',
            'type'      => 'text'
        ));

        $this->addColumn('cron_expr', array(
            'header'    => __('Cron Expression'),
            'index'     => 'cron_expr',
            'type'      => 'text'
        ));

        $this->addColumn('description', array(
            'header'    => __('Description'),
            'index'     => 'description',
            'type'      => 'text'
        ));

        $this->addColumn('is_active', array(
            'header'    => __('Is Active'),
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray()
        ));

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    /**
     * @param $row
     * @return string
     * @throws Exception
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
                'store'=>$this->getRequest()->getParam('store'),
                'id'=>$row->getId())
        );
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('generate_report');
        $this->getMassactionBlock()->setFormFieldName('entity_id');

        $this->getMassactionBlock()
            ->addItem('generate_report', array(
            'label'=> Mage::helper('accsvexport')->__('Generate Report'),
            'url'  => $this->getUrl('*/*/massGenerate'),
            'confirm' => Mage::helper('accsvexport')->__('Are you sure?')
        ));

        return $this;
    }

}