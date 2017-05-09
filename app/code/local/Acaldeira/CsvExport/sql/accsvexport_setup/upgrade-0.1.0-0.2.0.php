<?php
/**
 * Acaldeira_CsvExport
 *
 * @category    Acaldeira
 * @package     Acaldeira_CsvExport
 * @copyright   Copyright (c) 2017 Acaldeira. (http://www.acaldeira.com.br)
 */

$installer = $this;

use Acaldeira_Franchise_Model_Franchise as Franchise;

$installer->getConnection()
    ->addColumn($installer->getTable('cron/schedule'), 'custom_data', array(
        'TYPE'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'LENGTH'    => 255,
        'NULLABLE'  => true,
        'COMMENT'   => 'Custom Data'
    ));