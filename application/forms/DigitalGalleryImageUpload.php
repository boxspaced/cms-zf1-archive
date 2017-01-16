<?php

class App_Form_DigitalGalleryImageUpload extends App_Form_DigitalGalleryImage
{

    /**
     * @var string
     */
    protected $_privateDirectory;

    /**
     * @param string $privateDirectory
     * @return App_Form_DigitalGalleryImageUpload
     */
    public function setPrivateDirectory($privateDirectory)
    {
        $this->_privateDirectory = $privateDirectory;
        return $this;
    }

    /**
     * @return void
     */
    public function init()
    {
        $element = new Zend_Form_Element_File('image');
        $element->setLabel('Image');
        $element->setRequired(true);
        $element->setDescription('High resolution version (max 6MB and JPEG\'s only)');
        $element->setDestination($this->_privateDirectory);
        $element->setValidators(array(
            array('Count', true, 1),
            array('IsImage', true, 'jpeg'),
            array('Size', true, 6291456), // 6MB
        ));
        $this->addElement($element);

        $element = new Zend_Form_Element_Submit('upload');
        $element->setLabel('Upload');
        $this->addElement($element);

        return parent::init();
    }

}
