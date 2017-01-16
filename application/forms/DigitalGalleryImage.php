<?php

class App_Form_DigitalGalleryImage extends App_Form_Form
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
     * @return App_Form_DigitalGalleryImage
     */
    public function setRequest(Zend_Controller_Request_Abstract $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * @param App_Service_DigitalGallery $digitalGalleryService
     * @return App_Form_DigitalGalleryImage
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

        $element = new Zend_Form_Element_Text('title');
        $element->setLabel('Title');
        $element->setRequired(true);
        $element->addFilters(array(
            array('StripTags'),
        ));
        $this->addElement($element);

        $element = new Zend_Form_Element_Textarea('keywords');
        $element->setLabel('Keywords');
        $element->setRequired(true);
        $element->setAttribs(array(
            'rows' => 4,
            'cols' => 60,
        ));
        $element->addFilters(array(
            array('StripTags'),
        ));
        $element->setDescription('Comma separated');
        $this->addElement($element);

        $element = new Zend_Form_Element_Textarea('description');
        $element->setLabel('Description');
        $element->setRequired(true);
        $element->setAttribs(array(
            'rows' => 4,
            'cols' => 60,
        ));
        $element->addFilters(array(
            array('StripTags'),
        ));
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('imageNo');
        $element->setLabel('Image No.');
        $element->setRequired(true);
        $element->addFilters(array(
            array('StripTags'),
        ));
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('copyright');
        $element->setLabel('Copyright');
        $element->setRequired(true);
        $element->addFilters(array(
            array('StripTags'),
        ));
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('price');
        $element->setLabel('Price');
        $element->setRequired(true);
        $element->addFilters(array(
            array('StripTags'),
        ));
        $element->setValidators(array(
            array('Float', true),
        ));
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('imageName');
        $element->setLabel('Image');
        $element->setAttrib('disabled', 'disabled');
        $this->addElement($element);

        $element = new Zend_Form_Element_MultiCheckbox('categories');
        $element->setMultiOptions($this->_getCategoryMultiOptions());
        $this->addElement($element);

        $element = new Zend_Form_Element_MultiCheckbox('themes');
        $element->setMultiOptions($this->_getThemeMultiOptions());
        $this->addElement($element);

        $element = new Zend_Form_Element_MultiCheckbox('subjects');
        $element->setMultiOptions($this->_getSubjectMultiOptions());
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
        $filterOptions = $this->_digitalGalleryService->getAdminFilterOptions();

        $multiOptions = array();

        foreach ($filterOptions->categories as $category) {
            $multiOptions[$category->value] = $category->label;
        }

        return $multiOptions;
    }

    /**
     * @return array
     */
    protected function _getThemeMultiOptions()
    {
        $filterOptions = $this->_digitalGalleryService->getAdminFilterOptions();

        $multiOptions = array();

        foreach ($filterOptions->themes as $theme) {
            $multiOptions[$theme->value] = $theme->label;
        }

        return $multiOptions;
    }

    /**
     * @return array
     */
    protected function _getSubjectMultiOptions()
    {
        $filterOptions = $this->_digitalGalleryService->getAdminFilterOptions();

        $multiOptions = array();

        foreach ($filterOptions->subjects as $subject) {
            $multiOptions[$subject->value] = $subject->label;
        }

        return $multiOptions;
    }

    /**
     * @param App_Service_Dto_DigitalGalleryImage $digitalGalleryImage
     * @return App_Form_DigitalGalleryImage
     */
    public function populateFromDigitalGalleryImageDto(
            App_Service_Dto_DigitalGalleryImage $digitalGalleryImage
    )
    {
        $values = (array) $digitalGalleryImage;
        $cats = $values['categories'];

        $values['categories'] = array();
        $values['themes'] = array();
        $values['subjects'] = array();

        foreach ($cats as $cat) {

            switch ($cat->type) {

                case 'category':
                    $values['categories'][] = $cat->id;
                    break;

                case 'theme':
                    $values['themes'][] = $cat->id;
                    break;

                case 'subject':
                    $values['subjects'][] = $cat->id;
                    break;

                default:
                    // No default
            }
        }

        return parent::populate($values);
    }

}
