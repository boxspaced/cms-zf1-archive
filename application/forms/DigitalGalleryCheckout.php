<?php

class App_Form_DigitalGalleryCheckout extends App_Form_FrontendForm
{

    /**
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;

    /**
     * @param Zend_Controller_Request_Abstract $request
     * @return App_Form_DigitalGalleryCheckout
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
        $this->setAction('/digital-gallery/basket');
        $this->setMethod('post');
        $this->setAttrib('accept-charset', 'UTF-8');

        $element = new Zend_Form_Element_Text('name');
        $element->setLabel('Your Name');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('email');
        $element->setLabel('Your Email Address');
        $element->setRequired(true);
        $element->setValidators(array(
            array('EmailAddress', true),
        ));
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('dayPhone');
        $element->setLabel('Your Daytime Telephone Number');
        $element->setRequired(true);
        $element->setValidators(array(
            array('Digits', true),
        ));
        $this->addElement($element);

        $element = new Zend_Form_Element_Textarea('message');
        $element->setLabel('Message');
        $element->setAttribs(array(
            'rows' => 4,
        ));
        $this->addElement($element);

        return parent::init();
    }

}
