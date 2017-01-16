<?php

abstract class App_Form_AbstractItemBuilder extends App_Form_Form
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
     * @var App_Service_Workflow
     */
    protected $_workflowService;

    /**
     * @var bool
     */
    protected $_enableMetaElements;

    /**
     * @return Zend_Form_SubForm
     */
    abstract protected function _getPartFieldsForm();

    /**
     * @return Zend_Form_SubForm
     */
    abstract protected function _getFieldsForm();

    /**
     * @param Zend_Controller_Request_Abstract $request
     * @return App_Form_AbstractItemBuilder
     */
    public function setRequest(Zend_Controller_Request_Abstract $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * @param App_Service_Item $itemService
     * @return App_Form_AbstractItemBuilder
     */
    public function setItemService(App_Service_Item $itemService)
    {
        $this->_itemService = $itemService;
        return $this;
    }

    /**
     * @param App_Service_Workflow $workflowService
     * @return App_Form_AbstractItemBuilder
     */
    public function setWorkflowService(App_Service_Workflow $workflowService)
    {
        $this->_workflowService = $workflowService;
        return $this;
    }

    /**
     * @param bool $enableMetaElements
     * @return App_Form_AbstractItemBuilder
     */
    public function setEnableMetaElements($enableMetaElements)
    {
        $this->_enableMetaElements = (bool) $enableMetaElements;
        return $this;
    }

    /**
     * @return void
     */
    public function init()
    {
        $this->setAction('/item/edit');
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

        $element = new Zend_Form_Element_Hidden('numNewParts');
        $element->setValue('0');
        $element->addFilters(array(
            array('Int'),
        ));
        $this->addElement($element);

        $element = new Zend_Form_Element_Hidden('selectedPart');
        $element->setValue('1');
        $element->addFilters(array(
            array('Int'),
        ));
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('navText');
        $element->setLabel('Navigation text');
        $element->setDescription('Navigation text\' appears in the menu (if published to menu) and the \'bread crumbs');
        $element->setRequired(true);
        $element->addFilters(array(
            array('StripTags'),
        ));
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('title');
        $element->setLabel('Title');
        $element->setRequired(true);
        $element->addFilters(array(
            array('StripTags'),
        ));
        $this->addElement($element);

        if ($this->_enableMetaElements) {

            $element = new Zend_Form_Element_Textarea('metaKeywords');
            $element->setLabel('Meta keywords');
            $element->setAttribs(array(
                'rows' => 4,
                'cols' => 60,
            ));
            $element->addFilters(array(
                array('StripTags'),
            ));
            $this->addElement($element);

            $element = new Zend_Form_Element_Textarea('metaDescription');
            $element->setLabel('Meta description');
            $element->setAttribs(array(
                'rows' => 4,
                'cols' => 60,
            ));
            $element->addFilters(array(
                array('StripTags'),
            ));
            $this->addElement($element);
        }

        // Fields
        $fieldsForm = $this->_getFieldsForm();
        $this->addSubForm($fieldsForm, 'fields');

        // Parts
        $partsForm = new Zend_Form_SubForm();
        $this->addSubForm($partsForm, 'parts');
        $numParts = $this->_getNumParts();
        for ($i = 1; $i <= $numParts; $i++) {

            // Part fields
            $partForm = $this->_getPartFieldsForm();

            if ($this->getMultipleParts()) {

                $element = new Zend_Form_Element_Hidden('orderBy');
                $element->setValue($i);
                $element->addFilters(array(
                    array('Int'),
                ));
                $partForm->addElement($element);

                $element = new Zend_Form_Element_Hidden('delete');
                $element->setValue('0');
                $element->addFilters(array(
                    array('Int'),
                ));
                $partForm->addElement($element);
            }

            $partsForm->addSubForm($partForm, $i);
        }

        $element = new Zend_Form_Element_Submit('preview');
        $element->setLabel('Preview');
        $this->addElement($element);

        $templateMultiOptions = $this->_getTemplateMultiOptions();

        if ($templateMultiOptions) {

            $element = new Zend_Form_Element_Select('previewTemplateId');
            $element->setMultiOptions($templateMultiOptions);
            $this->addElement($element);
        }

        $element = new Zend_Form_Element_Textarea('note');
        $element->setLabel('Add a note');
        $element->setAttribs(array(
            'rows' => 4,
            'cols' => 60,
        ));
        $element->addFilters(array(
            array('StripTags'),
        ));
        $this->addElement($element);

        $element = new Zend_Form_Element_Submit('save');
        $element->setLabel('Save');
        $this->addElement($element);

        $element = new Zend_Form_Element_Submit('publish');
        $element->setLabel('Publish');
        $this->addElement($element);

        return parent::init();
    }

    /**
     * @return App_Service_Dto_ItemType
     */
    protected function _getItemType()
    {
        $id = $this->_request->getParam('id');
        $meta = $this->_itemService->getItemMeta($id);

        return $this->_itemService->getType($meta->typeId);
    }

    /**
     * @return array
     */
    protected function _getTemplateMultiOptions()
    {
        $type = $this->_getItemType();
        $id = $this->_request->getParam('id');

        $multiOptions = array();

        if ($this->_workflowService->getStatus(App_Service_Item::MODULE_NAME, $id) === App_Service_Workflow::WORKFLOW_STATUS_NEW) {

            foreach ($type->templates as $template) {
                $multiOptions[$template->id] = $template->name;
            }
        }

        return $multiOptions;
    }

    /**
     * @return bool
     */
    public function getMultipleParts()
    {
        $id = $this->_request->getParam('id');
        $meta = $this->_itemService->getItemMeta($id);

        return $meta->multipleParts;
    }

    /**
     * @return int
     */
    public function _getNumParts()
    {
        $id = $this->_request->getParam('id');
        $item = $this->_itemService->getItem($id);

        $numCurrentParts = count($item->parts) ?: 1;

        return $numCurrentParts + intval($this->_request->getParam('numNewParts'));
    }

    /**
     * @param App_Service_Dto_Item $item
     * @return App_Form_AbstractItemBuilder
     */
    public function populateFromItemDto(App_Service_Dto_Item $item)
    {
        $values = (array) $item;

        $fields = $values['fields'];
        $parts = $values['parts'];

        $values['fields'] = array();
        $values['parts'] = array();

        foreach ($fields as $field) {
            $values['fields'][$field->name] = $field->value;
        }

        foreach ($parts as $key => $part) {

            $part = (array) $part;

            foreach ($part['fields'] as $partField) {
                $part[$partField->name] = $partField->value;

            }

            unset($part['fields']);

            $values['parts'][$key+1] = $part;
        }

        return parent::populate($values);
    }

}
