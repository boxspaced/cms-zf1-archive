<?php

class App_Form_Form extends Zend_Form
{

    /**
     * @return void
     */
    public function init()
    {
        $this->setElementFilters(array('StringTrim'));
        return parent::init();
    }

    /**
     * @param array $multiOptions
     * @param string $key
     * @param string $value
     * @return array
     */
    protected function _prependEmptyMultiOption(array $multiOptions, $key = '', $value = '')
    {
        return array($key => $value) + $multiOptions;
    }

}
