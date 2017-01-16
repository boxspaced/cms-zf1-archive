<?php

class DigitalGalleryController extends Zend_Controller_Action
{

    /**
     * @var App_Service_ModulePage
     */
    public $modulePageService;

    /**
     * @var App_Service_DigitalGallery
     */
    public $digitalGalleryService;

    /**
     * @var Zend_Config
     */
    public $config;

    /**
     * @var Zend_Log
     */
    public $log;

    /**
     * @var Zend_Auth
     */
    public $auth;

    /**
     * @var App_Acl_Acl
     */
    public $acl;

    /**
     * @return void
     */
    public function init()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        $container = $bootstrap->getContainer();

        $this->modulePageService = $container['ModulePageService'];
        $this->digitalGalleryService = $container['DigitalGalleryService'];
        $this->blockService = $container['BlockService'];
        $this->auth = $container['Auth'];
        $this->acl = $container['Acl'];
        $this->previewSession = new Zend_Session_Namespace('preview');
        $this->basketSession = new Zend_Session_Namespace('basket');

        $this->_helper->bootstrapResourceInjector();

        $this->view->host = $this->config->settings->host;
    }

    /**
     * @return void
     */
    protected function _initFrontendAction()
    {
        $this->view->isModulePage = true;
        $this->view->navText = 'Digital Gallery';
        $this->view->basketCount = count($this->basketSession->images);

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
        $modulePageId = 9;

        $canPublish = $this->acl->isAllowedMultiRoles(
            $identityRoles,
            'default:digital-gallery',
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
                'digital-gallery',
                'search',
                $modulePageId,
                true
            );
        }

        $form = new App_Form_DigitalGallerySearch(array(
            'request' => $this->getRequest(),
        ));
        $this->view->form = $form;

        $this->view->tagCloud = $this->digitalGalleryService->getTagCloud();

        foreach ($this->digitalGalleryService->getFilterOptions() as $name => $options) {
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
        $modulePageId = 10;

        $canPublish = $this->acl->isAllowedMultiRoles(
            $identityRoles,
            'default:digital-gallery',
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
                'digital-gallery',
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

        $query .= $queryHelper->buildFilterSubQuery('categories', 'categories');
        $query .= $queryHelper->buildFilterSubQuery('themes', 'themes');
        $query .= $queryHelper->buildFilterSubQuery('subjects', 'subjects');

        try {

            $index = Zend_Search_Lucene::open($this->config->settings->digitalGallerySearchIndexPath);
            $query = Zend_Search_Lucene_Search_QueryParser::parse($query);
            $hits = $index->find($query);

        } catch (Exception $e) {

            $this->log->debug("Digital gallery search failed with query '{$query}' and error '{$e->getMessage()}'");
            $hits = array();
        }

        $this->view->searchQuery = $query;
        $this->view->query = $this->getRequest()->getQuery();

        $results = array();

        foreach ($hits as $hit) {

            $result = new stdClass();
            $result->id = $hit->identifier;
            $result->thumbSrc = '/digital_gallery/_thumbs/' . $hit->imageName;
            $result->title = $hit->title;
            $result->copyright = $hit->copyright;
            $result->inBasket = false;
            if (is_array($this->basketSession->images)) {
                $result->inBasket = in_array($hit->identifier, $this->basketSession->images);
            }

            $results[] = $result;
        }

        $adapter = new Zend_Paginator_Adapter_Array($results);
        $paginator = new Zend_Paginator($adapter);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage($this->config->settings->digitalGallerySearchShowPerPage);
        $this->view->paginator = $paginator;

        $this->_helper->assignBlocks($publishingOptions);
    }

    /**
     * @return void
     */
    public function addToBasketAction()
    {
        $this->_initFrontendAction();

        $success = true;

        try {

            $id = $this->_getParam('id');

            $this->digitalGalleryService->getImage($id); // Just to make sure is an image

            if (!in_array($id, (array) $this->basketSession->images)) {
                $this->basketSession->images[] = $id;
            }

        } catch (Exception $e) {

            $this->log->warn('Failed to add image to basket: ' . $e->getMessage());
            $success = false;
        }

        $json = array(
            'success' => $success,
            'data' => array(
                'newCount' => count($this->basketSession->images),
            ),
        );

        $this->_helper->json->sendJson($json);
    }

    /**
     * @return void
     */
    public function emptyBasketAction()
    {
        $this->_initFrontendAction();

        $this->basketSession->images = array();

        $this->_helper->redirector('basket');
    }

    /**
     * @return void
     */
    public function basketAction()
    {
        $this->_initFrontendAction();

        $adminControls = $this->_helper->getHelper('AdminControls');
        $this->view->adminMenuControl = $adminControls->createAdminMenuControl(true);

        // Images in basket
        $contents = array();
        foreach ((array) $this->basketSession->images as $imageId) {
            $contents[] = $this->digitalGalleryService->getImage($imageId);
        }
        $this->view->contents = $contents;

        $form = new App_Form_DigitalGalleryCheckout(array(
            'request' => $this->getRequest(),
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

        if (!$contents) {

            $this->_helper->flashMessenger(array(
                'status' => 'error',
                'message' => 'Basket is empty.',
            ));
            return;
        }

        $values = $form->getValues();
        $code = md5(mt_rand());

        $orderDto = new App_Service_Dto_DigitalGalleryOrder();
        $orderDto->name = $values['name'];
        $orderDto->dayPhone = $values['dayPhone'];
        $orderDto->email = $values['email'];
        $orderDto->message = $values['message'];
        $orderDto->createdTime = new DateTime();
        $orderDto->code = $code;
        $orderDto->images = $contents;

        $orderId = $this->digitalGalleryService->createOrder($orderDto);

        $this->digitalGalleryService->notifyOrderReceived($orderId, $values['message']);

        // Clear basket
        $this->basketSession->images = array();
        $this->view->contents = array();

        $this->_helper->flashMessenger(array(
            'status' => 'success',
            'message' => 'Order received',
        ));
    }

    /**
     * @return void
     */
    public function downloadAction()
    {
        $this->_initFrontendAction();

        $code = $this->_getParam('code');
        $zipFile = $this->digitalGalleryService->getDownloadByCode($code);

        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename=images.zip');
        header('Content-Length: ' . filesize($zipFile));
        readfile($zipFile);
        exit;
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
        $modulePageId = 11;

        if (!$id) {
            throw new Zend_Controller_Action_Exception('No identifier provided', 404);
        }

        try {
            $image = $this->digitalGalleryService->getCacheControlledImage($id);
        } catch (Exception $e) {
            throw new Zend_Controller_Action_Exception($e->getMessage(), 404);
        }

        $canPublish = $this->acl->isAllowedMultiRoles(
            $identityRoles,
            'default:digital-gallery',
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
                'digital-gallery',
                'results',
                $modulePageId
            );
        }

        $this->view->inBasket = in_array($id, (array) $this->basketSession->images);

        foreach ($image as $name => $value) {

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
                return $this->digitalGalleryService->getAllImages($offset, $itemCountPerPage);
            },
            function () {
                return $this->digitalGalleryService->countAllImages();
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
    public function categoriesAction()
    {
        $this->_initBackendAction();

        $adminControls = $this->_helper->getHelper('AdminControls');
        $this->view->adminMenuControl = $adminControls->createAdminMenuControl();

        $adapter = new ZendExt_Paginator_Adapter_Callback(
            function ($offset, $itemCountPerPage) {
                return $this->digitalGalleryService->getAllCategories($offset, $itemCountPerPage);
            },
            function () {
                return $this->digitalGalleryService->countAllCategories();
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
    public function createCategoryAction()
    {
        $this->_initBackendAction();

        $adminControls = $this->_helper->getHelper('AdminControls');
        $this->view->adminMenuControl = $adminControls->createAdminMenuControl();

        $form = new App_Form_DigitalGalleryCategory(array(
            'request' => $this->getRequest(),
            'digitalGalleryService' => $this->digitalGalleryService,
        ));
        $form->setAction('/digital-gallery/create-category');
        $form->removeElement('id');

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

        $dto = new App_Service_Dto_DigitalGalleryCategory();
        $dto->type = $values['type'];
        $dto->text = $values['text'];

        $this->digitalGalleryService->createCategory($dto);

        $this->_helper->flashMessenger(array(
            'status' => 'success',
            'message' => 'Create category successful.',
        ));

        $this->_helper->redirector('categories');
    }

    /**
     * @return void
     */
    public function editCategoryAction()
    {
        $this->_initBackendAction();

        $id = $this->_getParam('id');
        $category = $this->digitalGalleryService->getCategory($id);

        $adminControls = $this->_helper->getHelper('AdminControls');
        $this->view->adminMenuControl = $adminControls->createAdminMenuControl();

        $form = new App_Form_DigitalGalleryCategory(array(
            'request' => $this->getRequest(),
            'digitalGalleryService' => $this->digitalGalleryService,
        ));
        $form->setAction('/digital-gallery/edit-category');
        $form->populateFromDigitalGalleryCategoryDto($category);

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

        $dto = new App_Service_Dto_DigitalGalleryCategory();
        $dto->type = $values['type'];
        $dto->text = $values['text'];

        $this->digitalGalleryService->editCategory($id, $dto);

        $this->_helper->flashMessenger(array(
            'status' => 'success',
            'message' => 'Edit category successful.',
        ));

        $this->_helper->redirector('categories');
    }

    /**
     * @return void
     */
    public function deleteCategoryAction()
    {
        $this->_initBackendAction();

        $form = new App_Form_Confirm(array(
            'request' => $this->getRequest(),
        ));
        $form->setAction('/digital-gallery/delete-category');
        $form->getElement('confirm')->setLabel('Confirm delete');
        $this->view->form = $form;

        $this->_helper->viewRenderer('confirm');

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

        $this->digitalGalleryService->deleteCategory($values['id']);

        $this->_helper->flashMessenger(array(
            'status' => 'success',
            'message' => 'Delete successful.',
        ));

        $this->_helper->redirector('categories');
    }

    /**
     * @return void
     */
    public function editAction()
    {
        $this->_initBackendAction();

        $id = $this->_getParam('id');
        $image = $this->digitalGalleryService->getImage($id);

        $adminControls = $this->_helper->getHelper('AdminControls');
        $this->view->adminMenuControl = $adminControls->createAdminMenuControl();

        $form = new App_Form_DigitalGalleryImage(array(
            'request' => $this->getRequest(),
            'digitalGalleryService' => $this->digitalGalleryService,
        ));
        $form->setAction('/digital-gallery/edit');
        $form->populateFromDigitalGalleryImageDto($image);

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

        // Save
        $image = new App_Service_Dto_DigitalGalleryImage();
        $image->title = $values['title'];
        $image->keywords = $values['keywords'];
        $image->description = $values['description'];
        $image->imageNo = $values['imageNo'];
        $image->copyright = $values['copyright'];
        $image->price = $values['price'];

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

        $this->digitalGalleryService->editImage($id, $image);

        $this->_helper->flashMessenger(array(
            'status' => 'success',
            'message' => 'Edit successful.',
        ));

        $this->_helper->redirector('manage');
    }

    /**
     * @return void
     */
    public function uploadAction()
    {
        $this->_initBackendAction();

        ini_set('memory_limit', '256M');

        $adminControls = $this->_helper->getHelper('AdminControls');
        $this->view->adminMenuControl = $adminControls->createAdminMenuControl();

        $form = new App_Form_DigitalGalleryImageUpload(array(
            'request' => $this->getRequest(),
            'privateDirectory' => $this->config->settings->digitalGalleryPrivateDirectory,
            'digitalGalleryService' => $this->digitalGalleryService,
        ));
        $form->setAction('/digital-gallery/upload');
        $form->removeElement('id');

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

        $newImageName = sprintf(
            '%s.%s',
            uniqid(),
            mb_strtolower(pathinfo($form->image->getFileName())['extension'])
        );

        $form->image->addFilter('Rename', $newImageName);

        if (!$form->image->receive()) {

            $this->_helper->flashMessenger(array(
                'status' => 'error',
                'message' => 'File upload failed.',
            ));
            return;
        }

        $values = $form->getValues();
        $values['image'] = $newImageName;

        $digitalGalleryHelper = $this->_helper->getHelper('DigitalGallery');
        $digitalGalleryHelper->processImage($values);

        $this->log->info(sprintf(
            'Image upload memory usage: %dMB',
            ((memory_get_peak_usage(true) / 1024) / 1024)
        ));

        $this->_helper->flashMessenger(array(
            'status' => 'success',
            'message' => 'Upload successful.',
        ));

        $this->_helper->redirector('manage');
    }

    /**
     * @return void
     */
    public function importAction()
    {
        $this->_initBackendAction();

        set_time_limit(0);
        ini_set('memory_limit', '256M');

        $privateDirectory = $this->config->settings->digitalGalleryPrivateDirectory;
        $csvUploadDirectory = $this->config->settings->digitalGalleryCsvUploadDirectory;
        $imagesUploadDirectory = $this->config->settings->digitalGalleryZipUploadDirectory;

        $adminControls = $this->_helper->getHelper('AdminControls');
        $this->view->adminMenuControl = $adminControls->createAdminMenuControl();

        $form = new App_Form_DigitalGalleryImageImport(array(
            'request' => $this->getRequest(),
            'imagesUploadDirectory' => $imagesUploadDirectory,
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

        if (!$form->images->receive()) {

            $this->_helper->flashMessenger(array(
                'status' => 'error',
                'message' => 'Images upload failed.',
            ));
            return;
        }

        $zipFile = $imagesUploadDirectory . DIRECTORY_SEPARATOR . $values['images'];

        $zip = new ZipArchive();

        if (true !== $zip->open($zipFile)) {

            $this->_helper->flashMessenger(array(
                'status' => 'error',
                'message' => 'Could not open extract CSV.',
            ));

            return;
        }

        $extractTo = $imagesUploadDirectory . DIRECTORY_SEPARATOR . 'digital_gallery_bulk_' . date('YmdHis');

        $zip->extractTo($extractTo);
        unlink($zipFile);

        try {

            $file = new SplFileObject($csvUploadDirectory . DIRECTORY_SEPARATOR . $values['csv']);
            $file->setFlags(
                $file::READ_CSV
                |$file::READ_AHEAD
                |$file::SKIP_EMPTY
                |$file::DROP_NEW_LINE
            );

            $completed = 0;
            $imagesNotFound = array();
            $imagesTooLarge = array();
            $imagesErrored = array();

            foreach ($file as $data) {

                $data = array_map(array($this, '_cleanCsvField'), $data);

                if ('title' === $data[0]) {
                    // Skip header
                    continue;
                }

                if (empty($data[9])) {
                    continue;
                }

                $imagePath = $extractTo . DIRECTORY_SEPARATOR . $data[9];

                if (!file_exists($imagePath)) {
                    // Image not found in zip file
                    $imagesNotFound[] = $data[9];
                    continue;
                }

                if (filesize($imagePath) > 6291456) { // 6MB
                    // Image too large
                    $imagesTooLarge[] = $data[9];
                    continue;
                }

                $newImageName = sprintf(
                    '%s.%s',
                    uniqid(),
                    mb_strtolower(pathinfo($imagePath)['extension'])
                );

                $imageValues = array();
                $imageValues['title'] = $data[0];
                $imageValues['keywords'] = $data[1];
                $imageValues['description'] = $data[2];
                $imageValues['imageNo'] = $data[3];
                $imageValues['copyright'] = $data[4];
                $imageValues['price'] = $data[5];
                $imageValues['categories'] = explode(',', $data[6]);
                $imageValues['themes'] = explode(',', $data[7]);
                $imageValues['subjects'] = explode(',', $data[8]);
                $imageValues['image'] = $newImageName;

                // Move image (origianl high res) to private directory
                rename($imagePath, $privateDirectory . DIRECTORY_SEPARATOR . $newImageName);

                try {

                    $digitalGalleryHelper = $this->_helper->getHelper('DigitalGallery');
                    $digitalGalleryHelper->processImage($imageValues);

                } catch (Exception $e) {

                    $this->log->err($e);

                    $imagesErrored[] = $data[9];
                    continue;
                }

                $completed++;
            }

            $message = 'Import completed, number of images processed: ' . $completed;

            // @todo this should be a view helper
            $message = array_reduce($imagesNotFound, function($carry, $imageName) {

                $msg = sprintf('Image was skipped because it was not found in the zip file: %s', $imageName);

                $this->log->info($msg);
                $carry .= sprintf('<br>WARNING: %s', $msg);

                return $carry;

            }, $message);

            // @todo this should be a view helper
            $message = array_reduce($imagesTooLarge, function($carry, $imageName) {

                $msg = sprintf('Image was skipped because it was too large: %s', $imageName);

                $this->log->info($msg);
                $carry .= sprintf('<br>WARNING: %s', $msg);

                return $carry;

            }, $message);

            // @todo this should be a view helper
            $message = array_reduce($imagesErrored, function($carry, $imageName) {

                $msg = sprintf('Image was skipped because it errored: %s', $imageName);

                $this->log->info($msg);
                $carry .= sprintf('<br>WARNING: %s', $msg);

                return $carry;

            }, $message);

            $this->log->info(sprintf(
                'Image import memory usage: %dMB',
                ((memory_get_peak_usage(true) / 1024) / 1024)
            ));

            $this->_helper->flashMessenger(array(
                'status' => 'success',
                'message' => $message,
            ));

            $this->_helper->redirector('manage');

        } catch (Exception $e) {

            $this->log->err($e);

            $this->_helper->flashMessenger(array(
                'status' => 'error',
                'message' => 'Could not open CSV file.',
            ));
        }

        $zip->close();
    }

    /**
     * @return void
     */
    public function reindexAction()
    {
        $this->_initBackendAction();

        $this->digitalGalleryService->reindex();

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
    public function deleteAction()
    {
        $this->_initBackendAction();

        $form = new App_Form_Confirm(array(
            'request' => $this->getRequest(),
        ));
        $form->setAction('/digital-gallery/delete');
        $form->getElement('confirm')->setLabel('Confirm delete');
        $this->view->form = $form;

        $this->_helper->viewRenderer('confirm');

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

        $this->digitalGalleryService->deleteImage($values['id']);

        $this->_helper->flashMessenger(array(
            'status' => 'success',
            'message' => 'Delete successful.',
        ));

        $this->_helper->redirector('manage');
    }

    /**
     * @return void
     */
    public function publishAction()
    {
        $this->_initBackendAction();

        $id = $this->_getParam('id');
        $modulePage = $this->modulePageService->getModulePage($id);

        $this->view->moduleName = 'digital-gallery';
        $this->view->pageName = $modulePage->name;

        $form = new App_Form_ModulePagePublish(array(
            'request' => $this->getRequest(),
            'modulePageService' => $this->modulePageService,
        ));
        $form->setAction('/digital-gallery/publish');

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

            $this->_redirect($this->config->settings->host . '/digital-gallery/' . $modulePage->name);

        } else {

            // Preview
            $this->previewSession->publishing = $publishingOptions;
            $this->view->preview = true;
        }
    }

}
