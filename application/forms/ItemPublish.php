<?php

class App_Form_ItemPublish extends App_Form_Form
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
     * @param Zend_Controller_Request_Abstract $request
     * @return App_Form_ItemPublish
     */
    public function setRequest(Zend_Controller_Request_Abstract $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * @param App_Service_Item $itemService
     * @return App_Form_ItemPublish
     */
    public function setItemService(App_Service_Item $itemService)
    {
        $this->_itemService = $itemService;
        return $this;
    }

    /**
     * @param App_Service_Workflow $workflowService
     * @return App_Form_ItemPublish
     */
    public function setWorkflowService(App_Service_Workflow $workflowService)
    {
        $this->_workflowService = $workflowService;
        return $this;
    }

    /**
     * @return void
     */
    public function init()
    {
        $this->setAction('/item/publish');
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
            array('Db_NoRecordExists', true, array('table' => 'route', 'field' => 'slug', 'exclude' => array(
                'field' => 'slug',
                'value' => $this->_getCurrentName(),
            ))),
        ));
        $this->addElement($element);

        $element = new Zend_Form_Element_Select('colourScheme');
        $element->setLabel('Colour scheme');
        $element->setMultiOptions(array('' => '') + $this->_getColourSchemeMultiOptions());
        $element->setRequired(true);
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

        $teaserTemplateMultiOptions = $this->_getTeaserTemplateMultiOptions();

        if (1 === count($teaserTemplateMultiOptions)) {

            $element = new Zend_Form_Element_Hidden('teaserTemplateId');
            $element->setValue(key($teaserTemplateMultiOptions));
            $element->addFilters(array(
                array('Int'),
            ));
            $this->addElement($element);

        } else {

            $element = new Zend_Form_Element_Select('teaserTemplateId');
            $element->setLabel('Teaser template');
            $element->setMultiOptions(array('' => '') + $teaserTemplateMultiOptions);
            $element->setRequired(true);
            $element->setDescription('Template description: ');
            $this->addElement($element);
        }

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

        if ($this->_isProvisionalAvailable()) {

            $element = new Zend_Form_Element_Checkbox('useProvisional');
            $element->setValue('1');
            $element->setLabel('Use provisional location (choosen by author)');
            $element->addFilters(array(
                array('Int'),
            ));
            $this->addElement($element);
        }

        if (
            !$this->_isProvisionalAvailable()
            || ($this->_request->isPost() && !$this->_request->getParam('useProvisional'))
        ) {

            $element = new Zend_Form_Element_Select('to');
            $element->setLabel('To');
            $element->setMultiOptions(array('' => '') + $this->_getToMultiOptions());
            $element->setRequired(true);
            $this->addElement($element);

            if (
                ($this->_request->isPost() && $this->_request->getParam('to') === App_Service_Item::PUBLISH_TO_MENU)
                || (!$this->_request->isPost() && $this->_getCurrentTo() === App_Service_Item::PUBLISH_TO_MENU)
            ) {

                $element = new Zend_Form_Element_Select('beneathMenuItemId');
                $element->setLabel('Menu position');
                $element->setMultiOptions($this->_getMenuPositionMultiOptions());
                $element->setRequired(true);
                $this->addElement($element);
            }
        }

        $freeBlocksForm = new Zend_Form_SubForm();
        $this->addSubForm($freeBlocksForm, 'freeBlocks');

        $blockSequencesForm = new Zend_Form_SubForm();
        $this->addSubForm($blockSequencesForm, 'blockSequences');

        $currentPublishingOptions = $this->_getCurrentPublishingOptions();
        $template = $this->_getTemplate();

        if (null !== $template) {

            foreach ($template->blocks as $block) {

                if (!$block->sequence) {

                    $freeBlockForm = $this->_createFreeBlockSubForm($block->adminLabel);
                    $freeBlocksForm->addSubForm($freeBlockForm, $block->name);
                    continue;
                }

                $numCurrentBlocks = 0;

                if (null !== $currentPublishingOptions) {

                    foreach ($currentPublishingOptions->blockSequences as $blockSequence) {

                        if ($blockSequence->name === $block->name) {
                            $numCurrentBlocks = count($blockSequence->blocks);
                            break;
                        }
                    }
                }

                $blockSequencesRequest = $this->_request->getParam('blockSequences');
                $numNewBlocks = isset($blockSequencesRequest[$block->name]['numNewBlocks']) ? intval($blockSequencesRequest[$block->name]['numNewBlocks']) : 0;
                $numBlocks = $numCurrentBlocks + $numNewBlocks;

                $blockSequenceForm = new Zend_Form_SubForm();
                $blockSequencesForm->addSubForm($blockSequenceForm, $block->name);

                $element = new Zend_Form_Element_Hidden('numNewBlocks');
                $element->setValue('0');
                $element->addFilters(array(
                    array('Int'),
                ));
                $blockSequenceForm->addElement($element);

                $element = new Zend_Form_Element_Hidden('numCurrentBlocks');
                $element->setValue($numCurrentBlocks);
                $element->addFilters(array(
                    array('Int'),
                ));
                $blockSequenceForm->addElement($element);

                for ($i = 1; $i <= $numBlocks; $i++) {

                    if ($this->_request->isPost() && empty($blockSequencesRequest[$block->name][$i]['id'])) {
                        continue;
                    }

                    $sequenceBlockForm = $this->_createSequenceBlockSubForm();
                    $blockSequenceForm->addSubForm($sequenceBlockForm, $i);
                }
            }
        }

        $element = new Zend_Form_Element_Submit('preview');
        $element->setLabel('Preview');
        $this->addElement($element);

        $element = new Zend_Form_Element_Submit('publish');
        $element->setLabel('Publish');
        $this->addElement($element);

        return parent::init();
    }

    /**
     * @param string $label
     * @return Zend_Form_SubForm
     */
    protected function _createFreeBlockSubForm($label)
    {
        $form = new Zend_Form_SubForm();

        $element = new Zend_Form_Element_Select('id');
        $element->setLabel($label);
        $element->setMultiOptions(array('' => '') + $this->getBlockMultiOptions());
        $form->addElement($element);

        return $form;
    }

    /**
     * @return Zend_Form_SubForm
     */
    protected function _createSequenceBlockSubForm()
    {
        $form = new Zend_Form_SubForm();

        $element = new Zend_Form_Element_Hidden('orderBy');
        $element->addFilters(array(
            array('Int'),
        ));
        $form->addElement($element);

        $element = new Zend_Form_Element_Select('id');
        $element->setMultiOptions($this->getBlockMultiOptions());
        $form->addElement($element);

        return $form;
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
     * @return App_Service_Dto_PublishingOptions
     */
    protected function _getCurrentPublishingOptions()
    {
        $id = $this->_request->getParam('id');

        if ($this->_workflowService->getStatus(App_Service_Item::MODULE_NAME, $id) === App_Service_Workflow::WORKFLOW_STATUS_CURRENT) {
            return $this->_itemService->getCurrentPublishingOptions($id);
        }

        return null;
    }

    /**
     * @return string
     */
    protected function _getCurrentTo()
    {
        $currentPublishingOptions = $this->_getCurrentPublishingOptions();

        if (isset($currentPublishingOptions->to)) {
            return $currentPublishingOptions->to;
        }

        return null;
    }

    /**
     * @return string
     */
    protected function _getCurrentName()
    {
        $id = $this->_request->getParam('id');
        $meta = $this->_itemService->getItemMeta($id);
        return $meta->name;
    }

    /**
     * @return App_Service_Dto_ItemTemplate
     */
    protected function _getTemplate()
    {
        $type = $this->_getItemType();

        if (1 === count($type->templates)) {
            return array_pop($type->templates);
        }

        $templateId = $this->_request->getParam('templateId');

        if (!$templateId) {
            $currentPublishingOptions = $this->_getCurrentPublishingOptions();
            $templateId = isset($currentPublishingOptions->templateId) ? $currentPublishingOptions->templateId : null;
        }

        if (!$templateId) {
            return null;
        }

        foreach ($type->templates as $template) {

            if ($template->id != $templateId) {
                continue;
            }

            return $template;
        }

        return null;
    }

    /**
     * @return array
     */
    protected function _getMenuPositionMultiOptions()
    {
        $id = $this->_request->getParam('id');

        $locationOptions = $this->_itemService->getAvailableLocationOptions($id);
        $currentPublishingOptions = $this->_getCurrentPublishingOptions();

        $beneathMenuItemMultiOptions = array();

        foreach ($locationOptions->beneathMenuItemOptions as $option) {
            $beneathMenuItemMultiOptions[$option->value] = str_repeat('--', $option->level) . ' ' . $option->label;
        }

        if (null !== $currentPublishingOptions) {

            foreach ($beneathMenuItemMultiOptions as $value => $label) {

                if ($value == $currentPublishingOptions->beneathMenuItemId) {
                    $beneathMenuItemMultiOptions[$value] .= ' <--- Currently beneath';
                }
            }
        }

        return array(
            '0' => 'Top level',
            'Beneath' => $beneathMenuItemMultiOptions,
        );
    }

    /**
     * @return array
     */
    protected function _getToMultiOptions()
    {
        $id = $this->_request->getParam('id');
        $locationOptions = $this->_itemService->getAvailableLocationOptions($id);

        $multiOptions = array();

        foreach ($locationOptions->toOptions as $option) {

            $multiOptions[$option->value] = $option->label;
        }

        return $multiOptions;
    }

    /**
     * @return array
     */
    protected function _getColourSchemeMultiOptions()
    {
        $multiOptions = array();

        foreach ($this->_itemService->getAvailableColourSchemeOptions() as $option) {

            $multiOptions[$option->value] = $option->label;
        }

        return $multiOptions;
    }

    /**
     * @return array
     */
    public function getBlockMultiOptions()
    {
        $id = $this->_request->getParam('id');
        $blockOptions = $this->_itemService->getAvailableBlockOptions($id);

        $multiOptions = array();

        foreach ($blockOptions as $typeOption) {

            $options = array();

            foreach ($typeOption->blockOptions as $blockOption) {
                $options[$blockOption->value] = $blockOption->label;
            }

            $multiOptions[$typeOption->name] = $options;
        }

        return $multiOptions;
    }

    /**
     * @return array
     */
    protected function _getTeaserTemplateMultiOptions()
    {
        $type = $this->_getItemType();

        $multiOptions = array();

        foreach ($type->teaserTemplates as $teaserTemplate) {
            $multiOptions[$teaserTemplate->id] = $teaserTemplate->name;
        }

        return $multiOptions;
    }

    /**
     * @return array
     */
    protected function _getTemplateMultiOptions()
    {
        $type = $this->_getItemType();

        $multiOptions = array();

        foreach ($type->templates as $template) {
            $multiOptions[$template->id] = $template->name;
        }

        return $multiOptions;
    }

    /**
     * @return bool
     */
    protected function _isProvisionalAvailable()
    {
        $id = $this->_request->getParam('id');
        return (null !== $this->_itemService->getProvisionalLocation($id));
    }

    /**
     * @param App_Service_Dto_PublishingOptions $options
     * @return App_Form_ItemPublish
     */
    public function populateFromPublishingOptionsDto(App_Service_Dto_PublishingOptions $options)
    {
        $values = (array) $options;

        $freeBlocks = $values['freeBlocks'];
        $blockSequences = $values['blockSequences'];

        $values['freeBlocks'] = array();
        $values['blockSequences'] = array();

        foreach ($freeBlocks as $freeBlock) {
            $values['freeBlocks'][$freeBlock->name]['id'] = $freeBlock->id;
        }

        foreach ($blockSequences as $blockSequence) {

            $values['blockSequences'][$blockSequence->name] = array();

            $i = 1;
            foreach ($blockSequence->blocks as $block) {

                $values['blockSequences'][$blockSequence->name][$i]['id'] = $block->id;
                $values['blockSequences'][$blockSequence->name][$i]['orderBy'] = $block->orderBy;

                $i++;
            }
        }

        $values['liveFrom'] = ($values['liveFrom'] instanceof DateTime) ? $values['liveFrom']->format('Y-m-d H:i:s') : '';
        $values['expiresEnd'] = ($values['expiresEnd'] instanceof DateTime) ? $values['expiresEnd']->format('Y-m-d H:i:s') : '';

        return parent::populate($values);
    }

}
