<?php

class App_Form_Confirm extends App_Form_Form
{

    /**
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;

    /**
     * @param Zend_Controller_Request_Abstract $request
     * @return App_Form_Confirm
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
        $this->setMethod('post');
        $this->setAttrib('accept-charset', 'UTF-8');

        $element = new Zend_Form_Element_Hidden('from');
        $element->setValue($this->_request->getParam('from'));
        $this->addElement($element);

        $element = new Zend_Form_Element_Hidden('moduleName');
        $element->setValue($this->_request->getParam('moduleName'));
        $this->addElement($element);

        $element = new Zend_Form_Element_Hidden('id');
        $element->setValue($this->_request->getParam('id'));
        $element->addFilters(array(
            array('Int'),
        ));
        $this->addElement($element);

        $element = new Zend_Form_Element_Hash('token');
        $element->setTimeout(900);
        $this->addElement($element);

        $element = new Zend_Form_Element_Submit('confirm');
        $element->setLabel('Confirm');
        $this->addElement($element);

        return parent::init();
    }

}
