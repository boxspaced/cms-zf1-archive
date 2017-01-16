<?php

class App_Form_ImageMediumBlockBuilder extends App_Form_AbstractBlockBuilder
{

    /**
     * @return Zend_Form_SubForm
     */
    protected function _getFieldsForm()
    {
        $form = new Zend_Form_SubForm();

        $element = new ZendExt_Form_Element_Image('src');
        $element->setLabel('Image');
        $element->setDescription('Max width 280px');
        $element->setRequired(true);
        $element->addFilters(array(
            array('StripTags'),
        ));
        $form->addElement($element);

        $element = new Zend_Form_Element_Text('alt');
        $element->setLabel('Alternative text');
        $element->addFilters(array(
            array('StripTags'),
        ));
        $form->addElement($element);

        return $form;
    }

}
