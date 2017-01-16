<?php

class ItemController extends Zend_Controller_Action
{

    /**
     * @var App_Service_Item
     */
    public $itemService;

    /**
     * @var App_Service_Block
     */
    public $blockService;

    /**
     * @var App_Service_Workflow
     */
    public $workflowService;

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

        $this->itemService = $container['ItemService'];
        $this->blockService = $container['BlockService'];
        $this->workflowService = $container['WorkflowService'];
        $this->auth = $container['Auth'];
        $this->acl = $container['Acl'];
        $this->previewSession = new Zend_Session_Namespace('preview');

        $this->_helper->bootstrapResourceInjector();

        $this->view->from = $this->_getParam('from');
        $this->view->host = $this->config->settings->host;
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
    public function indexAction()
    {
        $id = $this->_getParam('id');
        $part = $this->_getParam('part');
        $preview = $this->_getParam('preview');
        $identity = $this->auth->getIdentity();
        $identityRoles = isset($identity->roles) ? $identity->roles : array();

        if (!$id) {
            throw new Zend_Controller_Action_Exception('No identifier provided', 404);
        }

        try {
            $itemMeta = $this->itemService->getCacheControlledItemMeta($id);
            $itemType = $this->itemService->getType($itemMeta->typeId);
        } catch (Exception $e) {
            throw new Zend_Controller_Action_Exception($e->getMessage(), 404);
        }

        $canEdit = $this->acl->isAllowedMultiRoles(
            $identityRoles,
            'default:item',
            'edit'
        );

        $canPublish = $this->acl->isAllowedMultiRoles(
            $identityRoles,
            'default:item',
            'publish'
        );

        if ('content' === $preview && $canEdit) {

            // Previewing content
            $item = $this->previewSession->content;

            if ($this->workflowService->getStatus(App_Service_Item::MODULE_NAME, $id) === App_Service_Workflow::WORKFLOW_STATUS_NEW) {

                $publishingOptions = new App_Service_Dto_PublishingOptions();
                $publishingOptions->templateId = $this->previewSession->templateId;
                $publishingOptions->to = App_Service_Item::PUBLISH_TO_STANDALONE;

            } else {
                $publishingOptions = $this->itemService->getCurrentPublishingOptions($id);
            }

        } elseif ('publishing' === $preview && $canPublish) {

            // Previewing publishing
            $itemId = $this->_getParam('contentId') ?: $id;
            $item = $this->itemService->getItem($itemId);

            // @todo module name constant shouldn't be coming from service,
            // inject module service possibly and have a getModule()->name (DTO)
            if ($this->workflowService->getStatus(App_Service_Item::MODULE_NAME, $id) === App_Service_Workflow::WORKFLOW_STATUS_NEW) {

                if ($this->_getParam('templateId')) {
                    $publishingOptions = new App_Service_Dto_PublishingOptions();
                    $publishingOptions->templateId = $this->_getParam('templateId');
                } else {
                    $publishingOptions = $this->previewSession->publishing;
                }

            } else {

                if ($this->_getParam('contentId')) {
                    $publishingOptions = $this->itemService->getCurrentPublishingOptions($id);
                } else {
                    $publishingOptions = $this->previewSession->publishing;
                }
            }

        } else {

            try {
                $item = $this->itemService->getCacheControlledItem($id);
                $publishingOptions = $this->itemService->getCurrentPublishingOptions($id);
            } catch (Exception $e) {
                throw new Zend_Controller_Action_Exception($e->getMessage(), 404);
            }

            // Live check
            $live = true;
            $now = new DateTime();

            if (
                $publishingOptions->liveFrom > $now
                || $publishingOptions->expiresEnd < $now
            ) {
                $live = false;
            }

            if (!$live && !$this->auth->hasIdentity()) {
                throw new Zend_Controller_Action_Exception('Item not live', 404);
            }

            $adminControls = $this->_helper->getHelper('AdminControls');
            $this->view->adminMenuControl = $adminControls->createAdminMenuControl(true);
            $this->view->adminControl = $adminControls->createFrontendControl(
                $publishingOptions->liveFrom,
                $publishingOptions->expiresEnd,
                $itemMeta->typeName
            );
        }

        // Templates
        foreach ($itemType->templates as $template) {

            if ($template->id == $publishingOptions->templateId) {
                $itemTemplate = $template;
                break;
            }
        }

        if (!isset($itemTemplate)) {
            throw new Zend_Controller_Exception('Item template not found');
        }

        $this->_helper->viewRenderer($itemTemplate->viewScript);
        $this->view->templateName = $itemTemplate->name;

        $navigation = $this->_helper->getHelper('Navigation')->createFrontendNavigation();
        $this->view->getHelper('navigation')->setContainer($navigation);

        foreach ($item as $name => $value) {

            if (!is_array($value) && !is_object($value)) {
                $this->view->assign($name, $value);
            }
        }

        $this->view->isStandalone = ($publishingOptions->to === App_Service_Item::PUBLISH_TO_STANDALONE);
        $this->view->colourScheme = $publishingOptions->colourScheme;

        if ('home' === $itemMeta->name) {
            $this->view->hideBreadcrumbs = true;
        }

        // Fields
        foreach ($item->fields as $field) {
            $this->view->assign($field->name, $field->value);
        }

        // Part
        $partIdx = $part ? ($part-1) : 0;
        $part = $item->parts[$partIdx];
        foreach ($part->fields as $field) {
            $this->view->assign($field->name, $field->value);
        }

        $this->_helper->assignBlocks($publishingOptions);
    }

