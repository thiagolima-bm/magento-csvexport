<?php
/**
 * Acaldeira_CsvExport
 *
 * @category    Acaldeira
 * @package     Acaldeira_CsvExport
 * @copyright   Copyright (c) 2017 Interactiv4. (http://www.acaldeira.com.br)
 */
class Acaldeira_CsvExport_Block_Adminhtml_Report_Edit_Tab_Button_Loadfields extends Mage_Core_Block_Template
{
    public function _toHtml()
    {

        $buttonBlock = $this->getLayout()->createBlock('adminhtml/widget_button');

        $data = array(
            'label' => $this->__('Load Fields'),
            'onclick' => 'loadFields()',
        );

        $url = Mage::helper('adminhtml')->getUrl("adminhtml/csvexport_csv/generatemode");
        $html = $buttonBlock->setData($data)->toHtml();

        $urlAjaxLabels = Mage::helper('adminhtml')->getUrl("adminhtml/csvexport_report/tableFields");

        $html .= "

        <script type=\"text/javascript\">
        //<![CDATA[
            function loadFields() {
            
                var view_name = $('view_name').getValue();
                var select = $('table_fields');
                var url = '$urlAjaxLabels';
                new Ajax.Request(url, {
                    method: 'get',
                    parameters: {view_name: view_name},
                    onSuccess: function(response) {
                        var json = response.responseText.evalJSON(true);
                        
                        if (json.success == true) {
                            select.update('');   
                            json.body.forEach(function(element) {
                                console.log(element);
                                select.insert(new Element('option', {value: element}).update(element));
                            });
                        }   
                    }
                });
            }
        //]] >
        </script>
        ";

        return $html;
    }
}