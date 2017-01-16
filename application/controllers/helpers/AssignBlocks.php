<?php

/*
 * @todo rename just Blocks and have other block helper methods here
 */
class Controller_Helper_AssignBlocks extends Zend_Controller_Action_Helper_Abstract
{

    /**
     * @var App_Service_Block
     */
    public $_blockService;

    /**
     * @var Zend_View
     */
    protected $_view;

    /**
     * @var Zend_Log
     */
    protected $_log;

    /**
     * @return void
     */
    public function init()
    {
        $front = Zend_Controller_Front::getInstance();
        $bootstrap = $front->getParam('bootstrap');
        $container = $bootstrap->getContainer();
        $controller = $this->getActionController();

        $this->_view = $controller->view;
        $this->_log = $bootstrap->getResource('log');
        $this->_blockService = $container['BlockService'];
    }

    /**
     * @todo Get rid
     * @param App_Service_Dto_PublishingOptions $publishingOptions
     * @return void
     */
    public function direct(App_Service_Dto_PublishingOptions $publishingOptions)
    {
        return $this->assignBlocks($publishingOptions);
    }

    /**
     * @todo Rename assignPublishingOptionsToView
     * @param App_Service_Dto_PublishingOptions $publishingOptions
     * @return void
     */
    public function assignBlocks(App_Service_Dto_PublishingOptions $publishingOptions)
    {
        $now = new DateTime();

        // Free blocks
        foreach ($publishingOptions->freeBlocks as $freeBlock) {

            $block = $this->_blockService->getCacheControlledBlock($freeBlock->id);
            $blockMeta = $this->_blockService->getCacheControlledBlockMeta($freeBlock->id);
            $blockType = $this->_blockService->getType($blockMeta->typeId);
            $blockPublishingOptions = $this->_blockService->getCurrentPublishingOptions($freeBlock->id);

            if ($blockPublishingOptions->liveFrom < $now
                && $blockPublishingOptions->expiresEnd > $now) {

                foreach ($blockType->templates as $template) {

                    if ($template->id == $blockPublishingOptions->templateId) {
                        $blockTemplate = $template;
                        break;
                    }
                }

                if (!isset($blockTemplate)) {
                    $this->_log->warn('Block template not found');
                    continue;
                }

                $partialValues = array();
                $partialValues['groupClass'] = sprintf(
                    '%s-block',
                    strtolower(Zend_Filter::filterStatic($freeBlock->name, 'Word_CamelCaseToDash'))
                );

                foreach ($block->fields as $blockField) {
                    $partialValues[$blockField->name] = $blockField->value;
                }

                $this->_view->assign(
                    $freeBlock->name, $this->_view->partial(
                        'block/' . $blockTemplate->viewScript . '.phtml',
                        $partialValues
                    )
                );
            }
        }

        // Block sequences
        foreach ($publishingOptions->blockSequences as $blockSequence) {

            $blocks = array();
            foreach ($blockSequence->blocks as $blockSequenceBlock) {

                $block = $this->_blockService->getCacheControlledBlock($blockSequenceBlock->id);
                $blockMeta = $this->_blockService->getCacheControlledBlockMeta($blockSequenceBlock->id);
                $blockType = $this->_blockService->getType($blockMeta->typeId);
                $blockPublishingOptions = $this->_blockService->getCurrentPublishingOptions($blockSequenceBlock->id);

                if ($blockPublishingOptions->liveFrom < $now
                    && $blockPublishingOptions->expiresEnd > $now) {

                    foreach ($blockType->templates as $template) {
                        if ($template->id == $blockPublishingOptions->templateId) {
                            $blockTemplate = $template;
                            break;
                        }
                    }

                    if (!isset($blockTemplate)) {
                        $this->_log->warn('Block template not found');
                        continue;
                    }

                    $partialValues = array();
                    $partialValues['groupClass'] = sprintf(
                        '%s-block',
                        strtolower(Zend_Filter::filterStatic($blockSequence->name, 'Word_CamelCaseToDash'))
                    );

                    foreach ($block->fields as $blockField) {
                        $partialValues[$blockField->name] = $blockField->value;
                    }

                    $blocks[] = $this->_view->partial(
                        'block/' . $blockTemplate->viewScript . '.phtml',
                        $partialValues
                    );
                }
            }

            $this->_view->assign($blockSequence->name, $blocks);
        }
    }

}
