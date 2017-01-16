<?php

class App_Form_WhatsOnSearch extends App_Form_Form
{

    /**
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;

    /**
     * @param Zend_Controller_Request_Abstract $request
     * @return App_Form_WhatsOnSearch
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
        $this->setAction('/whats-on/results');
        $this->setMethod('get');
        $this->setAttrib('accept-charset', 'UTF-8');

        $element = new Zend_Form_Element_Text('q');
        $element->setLabel('Query');
        $this->addElement($element);

        $element = new Zend_Form_Element_Submit('search');
        $element->setLabel('Search');
        $this->addElement($element);

        return parent::init();
    }

}
