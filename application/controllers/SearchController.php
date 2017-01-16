<?php

class SearchController extends Zend_Controller_Action
{

    /**
     * @var App_Service_ModulePage
     */
    public $modulePageService;

    /**
     * @var App_Service_Block
     */
    public $blockService;

    /**
     * @var Zend_Config
     */
    public $config;

    /**
     * @var App_Service_Item
     */
    public $itemService;

    /**
     * @var Zend_Auth
     */
    public $auth;

    /**
     * @var App_Acl_Acl
     */
    public $acl;

    /**
     * @var Zend_Session_Namespace
     */
    public $previewSession;

    /**
     * @var Zend_Log
     */
    public $log;

    /**
     * @return void
     */
    public function init()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        $container = $bootstrap->getContainer();

        $this->itemService = $container['ItemService'];
        $this->modulePageService = $container['ModulePageService'];
        $this->blockService = $container['BlockService'];
        $this->auth = $container['Auth'];
        $this->acl = $container['Acl'];
        $this->previewSession = new Zend_Session_Namespace('preview');

        $this->_helper->bootstrapResourceInjector();
    }

    /**
     * @return void
     */
    protected function _initBackendAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->security();
    }

    /**
     * @return void
     */
    public function simpleAction()
    {
        $preview = $this->_getParam('preview');
        $identity = $this->auth->getIdentity();
        $identityRoles = isset($identity->roles) ? $identity->roles : array();
        $modulePageId = 8;

        $this->view->navText = 'Search Results';
        $this->view->isStandalone = true;
        $this->view->query = $this->getRequest()->getQuery();

        $navigation = $this->_helper->getHelper('Navigation')->createFrontendNavigation();
        $this->view->getHelper('navigation')->setContainer($navigation);

        $canPublish = $this->acl->isAllowedMultiRoles(
            $identityRoles,
            'default:search',
            'publish'
        );

        if ('publishing' === $preview && $canPublish) {

            // Previewing publishing
            $publishingOptions = $this->previewSession->publishing;

        } else {

            $publishingOptions = $this->modulePageService->getCurrentPublishingOptions($modulePageId);

            $adminControls = $this->_helper->getHelper('AdminControls');
            $this->view->adminMenuControl = $adminControls->createAdminMenuControl(true);
            $this->view->adminControl = $adminControls->createFrontendModulePageControl(
                'search',
                'simple',
                $modulePageId,
                true
            );
        }

        $query = $this->_getParam('q') ?: '';

        try {

            $index = Zend_Search_Lucene::open($this->config->settings->siteSearchIndexPath);
            $query = Zend_Search_Lucene_Search_QueryParser::parse($query);
            $hits = $index->find($query);

        } catch (Exception $e) {

            $this->log->debug("Site search failed with query '{$query}' and error '{$e->getMessage()}'");
            $hits = array();
        }

        $adapter = new Zend_Paginator_Adapter_Array($hits);
        $paginator = new Zend_Paginator($adapter);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage($this->config->settings->searchShowPerPage);
        $this->view->paginator = $paginator;

        $results = array();

        foreach ($paginator as $hit) {

            if ($hit->module !== 'item') {
                continue;
            }

            try {

                $item = $this->itemService->getCacheControlledItem($hit->contentId);
                $itemMeta = $this->itemService->getCacheControlledItemMeta($hit->contentId);
                $itemType = $this->itemService->getType($itemMeta->typeId);
                $publishingOptions = $this->itemService->getCurrentPublishingOptions($hit->contentId);

                foreach ($itemType->teaserTemplates as $template) {

                    if ($template->id == $publishingOptions->teaserTemplateId) {
                        $teaserTemplate = $template;
                        break;
                    }
                }

                if (!isset($teaserTemplate)) {

                    $this->log->warn('Teaser template not found in simple search results, skipping item');
                    continue;
                }

                $partialValues = array();

                foreach ($item->fields as $itemField) {
                    $partialValues[$itemField->name] = $itemField->value;
                }

                foreach ($item->parts[0]->fields as $partField) {
                    $partialValues[$partField->name] = $partField->value;
                }

                $partialValues['title'] = $item->title;
                $partialValues['name'] = $itemMeta->name;
                $results[] = $this->view->partial('item/' . $teaserTemplate->viewScript . '.phtml', $partialValues);

            } catch (Exception $e) {
                $this->log->warn('Failed to get item for simple search results: ' . $e->getMessage());
            }
        }

        $this->view->results = $results;

        $this->_helper->assignBlocks($publishingOptions);
    }

    /**
     * @return void
     */
    public function publishAction()
    {
        $this->_initBackendAction();

        $id = $this->_getParam('id');
        $modulePage = $this->modulePageService->getModulePage($id);

        $this->view->moduleName = 'search';
        $this->view->pageName = $modulePage->name;

        $form = new App_Form_ModulePagePublish(array(
            'request' => $this->getRequest(),
            'modulePageService' => $this->modulePageService,
        ));
        $form->setAction('/search/publish');

        $this->view->form = $form;

        if (!$this->getRequest()->isPost()) {

            $currentPublishingOptions = $this->modulePageService->getCurrentPublishingOptions($id);
            $form->populateFromPublishingOptionsDto($currentPublishingOptions);
            return;
        }

        if ($this->_getParam('partial')) {

            $form->isValidPartial($this->_getAllParams());
            $form->partial->setValue('0');
            return;
        }

        if (!$form->isValid($this->_getAllParams())) {

            $this->_helper->flashMessenger(array(
                'status' => 'error',
                'message' => 'Validation failed.',
            ));
            return;
        }

        $values = $form->getValues();

        $publishingOptions = new App_Service_Dto_PublishingOptions();

        foreach ($values['freeBlocks'] as $name => $block) {

            $freeBlock = new App_Service_Dto_FreeBlock();
            $freeBlock->name = $name;
            $freeBlock->id = $block['id'];

            $publishingOptions->freeBlocks[] = $freeBlock;
        }

        foreach ($values['blockSequences'] as $name => $sequence) {

            $blockSequence = new App_Service_Dto_BlockSequence();
            $blockSequence->name = $name;

            foreach ($sequence as $key => $block) {

                if (is_numeric($key)) {

                    $blockSequenceBlock = new App_Service_Dto_BlockSequenceBlock();
                    $blockSequenceBlock->id = $block['id'];
                    $blockSequenceBlock->orderBy = $block['orderBy'];
                    $blockSequence->blocks[] = $blockSequenceBlock;
                }
            }

            $publishingOptions->blockSequences[] = $blockSequence;
        }

        if (!is_null($form->getUnfilteredValue('publish'))) {

            $this->modulePageService->publish($id, $publishingOptions);

            $this->_helper->flashMessenger(array(
                'status' => 'success',
                'message' => 'Publishing successful.',
            ));

            $this->_helper->redirector($modulePage->name);

        } else {

            // Preview
            $this->previewSession->publishing = $publishingOptions;
            $this->view->preview = true;
        }
    }

}
