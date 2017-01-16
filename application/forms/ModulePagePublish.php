<?php

class App_Form_ModulePagePublish extends App_Form_Form
{

    /**
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;

    /**
     * @var App_Service_ModulePage
     */
    protected $_modulePageService;

    /**
     * @param Zend_Controller_Request_Abstract $request
     * @return App_Form_ModulePagePublish
     */
    public function setRequest(Zend_Controller_Request_Abstract $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * @param App_Service_ModulePage $modulePageService
     * @return App_Form_ModulePagePublish
     */
    public function setModulePageService(App_Service_ModulePage $modulePageService)
    {
        $this->_modulePageService = $modulePageService;
        return $this;
    }

    /**
     * @return void
     */
    public function init()
    {
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

        $freeBlocksForm = new Zend_Form_SubForm();
        $this->addSubForm($freeBlocksForm, 'freeBlocks');

        $blockSequencesForm = new Zend_Form_SubForm();
        $this->addSubForm($blockSequencesForm, 'blockSequences');

        $id = $this->_request->getParam('id');
        $currentPublishingOptions = $this->_modulePageService->getCurrentPublishingOptions($id);

        foreach ($this->_modulePageService->getModulePageBlocks($id) as $block) {

            if (!$block->sequence) {

                $freeBlockForm = $this->_createFreeBlockSubForm($block->adminLabel);
                $freeBlocksForm->addSubForm($freeBlockForm, $block->name);
                continue;
            }

            $numCurrentBlocks = 0;

            foreach ($currentPublishingOptions->blockSequences as $blockSequence) {

                if ($blockSequence->name === $block->name) {
                    $numCurrentBlocks = count($blockSequence->blocks);
                    break;
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
     * @return array
     */
    public function getBlockMultiOptions()
    {
        $id = $this->_request->getParam('id');
        $blockOptions = $this->_modulePageService->getAvailableBlockOptions($id);

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
     * @param App_Service_Dto_PublishingOptions $options
     * @return App_Form_ModulePagePublish
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

        return parent::populate($values);
    }

}
