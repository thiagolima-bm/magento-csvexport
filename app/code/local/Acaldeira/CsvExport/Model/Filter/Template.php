<?php
/**
 * Acaldeira_CsvExport
 *
 * @category    Acaldeira
 * @package     Acaldeira_CsvExport
 * @copyright   Copyright (c) 2017 Acaldeira. (http://www.Acaldeira.com)
 */

class Acaldeira_CsvExport_Model_Filter_Template implements Zend_Filter_Interface
{
    /**
     * Cunstruction regular expression
     */
    const CONSTRUCTION_PATTERN = '/{{([a-z]{0,10})(.*?)}}/si';

    /**
     * Cunstruction logic regular expression
     */
    const CONSTRUCTION_DEPEND_PATTERN = '/{{depend\s*(.*?)}}(.*?){{\\/depend\s*}}/si';
    const CONSTRUCTION_IF_PATTERN = '/{{if\s*(.*?)}}(.*?)({{else}}(.*?))?{{\\/if\s*}}/si';

    /**
     * Looping regular expression
     */
    const LOOP_PATTERN = '/{{for(?P<loopItem>.*? )(in)(?P<loopData>.*?)}}(?P<loopBody>.*?){{\/for}}/si';

    /**
     * Assigned template variables
     *
     * @var array
     */
    protected $_templateVars = array();

    /**
     * Template processor
     *
     * @var array|string|null
     */
    protected $_templateProcessor = null;

    /**
     * Include processor
     *
     * @var array|string|null
     */
    protected $_includeProcessor = null;

    /**
     * Sets template variables that's can be called througth {var ...} statement
     *
     * @param array $variables
     */
    public function setVariables(array $variables)
    {
        foreach ($variables as $name => $value) {
            $this->_templateVars[$name] = $value;
        }
        return $this;
    }

    /**
     * Sets the proccessor of templates. Templates are directives that include email templates based on system
     * configuration path.
     *
     * @param array $callback it must return string
     */
    public function setTemplateProcessor(array $callback)
    {
        $this->_templateProcessor = $callback;
        return $this;
    }

    /**
     * Sets the proccessor of templates.
     *
     * @return array|null
     */
    public function getTemplateProcessor()
    {
        return is_callable($this->_templateProcessor) ? $this->_templateProcessor : null;
    }

    /**
     * Sets the proccessor of includes.
     *
     * @param array $callback it must return string
     */
    public function setIncludeProcessor(array $callback)
    {
        $this->_includeProcessor = $callback;
        return $this;
    }

    /**
     * Sets the proccessor of includes.
     *
     * @return array|null
     */
    public function getIncludeProcessor()
    {
        return is_callable($this->_includeProcessor) ? $this->_includeProcessor : null;
    }

    /**
     * Filter the string as template.
     *
     * @param string $value
     * @return string
     */
    public function filter($value)
    {
        // "depend" and "if" operands should be first
        foreach (array(
                     self::CONSTRUCTION_DEPEND_PATTERN => 'dependDirective',
                     self::CONSTRUCTION_IF_PATTERN => 'ifDirective',
                 ) as $pattern => $directive) {
            if (preg_match_all($pattern, $value, $constructions, PREG_SET_ORDER)) {
                foreach ($constructions as $index => $construction) {
                    $replacedValue = '';
                    $callback = array($this, $directive);
                    if (!is_callable($callback)) {
                        continue;
                    }
                    try {
                        $replacedValue = call_user_func($callback, $construction);
                    } catch (Exception $e) {
                        throw $e;
                    }
                    $value = str_replace($construction[0], $replacedValue, $value);
                }
            }
        }

        $value = $this->filterFor($value);

        if (preg_match_all(self::CONSTRUCTION_PATTERN, $value, $constructions, PREG_SET_ORDER)) {
            foreach ($constructions as $index => $construction) {
                $replacedValue = '';
                $callback = array($this, $construction[1] . 'Directive');
                if (!is_callable($callback)) {
                    continue;
                }
                try {
                    $replacedValue = call_user_func($callback, $construction);
                } catch (Exception $e) {
                    throw $e;
                }
                $value = str_replace($construction[0], $replacedValue, $value);
            }
        }
        return $value;
    }

