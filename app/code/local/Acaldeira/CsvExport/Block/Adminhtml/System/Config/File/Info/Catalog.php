<?php
/**
 * Acaldeira_CsvExport
 *
 * @category    Acaldeira
 * @package     Acaldeira_CsvExport
 * @copyright   Copyright (c) 2017 Acaldeira. (http://www.Acaldeira.com)
 */
use Acaldeira_CsvExport_Model_Exporter as Exporter;

class Acaldeira_CsvExport_Block_Adminhtml_System_Config_File_Info_Catalog extends Acaldeira_CsvExport_Block_Adminhtml_System_Config_File_Info
{
    protected $_mode = Exporter::MODE_CATALOG;

}