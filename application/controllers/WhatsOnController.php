<?php

class WhatsOnController extends Zend_Controller_Action
{

    /**
     * @var App_Service_ModulePage
     */
    public $modulePageService;

    /**
     * @var App_Service_WhatsOn
     */
    public $whatsOnService;

    /**
     * @var App_Service_Block
     */
    public $blockService;

    /**
     * @var Zend_Config
     */
    public $config;

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

        $this->modulePageService = $container['ModulePageService'];
        $this->whatsOnService = $container['WhatsOnService'];
        $this->blockService = $container['BlockService'];
        $this->auth = $container['Auth'];
        $this->acl = $container['Acl'];
        $this->previewSession = new Zend_Session_Namespace('preview');

        $this->_helper->bootstrapResourceInjector();

        $this->view->host = $this->config->settings->host;
    }

    /**
     * @return void
     */
    protected function _initFrontendAction()
    {
        $this->view->isModulePage = true;
        $this->view->navText = 'What\'s On';

        $navigation = $this->_helper->getHelper('Navigation')->createFrontendNavigation();
        $this->view->getHelper('navigation')->setContainer($navigation);
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
    public function searchAction()
    {
        $this->_initFrontendAction();

        $preview = $this->_getParam('preview');
        $identity = $this->auth->getIdentity();
        $identityRoles = isset($identity->roles) ? $identity->roles : array();
        $modulePageId = 2;

        $canPublish = $this->acl->isAllowedMultiRoles(
            $identityRoles,
            'default:whats-on',
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
                'whats-on',
                'search',
                $modulePageId,
                true
            );
        }

        $form = new App_Form_WhatsOnSearch(array(
            'request' => $this->getRequest(),
        ));
        $this->view->form = $form;

        foreach ($this->whatsOnService->getFilterOptions() as $name => $options) {
            $this->view->assign('filter' . ucfirst($name), $options);
        }

        $this->_helper->assignBlocks($publishingOptions);
    }

    /**
     * @return void
     */
    public function resultsAction()
    {
        $this->_initFrontendAction();

        $preview = $this->_getParam('preview');
        $identity = $this->auth->getIdentity();
        $identityRoles = isset($identity->roles) ? $identity->roles : array();
        $modulePageId = 3;

        $this->view->query = $this->getRequest()->getQuery();

        $canPublish = $this->acl->isAllowedMultiRoles(
            $identityRoles,
            'default:whats-on',
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
                'whats-on',
                'results',
                $modulePageId
            );
        }

        $queryHelper = $this->_helper->getHelper('SearchQuery');

        $query = $this->_getParam('q') ?: '';
        $query = strtr($query, array(
            ' and ' => ' ',
            ' or ' => ' ',
        ));

        $query .= $queryHelper->buildFilterSubQuery('specificDates', 'specificDate');
        $query .= $queryHelper->buildFilterSubQuery('categories', 'category');
        $query .= $queryHelper->buildFilterSubQuery('dayTimes', 'dayTime');
        $query .= $queryHelper->buildFilterSubQuery('venues', 'venue');
        $query .= $queryHelper->buildFilterSubQuery('ages', 'age');

        try {

            $index = Zend_Search_Lucene::open($this->config->settings->whatsOnSearchIndexPath);
            $query = Zend_Search_Lucene_Search_QueryParser::parse($query);
            $hits = $index->find($query);

        } catch (Exception $e) {

            $this->log->debug("Whats on search failed with query '$query' and error '{$e->getMessage()}'");
            $hits = array();
        }

        $results = array();

        foreach ($hits as $hit) {

            $result = new stdClass();
            $result->id = $hit->identifier;
            $result->activity = $hit->activity;
            $result->description = $hit->description;
            $result->dayTime = $hit->dayTime;
            $result->venue = $hit->venue;
            $result->age = $hit->age;
            $result->specificDate = $hit->specificDate;

            $results[] = $result;
        }

        $adapter = new Zend_Paginator_Adapter_Array($results);
        $paginator = new Zend_Paginator($adapter);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage($this->config->settings->whatsOnSearchShowPerPage);
        $this->view->paginator = $paginator;

        foreach ($this->whatsOnService->getFilterOptions() as $name => $options) {
            $this->view->assign('filter' . ucfirst($name), $options);
        }

        $this->_helper->assignBlocks($publishingOptions);
    }

    /**
     * @return void
     */
    public function indexAction()
    {
        $this->_initFrontendAction();

        $id = $this->_getParam('id');
        $preview = $this->_getParam('preview');
        $identity = $this->auth->getIdentity();
        $identityRoles = isset($identity->roles) ? $identity->roles : array();
        $modulePageId = 4;

        if (!$id) {
            throw new Zend_Controller_Action_Exception('No identifier provided', 404);
        }

        try {
            $whatsOn = $this->whatsOnService->getCacheControlledWhatsOn($id);
        } catch (Exception $e) {
            throw new Zend_Controller_Action_Exception($e->getMessage(), 404);
        }

        $canPublish = $this->acl->isAllowedMultiRoles(
            $identityRoles,
            'default:whats-on',
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
                'whats-on',
                'index',
                $modulePageId
            );
        }

        foreach ($whatsOn as $name => $value) {

            if (!is_array($value) && !is_object($value)) {
                $this->view->assign($name, $value);
            }
        }

        $this->_helper->assignBlocks($publishingOptions);
    }

    /**
     * @return void
     */
    public function manageAction()
    {
        $this->_initBackendAction();

        $adminControls = $this->_helper->getHelper('AdminControls');
        $this->view->adminMenuControl = $adminControls->createAdminMenuControl();

        $adapter = new ZendExt_Paginator_Adapter_Callback(
            function ($offset, $itemCountPerPage) {
                return $this->whatsOnService->getAllWhatsOns($offset, $itemCountPerPage);
            },
            function () {
                return $this->whatsOnService->countAllWhatsOns();
            }
        );

        $paginator = new Zend_Paginator($adapter);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage($this->config->settings->adminShowPerPage);
        $this->view->paginator = $paginator;
    }

    /**
     * @return void
     */
    public function importAction()
    {
        $this->_initBackendAction();

        $uploadDirectory = $this->config->settings->whatsOnUploadDirectory;

        $adminControls = $this->_helper->getHelper('AdminControls');
        $this->view->adminMenuControl = $adminControls->createAdminMenuControl();

        $form = new App_Form_WhatsOnImport(array(
            'request' => $this->getRequest(),
            'uploadDirectory' => $uploadDirectory,
        ));
        $this->view->form = $form;

        if (!$this->getRequest()->isPost()) {
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

        $readSuccess = false;
        $dtos = array();

        try {

            $file = new SplFileObject($uploadDirectory . DIRECTORY_SEPARATOR . $values['file']);
            $file->setFlags(
                $file::READ_CSV
                |$file::READ_AHEAD
                |$file::SKIP_EMPTY
                |$file::DROP_NEW_LINE
            );

            $count = 0;

            foreach ($file as $data) {

                $count++;

                if ($count === 1) {
                    continue; // Skip header
                }

                $data = array_map(array($this, '_cleanCsvField'), $data);

                $specificDate = str_replace('/', '-', $data[6]);
                $specificDate = $specificDate ? date('Y-m-d', strtotime($specificDate)) : null;
                $specificDate = $specificDate ? new DateTime($specificDate) : null;

                $dto = new App_Service_Dto_WhatsOn();
                $dto->category = $data[0];
                $dto->activity = $data[1];
                $dto->dayTime = $data[2];
                $dto->venue = $data[3];
                $dto->age = $data[4];
                $dto->description = $data[5];
                $dto->specificDate = $specificDate;

                $dtos[] = $dto;
            }

            $readSuccess = true;

        } catch (Exception $e) {

            $msg = 'Could not open CSV file';

            $this->log->warn($msg . ': ' . $e->getMessage());

            $this->_helper->flashMessenger(array(
                'status' => 'error',
                'message' => $msg . '.',
            ));
        }

        // Import
        if ($readSuccess) {

            $this->log->info('Starting whats on import');

            $this->whatsOnService->importWhatsOns($dtos);
            $this->whatsOnService->reindex();

            $this->log->info('Finished whats on import');

            $this->_helper->flashMessenger(array(
                'status' => 'success',
                'message' => 'Import successful, lines processed: ' . $count,
            ));

            $this->_helper->redirector('manage');
        }
    }

    /**
     * @return void
     */
    public function reindexAction()
    {
        $this->_initBackendAction();

        $this->whatsOnService->reindex();

        $this->_helper->flashMessenger(array(
            'status' => 'success',
            'message' => 'Reindex successful',
        ));

        $this->_helper->redirector('manage');
    }

    /**
     * @todo use CSV action helper
     * @param string $string
     * @return string
     */
    protected function _cleanCsvField($string)
    {
        return trim(strip_tags($string));
    }

    /**
     * @return void
     */
    public function publishAction()
    {
        $this->_initBackendAction();

        $id = $this->_getParam('id');
        $modulePage = $this->modulePageService->getModulePage($id);

        $this->view->moduleName = 'whats-on';
        $this->view->pageName = $modulePage->name;

        $form = new App_Form_ModulePagePublish(array(
            'request' => $this->getRequest(),
            'modulePageService' => $this->modulePageService,
        ));
        $form->setAction('/whats-on/publish');

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

            $this->_redirect($this->config->settings->host . '/whats-on/' . $modulePage->name);

        } else {

            // Preview
            $this->previewSession->publishing = $publishingOptions;
            $this->view->preview = true;
        }
    }

}
