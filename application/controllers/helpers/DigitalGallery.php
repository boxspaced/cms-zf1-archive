<?php

class Controller_Helper_DigitalGallery extends Zend_Controller_Action_Helper_Abstract
{

    /**
     * @var App_Service_DigitalGallery
     */
    protected $_digitalGalleryService;

    /**
     * @var Zend_Config
     */
    protected $_config;

    /**
     * @return void
     */
    public function init()
    {
        $front = Zend_Controller_Front::getInstance();
        $bootstrap = $front->getParam('bootstrap');
        $container = $bootstrap->getContainer();

        $this->_config = $bootstrap->getResource('config');
        $this->_digitalGalleryService = $container['DigitalGalleryService'];
    }

    /**
     * @todo rename processImageValues
     * @param array $values
     * @return void
     */
    public function processImage(array $values)
    {
        // Resize, watermarks etc.
        $this->_digitalGalleryService->processImage($values['image']);

        // Save
        $image = new App_Service_Dto_DigitalGalleryImage();
        $image->title = $values['title'];
        $image->keywords = $values['keywords'];
        $image->description = $values['description'];
        $image->imageNo = $values['imageNo'];
        $image->copyright = $values['copyright'];
        $image->price = $values['price'];
        $image->imageName = $values['image'];

        foreach ($values['categories'] as $category) {
            $categoryDto = new App_Service_Dto_DigitalGalleryCategory();
            $categoryDto->id = $category;
            $categoryDto->type = 'category';
            $image->categories[] = $categoryDto;
        }

        foreach ($values['themes'] as $theme) {
            $categoryDto = new App_Service_Dto_DigitalGalleryCategory();
            $categoryDto->id = $theme;
            $categoryDto->type = 'theme';
            $image->categories[] = $categoryDto;
        }

        foreach ($values['subjects'] as $subject) {
            $categoryDto = new App_Service_Dto_DigitalGalleryCategory();
            $categoryDto->id = $subject;
            $categoryDto->type = 'subject';
            $image->categories[] = $categoryDto;
        }

        $this->_digitalGalleryService->createImage($image);
    }

}