    /**
     * Filter the string as template.
     *
     * @param string $value
     * @example syntax {{for item in order.all_visible_items}} sku: {{var item.sku}}<br>name: {{var item.name}}<br> {{/for}} order items collection.
     * @example syntax {{for thing in things}} {{var thing.whatever}} {{/for}} e.g.:custom collection.
     * @return string
     */
    protected function filterFor($value)
    {
        if (preg_match_all(self::LOOP_PATTERN, $value, $constructions, PREG_SET_ORDER)) {
            foreach ($constructions as $construction) {
                if (!$this->isValidLoop($construction)) {
                    return $value;
                }
                $fullTextToReplace = $construction[0];
                $loopData = $this->_getVariable($construction['loopData'], '');
                $loopTextToReplace = $construction['loopBody'];
                $loopItem = preg_replace('/\s+/', '', $construction['loopItem']);
                if (is_array($loopData) || $loopData instanceof Varien_Data_Collection) {
                    $loopText = [];
                    $indexCount = 0;
                    $loop = new Varien_Object;
                    foreach ($loopData as $objectData) {
                        if (!$objectData instanceof Varien_Object) { // is array?
                            if (!is_array($objectData)) {
                                continue;
                            }
                            $_item = new Varien_Object;
                            $_item->setData($objectData);
                            $objectData = $_item;
                        }
                        $loop->setData('index', $indexCount++);
                        $this->_templateVars['loop'] = $loop;
                        $this->_templateVars[$loopItem] = $objectData;
                        if (preg_match_all(self::CONSTRUCTION_PATTERN, $loopTextToReplace, $attributes,
                            PREG_SET_ORDER)) {
                            $subText = $loopTextToReplace;
                            foreach ($attributes as $attribute) {
                                $text = $this->_getVariable($attribute[2], '');
                                $subText = str_replace($attribute[0], $text, $subText);
                            }
                            $loopText[] = $subText;
                        }
                        unset($this->_templateVars[$loopItem]);
                    }
                    $replaceText = implode('', $loopText);
                    $value = str_replace($fullTextToReplace, $replaceText, $value);
                }
            }
        }
        return $value;
    }

    /**
     * @param $construction
     * @return bool
     */
    private function isValidLoop($construction)
    {
        if ((strlen(trim($construction['loopBody'])) != 0) &&
            (strlen(trim($construction['loopItem'])) != 0) &&
            (strlen(trim($construction['loopData'])) != 0)
        ) {
            return true;
        }
        return false;
    }

    public function varDirective($construction)
    {
        if (count($this->_templateVars) == 0) {
            // If template preprocessing
            return $construction[0];
        }

        $replacedValue = $this->_getVariable($construction[2], '');
        return $replacedValue;
    }

    public function includeDirective($construction)
    {
        // Processing of {include template=... [...]} statement
        $includeParameters = $this->_getIncludeParameters($construction[2]);
        if (!isset($includeParameters['template']) or !$this->getIncludeProcessor()) {
            // Not specified template or not seted include processor
            $replacedValue = '{Error in include processing}';
        } else {
            // Including of template
            $templateCode = $includeParameters['template'];
            unset($includeParameters['template']);
            $includeParameters = array_merge_recursive($includeParameters, $this->_templateVars);
            $replacedValue = call_user_func($this->getIncludeProcessor(), $templateCode, $includeParameters);
        }
        return $replacedValue;
    }

