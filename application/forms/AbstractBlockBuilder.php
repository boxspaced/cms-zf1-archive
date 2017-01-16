<?php

abstract class App_Form_AbstractBlockBuilder extends App_Form_Form
{

    /**
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;

    /**
     * @return Zend_Form_SubForm
     */
    abstract protected function _getFieldsForm();

    /**
     * @param Zend_Controller_Request_Abstract $request
     * @return App_Form_AbstractBlockBuilder
     */
    public function setRequest(Zend_Controller_Request_Abstract $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * @return void
     */
    public function init()
    {
        $this->setAction('/block/edit');
        $this->setAttrib('name', 'main');
        $this->setMethod('post');
        $this->setAttrib('accept-charset', 'UTF-8');

        $element = new Zend_Form_Element_Hidden('from');
        $element->setValue($this->_request->getParam('from'));
        $this->addElement($element);

        $element = new Zend_Form_Element_Hidden('id');
        $element->setValue($this->_request->getParam('id'));
        $element->setRequired(true);
        $element->addFilters(array(
            array('Int'),
        ));
        $this->addElement($element);

        $element = new Zend_Form_Element_Hidden('partial');
        $element->setValue('0');
        $element->addFilters(array(
            array('Int'),
        ));
        $this->addElement($element);

        $element = new Zend_Form_Element_Hash('token');
        $element->setTimeout(900);
        $this->addElement($element);

        // Fields
        $fieldsForm = $this->_getFieldsForm();
        $this->addSubForm($fieldsForm, 'fields');

        $element = new Zend_Form_Element_Textarea('note');
        $element->setLabel('Add a note');
        $element->setAttribs(array(
            'rows' => 4,
            'cols' => 60,
        ));
        $element->addFilters(array(
            array('StripTags'),
        ));
        $this->addElement($element);

        $element = new Zend_Form_Element_Submit('save');
        $element->setLabel('Save');
        $this->addElement($element);

        $element = new Zend_Form_Element_Submit('publish');
        $element->setLabel('Publish');
        $this->addElement($element);

        return parent::init();
    }

    /**
     * @param App_Service_Dto_Block $block
     * @return App_Form_AbstractBlockBuilder
     */
    public function populateFromBlockDto(App_Service_Dto_Block $block)
    {
        $values = (array) $block;

        $fields = $values['fields'];

        $values['fields'] = array();

        foreach ($fields as $field) {
            $values['fields'][$field->name] = $field->value;
        }

        return parent::populate($values);
    }

}
