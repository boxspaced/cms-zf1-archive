<?php

class BlockController extends Zend_Controller_Action
{

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
     * @return void
     */
    public function init()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        $container = $bootstrap->getContainer();

        $this->blockService = $container['BlockService'];
        $this->workflowService = $container['WorkflowService'];
        $this->auth = $container['Auth'];
        $this->acl = $container['Acl'];

        $this->_helper->bootstrapResourceInjector();
        $this->_helper->layout->disableLayout();
        $this->_helper->security();
    }

    /**
     * @return void
     */
    public function indexAction()
    {
        $identity = $this->auth->getIdentity();
        $identityRoles = isset($identity->roles) ? $identity->roles : array();

        $adminControls = $this->_helper->getHelper('AdminControls');
        $this->view->adminMenuControl = $adminControls->createAdminMenuControl();

        $adapter = new ZendExt_Paginator_Adapter_Callback(
            function ($offset, $itemCountPerPage) {
                return $this->blockService->getPublishedBlocks($offset, $itemCountPerPage);
            },
            function () {
                return $this->blockService->countPublishedBlocks();
            }
        );

        $paginator = new Zend_Paginator($adapter);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage($this->config->settings->adminShowPerPage);

        $this->view->paginator = $paginator;

        $blockItems = array();

        foreach ($paginator as $block) {

            $blockMeta = $this->blockService->getBlockMeta($block->id);
            $publishingOptions = $this->blockService->getCurrentPublishingOptions($block->id);

            $lifespanState = $adminControls->calcLifeSpanState($publishingOptions->liveFrom, $publishingOptions->expiresEnd);
            $lifespanTitle = $adminControls->calcLifeSpanTitle($publishingOptions->liveFrom, $publishingOptions->expiresEnd);

            $blockItems[] = array(
                'typeIcon' => $blockMeta->typeIcon,
                'typeName' => $blockMeta->typeName,
                'name' => $blockMeta->name,
                'id' => $block->id,
                'lifespanState' => $lifespanState,
                'lifespanTitle' => $lifespanTitle,
                'allowEdit' => $this->acl->isAllowedMultiRoles(
                    $identityRoles,
                    'default:block',
                    'edit'
                ),
                'allowPublish' => $this->acl->isAllowedMultiRoles(
                    $identityRoles,
                    'default:block',
                    'publish'
                ),
                'allowDelete' => $this->acl->isAllowedMultiRoles(
                    $identityRoles,
                    'default:block',
                    'delete'
                ),
            );
        }

        $this->view->blockItems = $blockItems;

        $this->view->allowCreate = $this->acl->isAllowedMultiRoles(
            $identityRoles,
            'default:block',
            'create'
        );
    }

    /**
     * @return void
     */
    public function createAction()
    {
        $form = new App_Form_BlockCreate(array(
            'request' => $this->getRequest(),
            'blockService' => $this->blockService,
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

        $blockId = $this->blockService->createDraft($values['name'], $values['typeId']);

        $this->_helper->flashMessenger(array(
            'status' => 'success',
            'message' => 'Create successful, add content below.',
        ));

        $this->_helper->redirector('edit', 'block', 'default', array(
            'id' => $blockId,
            'from' => $values['from'],
        ));
    }

    /**
     * @return void
     */
    public function editAction()
    {
        $id = $this->_getParam('id');
        $blockMeta = $this->blockService->getBlockMeta($id);
        $block = $this->blockService->getBlock($id);
        $identity = $this->auth->getIdentity();
        $identityRoles = isset($identity->roles) ? $identity->roles : array();

        if (
            $this->workflowService->getStatus(App_Service_Block::MODULE_NAME, $id) !== App_Service_Workflow::WORKFLOW_STATUS_CURRENT
            && $blockMeta->authorId != $identity->id
        ) {
            throw new Zend_Controller_Exception('User has not authored this draft/revision');
        }

        $this->view->titleSuffix = '';
        if ($this->workflowService->getStatus(App_Service_Block::MODULE_NAME, $id) !== App_Service_Workflow::WORKFLOW_STATUS_CURRENT) {
            $this->view->titleSuffix = $this->workflowService->getStatus(App_Service_Block::MODULE_NAME, $id);
        }

        $this->view->typeName = $blockMeta->typeName;
        $this->view->blockName = $blockMeta->name;
        $this->view->blockNotes = $blockMeta->notes;

        $typePart = Zend_Filter::filterStatic($blockMeta->typeName, 'Word_DashToCamelCase');
        $formClass = 'App_Form_' . ucfirst($typePart) . 'BlockBuilder';
        $form = new $formClass(array(
            'request' => $this->getRequest(),
        ));
        $this->view->form = $form;

        if (!$this->getRequest()->isPost()) {

            $form->populateFromBlockDto($block);
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

        $block = new App_Service_Dto_Block();

        foreach ($values['fields'] as $name => $value) {

            $field = new App_Service_Dto_BlockField();
            $field->name = $name;
            $field->value = $value;

            $block->fields[] = $field;
        }

        if (null !== $form->getUnfilteredValue('save')) {

            $editId = $id;

            if ($this->workflowService->getStatus(App_Service_Block::MODULE_NAME, $id) === App_Service_Workflow::WORKFLOW_STATUS_CURRENT) {
                $editId = $this->blockService->createRevision($id);
            }

            $this->blockService->edit($editId, $block, $values['note']);

            $this->_helper->flashMessenger(array(
                'status' => 'success',
                'message' => 'Save successful.',
            ));

            $this->_helper->redirector('authoring', 'workflow');
        }

        if (null !== $form->getUnfilteredValue('publish')) {

            $canPublish = $this->acl->isAllowedMultiRoles(
                $identityRoles,
                'default:block',
                'publish'
            );

            if (!$canPublish) {

                $editId = $id;

                if ($this->workflowService->getStatus(App_Service_Block::MODULE_NAME, $id) === App_Service_Workflow::WORKFLOW_STATUS_CURRENT) {
                    $editId = $this->blockService->createRevision($id);
                }

                $this->blockService->edit($editId, $block, $values['note']);
                $this->workflowService->moveToPublishing(App_Service_Block::MODULE_NAME, $editId);

                $this->_helper->flashMessenger(array(
                    'status' => 'success',
                    'message' => 'Save successful, content moved to publishing for approval.',
                ));

                $this->_helper->redirector('authoring', 'workflow');
            }

            switch ($this->workflowService->getStatus(App_Service_Block::MODULE_NAME, $id)) {

                case App_Service_Workflow::WORKFLOW_STATUS_CURRENT:

                    $revisionId = $this->blockService->createRevision($id);

                    $this->blockService->edit($revisionId, $block, $values['note']);
                    $this->blockService->publish($revisionId);

                    $this->_helper->flashMessenger(array(
                        'status' => 'success',
                        'message' => 'Update successful.',
                    ));

                    $this->_helper->redirector('index');
                    break;

                case App_Service_Workflow::WORKFLOW_STATUS_UPDATE:

                    $this->blockService->edit($id, $block, $values['note']);
                    $this->blockService->publish($id);

                    $this->_helper->flashMessenger(array(
                        'status' => 'success',
                        'message' => 'Update successful.',
                    ));

                    $this->_helper->redirector('authoring', 'workflow');
                    break;

                case App_Service_Workflow::WORKFLOW_STATUS_NEW:

                    $this->blockService->edit($id, $block, $values['note']);
                    $this->workflowService->moveToPublishing(App_Service_Block::MODULE_NAME, $id);

                    $this->_helper->flashMessenger(array(
                        'status' => 'success',
                        'message' => 'Save successful, please set options below to complete publishing process.',
                    ));

                    $this->_helper->redirector('publish', 'block', 'default', array('id' => $id));
                    break;

                default:
                    throw new Zend_Controller_Exception('Workflow status unknown');
            }
        }
    }

    /**
     * @return void
     */
    public function publishAction()
    {
        $id = $this->_getParam('id');
        $blockMeta = $this->blockService->getBlockMeta($id);
        $type = $this->blockService->getType($blockMeta->typeId);

        $currentPublishingOptions = null;
        if ($this->workflowService->getStatus(App_Service_Block::MODULE_NAME, $id) === App_Service_Workflow::WORKFLOW_STATUS_CURRENT) {
            $currentPublishingOptions = $this->blockService->getCurrentPublishingOptions($id);
        }

        $this->view->typeName = $blockMeta->typeName;
        $this->view->blockName = $blockMeta->name;
        $this->view->blockNotes = $blockMeta->notes;

        foreach ($type->templates as $template) {

            if ($template->id == $this->_getParam('templateId')) {
                $this->view->templateDescription = $template->description;
            }
        }

        $form = new App_Form_BlockPublish(array(
            'request' => $this->getRequest(),
            'blockService' => $this->blockService,
        ));

        $this->view->form = $form;

        if (!$this->getRequest()->isPost()) {

            $form->populate(array(
                'name' => $blockMeta->name,
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
        $publishingOptions->liveFrom = new DateTime($values['liveFrom']);
        $publishingOptions->expiresEnd = new DateTime($values['expiresEnd']);
        $publishingOptions->templateId = $values['templateId'];

        $this->blockService->publish($id, $publishingOptions);

        $this->_helper->flashMessenger(array(
            'status' => 'success',
            'message' => 'Publishing successful.',
        ));

        $this->_helper->redirector('index');
    }

    /**
     * @return void
     */
    public function deleteAction()
    {
        $form = new App_Form_Confirm(array(
            'request' => $this->getRequest(),
        ));
        $form->setAction('/block/delete');
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

        $this->blockService->delete($values['id']);

        $this->_helper->flashMessenger(array(
            'status' => 'success',
            'message' => 'Delete successful.',
        ));

        $this->_helper->redirector('index');
    }

    /**
     * @return void
     */
    public function publishUpdateAction()
    {
        $form = new App_Form_Confirm(array(
            'request' => $this->getRequest(),
        ));
        $form->setAction('/block/publish-update');
        $form->getElement('confirm')->setLabel('Confirm update');
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
        $this->blockService->publish($values['id']);

        $this->_helper->flashMessenger(array(
            'status' => 'success',
            'message' => 'Update successful.',
        ));

        $this->_helper->redirector('publishing', 'workflow');
    }

}
