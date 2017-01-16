<?php

class App_Form_DigitalGalleryCategory extends App_Form_Form
{

    /**
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;

    /**
     * @var App_Service_DigitalGallery
     */
    protected $_digitalGalleryService;

    /**
     * @param Zend_Controller_Request_Abstract $request
     * @return App_Form_DigitalGalleryCategory
     */
    public function setRequest(Zend_Controller_Request_Abstract $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * @param App_Service_DigitalGallery $digitalGalleryService
     * @return App_Form_DigitalGalleryCategory
     */
    public function setDigitalGalleryService(App_Service_DigitalGallery $digitalGalleryService)
    {
        $this->_digitalGalleryService = $digitalGalleryService;
        return $this;
    }

    /**
     * @return void
     */
    public function init()
    {
        $this->setMethod('post');
        $this->setAttrib('accept-charset', 'UTF-8');

        $element = new Zend_Form_Element_Hidden('id');
        $element->setValue($this->_request->getParam('id'));
        $element->setRequired(true);
        $element->addFilters(array(
            array('Int'),
        ));
        $this->addElement($element);

        $element = new Zend_Form_Element_Hash('token');
        $element->setTimeout(900);
        $this->addElement($element);

        $element = new Zend_Form_Element_Select('type');
        $element->setLabel('Type');
        $element->setMultiOptions(array('' => '') + $this->_getCategoryMultiOptions());
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('text');
        $element->setLabel('Text');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Zend_Form_Element_Submit('save');
        $element->setLabel('Save');
        $this->addElement($element);

        return parent::init();
    }

    /**
     * @return array
     */
    protected function _getCategoryMultiOptions()
    {
        $categoryTypes = $this->_digitalGalleryService->getCategoryTypes();

        $multiOptions = array();

        foreach ($categoryTypes as $value => $label) {
            $multiOptions[$value] = $label;
        }

        return $multiOptions;
    }

    /**
     * @param App_Service_Dto_DigitalGalleryCategory $digitalGalleryCategory
     * @return App_Form_DigitalGalleryCategory
     */
    public function populateFromDigitalGalleryCategoryDto(
            App_Service_Dto_DigitalGalleryCategory $digitalGalleryCategory
    )
    {
        $values = (array) $digitalGalleryCategory;
        return parent::populate($values);
    }

}
