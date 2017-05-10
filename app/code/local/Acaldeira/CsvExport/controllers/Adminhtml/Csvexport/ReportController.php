<?php
/**
 * Acaldeira_CsvExport
 *
 * @category    Acaldeira
 * @package     Acaldeira_CsvExport
 * @copyright   Copyright (c) 2017 Acaldeira. (http://www.Acaldeira.com)
 */
class Acaldeira_CsvExport_Adminhtml_Csvexport_ReportController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('accsvexport/adminhtml_report'));
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_redirect('*/*/edit');
    }

    public function editAction()
    {
        $this->loadLayout();
        $model = $this->_initObject();
        Mage::register('report_data', $model);
        $this->_addContent($this->getLayout()->createBlock('accsvexport/adminhtml_report_edit'))
            ->_addLeft($this->getLayout()->createBlock('accsvexport/adminhtml_report_edit_tabs'));
        $this->renderLayout();
    }

    public function saveAction()
    {
        $redirectBack = $this->getRequest()->getParam('back', false);
        if ($data = $this->getRequest()->getPost()) {

            $id    = $this->getRequest()->getParam('id');

            $model = $this->_initObject();

            $data = new Varien_Object($data);

            // save model
            try {
                $fields = implode(',', $data->getData('fields'));
                $data->setData('fields', $fields);
                $model->addData($data->getData());
                $model->setId($id);
                $this->_getSession()->setFormData($model->getData());
                $model->save();
                $this->_getSession()->setFormData(false);
                $this->_getSession()->addSuccess(__('The report has been saved.'));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $redirectBack = true;
            } catch (Exception $e) {
                $this->_getSession()->addError(__('Unable to save the report.'));
                $this->_getSession()->addError($e->getMessage());
                $redirectBack = true;
                Mage::logException($e);
            }

            if ($redirectBack) {
                $this->_redirect('*/*/edit', array('id' => $model->getId()));

                return;
            }
        }
        $this->_redirect('*/*/index');
    }

    public function deleteAction() {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                // init model and delete
                $model = Mage::getModel('accsvexport/report');
                $model->load($id);
                if (!$model->getId()) {
                    Mage::throwException(__('Unable to find a report to delete.'));
                }
                $model->delete();
                // display success message
                $this->_getSession()->addSuccess(__('The report has been deleted.'));
                // go to grid
                $this->_redirect('*/*/index');

                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(__('An error occurred while deleting the report. Please review log and try again.'));
                Mage::logException($e);
            }
            // redirect to edit form
            $this->_redirect('*/*/edit', array('id' => $id));

            return;
        }
        // display error message
        $this->_getSession()->addError(__('Unable to find a report to delete.'));
        // go to grid
        $this->_redirect('*/*/index');
    }

    /**
     * @return Acaldeira_CsvExport_Model_Report|void
     */
    private function _initObject()
    {
        $id    = $this->getRequest()->getParam('id');
        $model = Mage::getModel('accsvexport/report');
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->_getSession()->addError(__('This report no longer exists.'));
                $this->_redirect('*/*/index');

                return;
            }
        }
        return $model;
    }

    /**
     *
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('accsvexport/adminhtml_report_grid')->toHtml()
        );
    }


    public function massGenerateAction()
    {
        $reportIds = $this->getRequest()->getParam('entity_id');

        if (!is_array($reportIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('accsvexport')->__('Please select report(es).'));
        } else {

            try {
                $reportModel = Mage::getModel('accsvexport/report');
                foreach ($reportIds as $reportId) {
                    $reportModel->load($reportId)->generate();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('accsvexport')->__(
                        'Total of %d report(s) were generated.', count($reportIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    public function tableFieldsAction()
    {
        $tableName = $this->getRequest()->getParam('view_name');
        $fields = [];
        $actionResult = [];

        if ($tableName) {

            try {
                preg_match('/^[A-Za-z0-9_]+$/', $tableName, $matches);

                if (!$matches) {
                    Mage::throwException('Invalid view name. Only numbers, letters and underscore allowed');
                }
                $query = "SHOW COLUMNS FROM $tableName";
                $resource = Mage::getSingleton('core/resource');
                $readConnection = $resource->getConnection('core_read');
                $result = $readConnection->fetchAll($query);

                foreach ($result as $row) {
                    $fields[] = $row['Field'];
                }

                $actionResult['success'] = true;
                $actionResult['body'] = $fields;

            } catch (Exception $e) {
                $actionResult['error'] = $e->getMessage();
            }

        }
        $this->getResponse()->setBody(json_encode($actionResult));
    }

}