    /**
     * This directive allows email templates to be included inside other email templates using the following syntax:
     * {{template config_path="<PATH>"}}, where <PATH> equals the XPATH to the system configuration value that contains
     * the value of the email template. For example "sales_email/order/template", which is stored in the
     * Mage_Sales_Model_Order::sales_email/order/template. This directive is useful to include things like a global
     * header/footer.
     *
     * @param $construction
     * @return mixed|string
     */
    public function templateDirective($construction)
    {
        // Processing of {template config_path=... [...]} statement
        $templateParameters = $this->_getIncludeParameters($construction[2]);
        if (!isset($templateParameters['config_path']) or !$this->getTemplateProcessor()) {
            $replacedValue = '{Error in template processing}';
        } else {
            // Including of template
            $configPath = $templateParameters['config_path'];
            unset($templateParameters['config_path']);
            $templateParameters = array_merge_recursive($templateParameters, $this->_templateVars);
            $replacedValue = call_user_func($this->getTemplateProcessor(), $configPath, $templateParameters);
        }
        return $replacedValue;
    }

    public function dependDirective($construction)
    {
        if (count($this->_templateVars) == 0) {
            // If template preprocessing
            return $construction[0];
        }

        if ($this->_getVariable($construction[1], '') == '') {
            return '';
        } else {
            return $construction[2];
        }
    }

    public function ifDirective($construction)
    {
        if (count($this->_templateVars) == 0) {
            return $construction[0];
        }

        if ($this->_getVariable($construction[1], '') == '') {
            if (isset($construction[3]) && isset($construction[4])) {
                return $construction[4];
            }
            return '';
        } else {
            return $construction[2];
        }
    }

    /**
     * Return associative array of include construction.
     *
     * @param string $value raw parameters
     * @return array
     */
    protected function _getIncludeParameters($value)
    {
        $tokenizer = new Varien_Filter_Template_Tokenizer_Parameter();
        $tokenizer->setString($value);
        $params = $tokenizer->tokenize();
        foreach ($params as $key => $value) {
            if (substr($value, 0, 1) === '$') {
                $params[$key] = $this->_getVariable(substr($value, 1), null);
            }
        }
        return $params;
    }

    /**
     * Return variable value for var construction
     *
     * @param string $value raw parameters
     * @param string $default default value
     * @return string
     */
    protected function _getVariable($value, $default = '{no_value_defined}')
    {
        Varien_Profiler::start("email_template_proccessing_variables");
        $tokenizer = new Varien_Filter_Template_Tokenizer_Variable();
        $tokenizer->setString($value);
        $stackVars = $tokenizer->tokenize();
        $result = $default;
        $last = 0;
        for ($i = 0; $i < count($stackVars); $i++) {
            if ($i == 0 && isset($this->_templateVars[$stackVars[$i]['name']])) {
                // Getting of template value
                $stackVars[$i]['variable'] =& $this->_templateVars[$stackVars[$i]['name']];
            } elseif (isset($stackVars[$i - 1]['variable']) && $stackVars[$i - 1]['variable'] instanceof Varien_Object) {
                // If object calling methods or getting properties
                if ($stackVars[$i]['type'] == 'property') {
                    $caller = 'get' . uc_words($stackVars[$i]['name'], '');
                    $stackVars[$i]['variable'] = method_exists($stackVars[$i - 1]['variable'], $caller)
                        ? $stackVars[$i - 1]['variable']->$caller()
                        : $stackVars[$i - 1]['variable']->getData($stackVars[$i]['name']);
                } elseif ($stackVars[$i]['type'] == 'method') {
                    // Calling of object method
                    if (method_exists($stackVars[$i - 1]['variable'], $stackVars[$i]['name'])
                        || substr($stackVars[$i]['name'], 0, 3) == 'get'
                    ) {
                        $stackVars[$i]['variable'] = call_user_func_array(
                            array($stackVars[$i - 1]['variable'], $stackVars[$i]['name']),
                            $stackVars[$i]['args']
                        );
                    }
                }
                $last = $i;
            }
        }

        if (isset($stackVars[$last]['variable'])) {
            // If value for construction exists set it
            $result = $stackVars[$last]['variable'];
        }
        Varien_Profiler::stop("email_template_proccessing_variables");
        return $result;
    }

}