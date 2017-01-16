<?php

class App_Form_CourseImport extends App_Form_Form
{

    /**
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;

    /**
     * @var string
     */
    protected $_uploadDirectory;

    /**
     * @param Zend_Controller_Request_Abstract $request
     * @return App_Form_CourseImport
     */
    public function setRequest(Zend_Controller_Request_Abstract $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * @param string $uploadDirectory
     * @return App_Form_CourseImport
     */
    public function setUploadDirectory($uploadDirectory)
    {
        $this->_uploadDirectory = $uploadDirectory;
        return $this;
    }

    /**
     * @return void
     */
    public function init()
    {
        $this->setAction('/course/import');
        $this->setMethod('post');
        $this->setAttrib('accept-charset', 'UTF-8');

        $element = new Zend_Form_Element_Hash('token');
        $element->setTimeout(900);
        $this->addElement($element);

        $element = new ZendExt_Form_Element_Document('file');
        $element->setLabel('Csv');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Zend_Form_Element_Submit('import');
        $element->setLabel('Import');
        $this->addElement($element);

        return parent::init();
    }

}
