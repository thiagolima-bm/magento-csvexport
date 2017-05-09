<?php
/**
 * Acaldeira_CsvExport
 *
 * @category    Acaldeira
 * @package     Acaldeira_CsvExport
 * @copyright   Copyright (c) 2017 Acaldeira. (http://www.acaldeira.com.br)
 */


$installer = $this;
$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `{$this->getTable('accsvexport/report')}`;
CREATE TABLE `{$this->getTable('accsvexport/report')}` (
    `entity_id`       int(11)      NOT NULL AUTO_INCREMENT,
    `name`            varchar(255) NULL,
    `view_name`       varchar(255) NULL,
    `cron_expr`       varchar(255) NULL,
    `description`     text         NOT NULL,
    `is_active`       int(1)       NOT NULL DEFAULT '0',
    `created_at`      datetime     DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