    /**
     * @return void
     */
    public function createAction()
    {
        $this->_initBackendAction();

        $form = new App_Form_ItemCreate(array(
            'request' => $this->getRequest(),
            'itemService' => $this->itemService,
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

        // Provisional location
        $provisionalLocation = null;
        if ($values['provisionalTo']) {

            $provisionalLocation = new App_Service_Dto_ProvisionalLocation();
            $provisionalLocation->to = $values['provisionalTo'];
            $provisionalLocation->beneathMenuItemId = (int) $values['provisionalBeneathMenuItemId'];
            $provisionalLocation->containerId = (int) $values['provisionalContainerId'];
        }

        $itemId = $this->itemService->createDraft($values['name'], $values['typeId'], $provisionalLocation);

        $this->_helper->flashMessenger(array(
            'status' => 'success',
            'message' => 'Create successful, add content below.',
        ));

        $this->_helper->redirector('edit', 'item', 'default', array(
            'id' => $itemId,
            'from' => $values['from'],
        ));
    }

    /**
     * @return void
     */
    public function editAction()
    {
        $this->_initBackendAction();

        $id = $this->_getParam('id');
        $itemMeta = $this->itemService->getItemMeta($id);
        $item = $this->itemService->getItem($id);
        $identity = $this->auth->getIdentity();
        $identityRoles = isset($identity->roles) ? $identity->roles : array();

        if (
            $this->workflowService->getStatus(App_Service_Item::MODULE_NAME, $id) !== App_Service_Workflow::WORKFLOW_STATUS_CURRENT
            && $itemMeta->authorId != $identity->id
        ) {
            throw new Zend_Controller_Exception('User has not authored this draft/revision');
        }

        $this->view->titleSuffix = '';
        if ($this->workflowService->getStatus(App_Service_Item::MODULE_NAME, $id) !== App_Service_Workflow::WORKFLOW_STATUS_CURRENT) {
            $this->view->titleSuffix = $this->workflowService->getStatus(App_Service_Item::MODULE_NAME, $id);
        }

        $this->view->typeName = $itemMeta->typeName;
        $this->view->itemName = $itemMeta->name;
        $this->view->itemNotes = $itemMeta->notes;

        $typePart = Zend_Filter::filterStatic($itemMeta->typeName, 'Word_DashToCamelCase');
        $formClass = 'App_Form_' . ucfirst($typePart) . 'ItemBuilder';
        $form = new $formClass(array(
            'request' => $this->getRequest(),
            'itemService' => $this->itemService,
            'workflowService' => $this->workflowService,
            'enableMetaFields' => $this->config->settings->enableMetaFields,
        ));

        $this->view->form = $form;

        if (!$this->getRequest()->isPost()) {

            $form->populateFromItemDto($item);
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

        $item = new App_Service_Dto_Item();
        $item->navText = $values['navText'];
        $item->title = $values['title'];
        $item->metaKeywords = isset($values['metaKeywords']) ? $values['metaKeywords'] : '';
        $item->metaDescription = isset($values['metaDescription']) ? $values['metaDescription'] : '';

        foreach ($values['fields'] as $name => $value) {

            $field = new App_Service_Dto_ItemField();
            $field->name = $name;
            $field->value = $value;

            $item->fields[] = $field;
        }

        foreach ($values['parts'] as $part => $fields) {

            if (empty($fields['delete'])) {

                $part = new App_Service_Dto_ItemPart();
                $part->orderBy = isset($fields['orderBy']) ? (int) $fields['orderBy'] : 0;

                unset($fields['delete']);
                unset($fields['orderBy']);

                foreach ($fields as $name => $value) {

                    $field = new App_Service_Dto_ItemField();
                    $field->name = $name;
                    $field->value = $value;

                    $part->fields[] = $field;
                }

                $item->parts[] = $part;
            }
        }

        if (null !== $form->getUnfilteredValue('save')) {

            $editId = $id;

            if ($this->workflowService->getStatus(App_Service_Item::MODULE_NAME, $id) === App_Service_Workflow::WORKFLOW_STATUS_CURRENT) {
                $editId = $this->itemService->createRevision($id);
            }

            $this->itemService->edit($editId, $item, $values['note']);

            $this->_helper->flashMessenger(array(
                'status' => 'success',
                'message' => 'Save successful.',
            ));

            $this->_helper->redirector('authoring', 'workflow');
        }

        if (null !== $form->getUnfilteredValue('publish')) {

            $canPublish = $this->acl->isAllowedMultiRoles(
                $identityRoles,
                'default:item',
                'publish'
            );

            if (!$canPublish) {

                $editId = $id;

                if ($this->workflowService->getStatus(App_Service_Item::MODULE_NAME, $id) === App_Service_Workflow::WORKFLOW_STATUS_CURRENT) {
                    $editId = $this->itemService->createRevision($id);
                }

                $this->itemService->edit($editId, $item, $values['note']);
                $this->workflowService->moveToPublishing(App_Service_Item::MODULE_NAME, $editId);

                $this->_helper->flashMessenger(array(
                    'status' => 'success',
                    'message' => 'Save successful, content moved to publishing for approval.',
                ));

                $this->_helper->redirector('authoring', 'workflow');
            }

            switch ($this->workflowService->getStatus(App_Service_Item::MODULE_NAME, $id)) {

                case App_Service_Workflow::WORKFLOW_STATUS_CURRENT:

                    $revisionId = $this->itemService->createRevision($id);

                    $this->itemService->edit($revisionId, $item, $values['note']);
                    $this->itemService->publish($revisionId);

                    $this->_helper->flashMessenger(array(
                        'status' => 'success',
                        'message' => 'Update successful.',
                    ));

                    $this->_redirect($this->config->settings->host . '/' . $itemMeta->name);
                    break;

                case App_Service_Workflow::WORKFLOW_STATUS_UPDATE:

                    $this->itemService->edit($id, $item, $values['note']);
                    $this->itemService->publish($id);

                    $this->_helper->flashMessenger(array(
                        'status' => 'success',
                        'message' => 'Update successful.',
                    ));

                    $this->_helper->redirector('authoring', 'workflow');
                    break;

                case App_Service_Workflow::WORKFLOW_STATUS_NEW:

                    $this->itemService->edit($id, $item, $values['note']);
                    $this->workflowService->moveToPublishing(App_Service_Item::MODULE_NAME, $id);

                    $this->_helper->flashMessenger(array(
                        'status' => 'success',
                        'message' => 'Save successful, please set options below to complete publishing process.',
                    ));

                    $this->_helper->redirector('publish', 'item', 'default', array('id' => $id));
                    break;

                default:
                    throw new Zend_Controller_Exception('Workflow status unknown');
            }
        }

        // Preview
        $this->previewSession->content = $item;
        $this->previewSession->templateId = isset($values['previewTemplateId']) ? $values['previewTemplateId'] : null;
        $this->view->preview = true;
    }

    /**
     * @return void
     */
    public function publishAction()
    {
        $this->_initBackendAction();

        $id = $this->_getParam('id');
        $itemMeta = $this->itemService->getItemMeta($id);
        $type = $this->itemService->getType($itemMeta->typeId);

        $provisionalLocation = $this->itemService->getProvisionalLocation($id);
        $availableLocationOptions = $this->itemService->getAvailableLocationOptions($id);

        $currentPublishingOptions = null;
        if ($this->workflowService->getStatus(App_Service_Item::MODULE_NAME, $id) === App_Service_Workflow::WORKFLOW_STATUS_CURRENT) {
            $currentPublishingOptions = $this->itemService->getCurrentPublishingOptions($id);
        }

        $this->view->typeName = $itemMeta->typeName;
        $this->view->itemName = $itemMeta->name;
        $this->view->itemNotes = $itemMeta->notes;

        if (null !== $provisionalLocation) {

            $provisionalTo = array_pop(array_filter(
                $availableLocationOptions->toOptions,
                function ($option) use ($provisionalLocation) {
                    return $option->value === $provisionalLocation->to;
                }
            ));

            $this->view->provisionalTo = $provisionalTo->label;

            if ($provisionalLocation->beneathMenuItemId) {

                $provisionalBeneathMenuItem = array_pop(array_filter(
                    $availableLocationOptions->beneathMenuItemOptions,
                    function($option) use ($provisionalLocation) {
                        return $option->value === $provisionalLocation->beneathMenuItemId;
                    }
                ));

                $this->view->provisionalBeneathMenuItem = $provisionalBeneathMenuItem->label;

            } elseif ($provisionalLocation->to === App_Service_Item::PUBLISH_TO_MENU) {
                $this->view->provisionalBeneathMenuItem = 'Top level';
            }
        }

        foreach ($type->teaserTemplates as $teaserTemplate) {

            if ($teaserTemplate->id == $this->_getParam('teaserTemplateId')) {
                $this->view->teaserTemplateDescription = $teaserTemplate->description;
            }
        }

        foreach ($type->templates as $template) {

            if ($template->id == $this->_getParam('templateId')) {
                $this->view->templateDescription = $template->description;
            }
        }

        $form = new App_Form_ItemPublish(array(
            'request' => $this->getRequest(),
            'itemService' => $this->itemService,
            'workflowService' => $this->workflowService,
        ));

        $this->view->form = $form;

        if (!$this->getRequest()->isPost()) {

            $form->populate(array(
                'name' => $itemMeta->name,
            ));

            if ($currentPublishingOptions) {
                // Already published, editing
                $form->populateFromPublishingOptionsDto($currentPublishingOptions);
            }

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
        $publishingOptions->name = $values['name'];
        $publishingOptions->colourScheme = $values['colourScheme'];
        $publishingOptions->liveFrom = new DateTime($values['liveFrom']);
        $publishingOptions->expiresEnd = new DateTime($values['expiresEnd']);
        $publishingOptions->teaserTemplateId = $values['teaserTemplateId'];
        $publishingOptions->templateId = $values['templateId'];

        if (!empty($values['useProvisional']) && null !== $provisionalLocation) {

            $publishingOptions->to = $provisionalLocation->to;
            $publishingOptions->beneathMenuItemId = $provisionalLocation->beneathMenuItemId;

        } else {

            $publishingOptions->to = $values['to'];

            if ($values['to'] === App_Service_Item::PUBLISH_TO_MENU) {
                $publishingOptions->beneathMenuItemId = $values['beneathMenuItemId'];
            }
        }

        foreach ($values['freeBlocks'] as $name => $block) {

            if (empty($block['id'])) {
                continue;
            }

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

        if (null !== $form->getUnfilteredValue('publish')) {

            $this->itemService->publish($id, $publishingOptions);

            $this->_helper->flashMessenger(array(
                'status' => 'success',
                'message' => 'Publishing successful.',
            ));

            $this->_redirect($this->config->settings->host . '/' . $publishingOptions->name);
        }

        // Preview
        $this->previewSession->publishing = $publishingOptions;
        $this->view->preview = true;
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
        $form->setAction('/item/delete');
        $form->getElement('confirm')->setLabel('Confirm delete');
        $this->view->form = $form;

        $this->_helper->viewRenderer('confirm');

        if (!$form->isValid($this->_getAllParams())) {

            $this->_helper->flashMessenger(array(
                'status' => 'error',
                'message' => 'Validation failed.',
            ));
            return;
        }

        $values = $form->getValues();

        $this->itemService->delete($values['id']);

        $this->_helper->flashMessenger(array(
            'status' => 'success',
            'message' => 'Delete successful.',
        ));

        switch ($values['from']) {

            case 'menu':
                $this->_helper->redirector('index', 'menu');
                break;

            case 'standalone':
                $this->_helper->redirector('index', 'standalone');
                break;

            default:
                $this->_helper->getHelper('redirector')->gotoRoute(array(), 'home');
        }
    }

    /**
     * @return void
     */
    public function publishUpdateAction()
    {
        $this->_initBackendAction();

        $form = new App_Form_Confirm(array(
            'request' => $this->getRequest(),
        ));
        $form->setAction('/item/publish-update');
        $form->getElement('confirm')->setLabel('Confirm update');
        $this->view->form = $form;

        $this->_helper->viewRenderer('confirm');

        if (!$form->isValid($this->_getAllParams())) {

            $this->_helper->flashMessenger(array(
                'status' => 'error',
                'message' => 'Validation failed.',
            ));
            return;
        }

        $values = $form->getValues();

        $this->itemService->publish($values['id']);

        $this->_helper->flashMessenger(array(
            'status' => 'success',
            'message' => 'Update successful.',
        ));

        $this->_helper->redirector('publishing', 'workflow');
    }

}
