<?php

class App_Form_HtmlBlockBuilder extends App_Form_AbstractBlockBuilder
{

    /**
     * @return Zend_Form_SubForm
     */
    protected function _getFieldsForm()
    {
        $form = new Zend_Form_SubForm();

        $element = new Zend_Form_Element_Textarea('html');
        $element->setLabel('Html');
        $element->setAttribs(array(
            'rows' => 4,
            'cols' => 60,
        ));
        $element->setAttrib('class', 'wysiwyg');
        $form->addElement($element);

        return $form;
    }

}
