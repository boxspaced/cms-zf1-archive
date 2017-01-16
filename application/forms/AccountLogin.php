<?php

class App_Form_AccountLogin extends App_Form_Form
{

    /**
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;

    /**
     * @param Zend_Controller_Request_Abstract $request
     * @return \App_Form_AccountLogin
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
        $this->setAction('/account/login');
        $this->setMethod('post');
        $this->setAttrib('accept-charset', 'UTF-8');

        $element = new Zend_Form_Element_Hidden('requestUri');
        $element->setValue($this->_request->getRequestUri());
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('username');
        $element->setLabel('Username');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Zend_Form_Element_Password('password');
        $element->setLabel('Password');
        $element->setRequired(true);
        $element->setRenderPassword(true);
        $this->addElement($element);

        $element = new Zend_Form_Element_Submit('login');
        $element->setLabel('Login');
        $this->addElement($element);

        return parent::init();
    }

}
