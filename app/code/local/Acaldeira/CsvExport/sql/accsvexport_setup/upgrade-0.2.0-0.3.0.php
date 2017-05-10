<?php
/**
 * Acaldeira_CsvExport
 *
 * @category    Acaldeira
 * @package     Acaldeira_CsvExport
 * @copyright   Copyright (c) 2017 Acaldeira. (http://www.acaldeira.com.br)
 */

$installer = $this;

$installer->getConnection()
    ->addColumn($installer->getTable('accsvexport/report'), 'fields', array(
        'TYPE'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'LENGTH'    => Varien_Db_Ddl_Table::DEFAULT_TEXT_SIZE,
        'NULLABLE'  => true,
        'COMMENT'   => 'Fields'
    ));