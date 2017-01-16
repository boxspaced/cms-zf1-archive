<?php

class App_Form_HomePageItemBuilder extends App_Form_AbstractItemBuilder
{

    /**
     * @return Zend_Form_SubForm
     */
    protected function _getFieldsForm()
    {
        $form = new Zend_Form_SubForm();

        return $form;
    }

    /**
     * @return Zend_Form_SubForm
     */
    protected function _getPartFieldsForm()
    {
        $form = new Zend_Form_SubForm();

        return $form;
    }

}
