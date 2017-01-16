<?php

class App_Form_BlockCreate extends App_Form_Form
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
     * @return App_Form_BlockCreate
     */
    public function setRequest(Zend_Controller_Request_Abstract $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * @param App_Service_Block $blockService
     * @return App_Form_BlockCreate
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
        $this->setAction('/block/create');
        $this->setAttrib('name', 'main');
        $this->setMethod('post');
        $this->setAttrib('accept-charset', 'UTF-8');

        $element = new Zend_Form_Element_Hidden('from');
        $element->setValue($this->_request->getParam('from'));
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
            array('Db_NoRecordExists', true, array('table' => 'block', 'field' => 'name')),
        ));
        $this->addElement($element);

        $element = new Zend_Form_Element_Select('typeId');
        $element->setLabel('Type');
        $element->setMultiOptions(array('' => '') + $this->_getTypeMultiOptions());
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Zend_Form_Element_Submit('create');
        $element->setLabel('Create block');
        $this->addElement($element);

        return parent::init();
    }

    /**
     * @return array
     */
    protected function _getTypeMultiOptions()
    {
        $multiOptions = array();

        foreach ($this->_blockService->getTypes() as $type) {
            $multiOptions[$type->id] = $type->name;
        }

        return $multiOptions;
    }

}
