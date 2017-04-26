<?php
/**
 * Acaldeira_CsvExport
 *
 * @category    Acaldeira
 * @package     Acaldeira_CsvExport
 * @copyright   Copyright (c) 2017 Acaldeira. (http://www.Acaldeira.com)
 */
use Acaldeira_CsvExport_Model_Exporter as Exporter;
class Acaldeira_CsvExport_Adminhtml_Csvexport_CsvController extends Mage_Adminhtml_Controller_Action
{

    private $_modeAllowed = array(Exporter::MODE_CUSTOMER, Exporter::MODE_ORDER, Exporter::MODE_CATALOG);

    private function _isModeAllowed($mode) {
        return in_array($mode, $this->_modeAllowed);
    }


    public function downloadAction() {

        $mode = $this->getRequest()->getParam('mode');
        if (!$this->_isModeAllowed($mode))
            return;

        $base = Mage::helper('accsvexport')->getExportDir();
        $fileName = Mage::helper('accsvexport')->getCsvFileName($mode);
        $filePath = $base . DS . $fileName;
        if (! is_file ( $filePath ) || ! is_readable ( $filePath )) {
            throw new Exception ( );
        }
        $this->getResponse ()
            ->setHttpResponseCode ( 200 )
            ->setHeader ( 'Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true )
            ->setHeader ( 'Pragma', 'public', true )
            ->setHeader ( 'Content-type', 'application/force-download' )
            ->setHeader ( 'Content-Length', filesize($filePath) )
            ->setHeader ('Content-Disposition', 'attachment' . '; filename=' . basename($filePath) );
        $this->getResponse ()->clearBody ();
        $this->getResponse ()->sendHeaders ();
        readfile ( $filePath );
        exit;
    }

    public function generateModeAction()
    {
        $mode = $this->getRequest()->getParam('mode');

        try {

            if (!$this->_isModeAllowed($mode))
                Mage::throwException($this->__('Invalid mode %s', $mode));

            $fileName = Mage::helper('accsvexport')->getCsvFileName($mode);
            Mage::getSingleton('adminhtml/session')->addSuccess(__('The file %s will be updated in a few minutes', $fileName));
            Mage::helper('accsvexport')->createMode($mode);
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            return $this->_redirectReferer();
        }

        return $this->_redirectReferer();
    }
}