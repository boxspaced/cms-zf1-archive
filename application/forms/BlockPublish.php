<?php

class App_Form_BlockPublish extends App_Form_Form
{

    /**
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;

    /**
     * @var App_Service_Block
     */
    protected $_blockService;

    /**
     * @param Zend_Controller_Request_Abstract $request
     * @return App_Form_BlockPublish
     */
    public function setRequest(Zend_Controller_Request_Abstract $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * @param App_Service_Block $blockService
     * @return App_Form_BlockPublish
     */
    public function setBlockService(App_Service_Block $blockService)
    {
        $this->_blockService = $blockService;
        return $this;
    }

    /**
     * @return void
     */
    public function init()
    {
        $this->setAction('/block/publish');
        $this->setAttrib('name', 'main');
        $this->setMethod('post');
        $this->setAttrib('accept-charset', 'UTF-8');

        $element = new Zend_Form_Element_Hidden('from');
        $element->setValue($this->_request->getParam('from'));
        $this->addElement($element);

        $element = new Zend_Form_Element_Hidden('id');
        $element->setValue($this->_request->getParam('id'));
        $element->setRequired(true);
        $element->addFilters(array(
            array('Int'),
        ));
        $this->addElement($element);

        $element = new Zend_Form_Element_Hidden('partial');
        $element->setValue('0');
        $element->addFilters(array(
            array('Int'),
        ));
        $this->addElement($element);

        $element = new Zend_Form_Element_Hash('token');
        $element->setTimeout(900);
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('name');
        $element->setLabel('Name');
        $element->setDescription('a-z, 0-9 and hyphens only');
        $element->setRequired(true);
        $element->setValidators(array(
            array('Regex', true, array('pattern' => '/^[a-z0-9-]+$/')),
            array('Db_NoRecordExists', true, array('table' => 'block', 'field' => 'name', 'exclude' => array(
                'field' => 'name',
                'value' => $this->_getCurrentName(),
            ))),
        ));
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('liveFrom');
        $element->setLabel('Live from');
        $element->setRequired(true);
        $element->setValidators(array(
            array('Regex', true, array(
                'pattern' => '/^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9]) (?:([0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$/',
            )),
        ));
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('expiresEnd');
        $element->setLabel('Expires end');
        $element->setRequired(true);
        $element->setValidators(array(
            array('Regex', true, array(
                'pattern' => '/^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9]) (?:([0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$/',
            )),
        ));
        $this->addElement($element);

        $templateMultiOptions = $this->_getTemplateMultiOptions();

        if (1 === count($templateMultiOptions)) {

            $element = new Zend_Form_Element_Hidden('templateId');
            $element->setValue(key($templateMultiOptions));
            $element->addFilters(array(
                array('Int'),
            ));
            $this->addElement($element);

        } else {

            $element = new Zend_Form_Element_Select('templateId');
            $element->setLabel('Main template');
            $element->setMultiOptions(array('' => '') + $templateMultiOptions);
            $element->setRequired(true);
            $element->setDescription('Template description: ');
            $this->addElement($element);
        }

        $element = new Zend_Form_Element_Submit('publish');
        $element->setLabel('Publish');
        $this->addElement($element);

        return parent::init();
    }

    /**
     * @return App_Service_Dto_BlockType
     */
    protected function _getBlockType()
    {
        $id = $this->_request->getParam('id');
        $meta = $this->_blockService->getBlockMeta($id);
        return $this->_blockService->getType($meta->typeId);
    }

    /**
     * @return string
     */
    protected function _getCurrentName()
    {
        $id = $this->_request->getParam('id');
        $meta = $this->_blockService->getBlockMeta($id);
        return $meta->name;
    }

    /**
     * @return array
     */
    protected function _getTemplateMultiOptions()
    {
        $type = $this->_getBlockType();

        $multiOptions = array();

        foreach ($type->templates as $template) {
            $multiOptions[$template->id] = $template->name;
        }

        return $multiOptions;
    }

    /**
     * @param App_Service_Dto_PublishingOptions $options
     * @return App_Form_BlockPublish
     */
    public function populateFromPublishingOptionsDto(App_Service_Dto_PublishingOptions $options)
    {
        $values = (array) $options;

        $values['liveFrom'] = ($values['liveFrom'] instanceof DateTime) ? $values['liveFrom']->format('Y-m-d H:i:s') : '';
        $values['expiresEnd'] = ($values['expiresEnd'] instanceof DateTime) ? $values['expiresEnd']->format('Y-m-d H:i:s') : '';

        return parent::populate($values);
    }

}
