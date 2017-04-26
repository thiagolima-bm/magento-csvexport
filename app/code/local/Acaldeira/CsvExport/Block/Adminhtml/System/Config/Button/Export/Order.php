<?php
/**
 * Acaldeira_CsvExport
 *
 * @category    Acaldeira
 * @package     Acaldeira_CsvExport
 * @copyright   Copyright (c) 2017 Acaldeira. (http://www.Acaldeira.com)
 */
use Acaldeira_CsvExport_Model_Exporter as Exporter;

class Acaldeira_CsvExport_Block_Adminhtml_System_Config_Button_Export_Order
    extends Acaldeira_CsvExport_Block_Adminhtml_System_Config_Button_Export
{

    protected $_mode = Exporter::MODE_ORDER;
}