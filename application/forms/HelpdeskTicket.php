<?php

class App_Form_HelpdeskTicket extends App_Form_Form
{

    /**
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;

    /**
     * @var string
     */
    protected $_attachmentUploadDirectory;

    /**
     * @param Zend_Controller_Request_Abstract $request
     * @return App_Form_HelpdeskTicket
     */
    public function setRequest(Zend_Controller_Request_Abstract $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * @param string $attachmentUploadDirectory
     * @return App_Form_HelpdeskTicket
     */
    public function setAttachmentUploadDirectory($attachmentUploadDirectory)
    {
        $this->_attachmentUploadDirectory = $attachmentUploadDirectory;
        return $this;
    }

    /**
     * @return void
     */
    public function init()
    {
        $this->setMethod('post');
        $this->setAttrib('accept-charset', 'UTF-8');

        $element = new Zend_Form_Element_Hash('token');
        $element->setTimeout(900);
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('subject');
        $element->setLabel('Subject');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Zend_Form_Element_Textarea('issue');
        $element->setLabel('Issue');
        $element->setRequired(true);
        $element->setAttribs(array(
            'rows' => 8,
            'cols' => 90,
        ));
        $element->addFilters(array(
            array('StripTags'),
        ));
        $this->addElement($element);

        $element = new Zend_Form_Element_File('attachment');
        $element->setLabel('Attach an image');
        $element->setDescription('Attach an image e.g. a screen shot to help us see the problem');
        $element->setDestination($this->_attachmentUploadDirectory);
        $element->setValidators(array(
            array('Count', true, 1),
            array('IsImage', true),
        ));
        $this->addElement($element);

        $element = new Zend_Form_Element_Submit('save');
        $element->setLabel('Save');
        $this->addElement($element);

        return parent::init();
    }

}
