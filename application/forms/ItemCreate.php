<?php

class App_Form_ItemCreate extends App_Form_Form
{

    /**
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;

    /**
     * @var App_Service_Item
     */
    protected $_itemService;

    /**
     * @param Zend_Controller_Request_Abstract $request
     * @return App_Form_ItemCreate
     */
    public function setRequest(Zend_Controller_Request_Abstract $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * @param App_Service_Item $itemService
     * @return App_Form_ItemCreate
     */
    public function setItemService(App_Service_Item $itemService)
    {
        $this->_itemService = $itemService;
        return $this;
    }

    /**
     * @return void
     */
    public function init()
    {
        $this->setAction('/item/create');
        $this->setAttrib('name', 'main');
        $this->setMethod('post');
        $this->setAttrib('accept-charset', 'UTF-8');

        $element = new Zend_Form_Element_Hidden('from');
        $element->setValue($this->_request->getParam('from'));
        $this->addElement($element);

        $element = new Zend_Form_Element_Hidden('provisionalTo');
        $element->setValue($this->_request->getParam('provisionalTo'));
        $this->addElement($element);

        $element = new Zend_Form_Element_Hidden('provisionalBeneathMenuItemId');
        $element->setValue($this->_request->getParam('provisionalBeneathMenuItemId'));
        $element->addFilters(array(
            array('Int'),
        ));
        $this->addElement($element);

        $element = new Zend_Form_Element_Hidden('provisionalContainerId');
        $element->setValue($this->_request->getParam('provisionalContainerId'));
        $element->addFilters(array(
            array('Int'),
        ));
        $this->addElement($element);

        $element = new Zend_Form_Element_Hash('token');
        $element->setTimeout(900);
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('name');
        $element->setLabel('Name');
        $element->setDescription('
            a-z, 0-9 and hyphens only<br><br>
            The name above will become the permanent link<br>
            to this item e.g. http://www.example.com/<span id="dynname"></span>');
        $element->setRequired(true);
        $element->setValidators(array(
            array('Regex', true, array('pattern' => '/^[a-z0-9-]+$/')),
            array('Db_NoRecordExists', true, array('table' => 'route', 'field' => 'slug')),
        ));
        $this->addElement($element);

        $element = new Zend_Form_Element_Select('typeId');
        $element->setLabel('Type');
        $element->setMultiOptions(array('' => '') + $this->_getTypeMultiOptions());
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Zend_Form_Element_Submit('create');
        $element->setLabel('Create item');
        $this->addElement($element);

        return parent::init();
    }

    /**
     * @return array
     */
    protected function _getTypeMultiOptions()
    {
        $multiOptions = array();

        foreach ($this->_itemService->getTypes() as $type) {

            if (in_array($type->name, array(
                'home-page',
                'sitemap-page',
            ))) {
                continue;
            }

            $multiOptions[$type->id] = $type->name;
        }

        return $multiOptions;
    }

}
