<?php
/**
 * Acaldeira_CsvExport
 *
 * @category    Acaldeira
 * @package     Acaldeira_CsvExport
 * @copyright   Copyright (c) 2017 Acaldeira. (http://www.Acaldeira.com)
 *
 * @method String getViewName()
 * @method String getName()
 */
class Acaldeira_CsvExport_Model_Report extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('accsvexport/report');
    }

    public function _beforeSave()
    {
        preg_match('/^[A-Za-z0-9_]+$/', $this->getViewName(), $matches);
        
        if (!$matches) {
            Mage::throwException('Invalid view name. Only numbers, letters and underscore allowed');
        }

    }
}
