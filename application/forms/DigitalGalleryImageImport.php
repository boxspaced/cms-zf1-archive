<?php

class App_Form_DigitalGalleryImageImport extends App_Form_Form
{

    /**
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;

    /**
     * @var string
     */
    protected $_imagesUploadDirectory;

    /**
     * @param Zend_Controller_Request_Abstract $request
     * @return App_Form_DigitalGalleryImageImport
     */
    public function setRequest(Zend_Controller_Request_Abstract $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * @param string $imagesUploadDirectory
     * @return App_Form_DigitalGalleryImageImport
     */
    public function setImagesUploadDirectory($imagesUploadDirectory)
    {
        $this->_imagesUploadDirectory = $imagesUploadDirectory;
        return $this;
    }

    /**
     * @return void
     */
    public function init()
    {
        $this->setAction('/digital-gallery/import');
        $this->setMethod('post');
        $this->setAttrib('accept-charset', 'UTF-8');

        $element = new Zend_Form_Element_Hash('token');
        $element->setTimeout(900);
        $this->addElement($element);

        $element = new Zend_Form_Element_File('images');
        $element->setLabel('Images');
        $element->setRequired(true);
        $element->setDescription('A Zip file containing images to import, high resolution versions (max 600MB and JPEG\'s only)');
        $element->setDestination($this->_imagesUploadDirectory);
        $element->setValidators(array(
            array('Count', true, 1),
            array('IsCompressed', true, 'zip'),
            array('Size', true, 629145600), // 600MB
        ));
        $this->addElement($element);

        $element = new ZendExt_Form_Element_Document('csv');
        $element->setLabel('Csv');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Zend_Form_Element_Submit('import');
        $element->setLabel('Import');
        $this->addElement($element);

        return parent::init();
    }

}
