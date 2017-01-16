<?php

class App_Form_LandingPageItemBuilder extends App_Form_AbstractItemBuilder
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

        $element = new Zend_Form_Element_Textarea('leftColumnBody');
        $element->setLabel('Left column');
        $element->setAttribs(array(
            'rows' => 4,
            'cols' => 60,
        ));
        $element->setAttrib('class', 'wysiwyg');
        $form->addElement($element);

        $element = new Zend_Form_Element_Textarea('centreColumnBody');
        $element->setLabel('Centre column');
        $element->setAttribs(array(
            'rows' => 4,
            'cols' => 60,
        ));
        $element->setAttrib('class', 'wysiwyg');
        $form->addElement($element);

        return $form;
    }

}
