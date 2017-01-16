<?php

class WorkflowController extends Zend_Controller_Action
{

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

    }

    /**
     * @return void
     */
    public function authoringAction()
    {
        $adminControls = $this->_helper->getHelper('AdminControls');
        $this->view->adminMenuControl = $adminControls->createAdminMenuControl();
        $this->view->createDropdown = $adminControls->createCreateInWorkflowDropdown();

        $adapter = new ZendExt_Paginator_Adapter_Callback(
            function ($offset, $itemCountPerPage) {
                return $this->workflowService->getContentInAuthoring($offset, $itemCountPerPage);
            },
            function () {
                return $this->workflowService->countContentInAuthoring();
            }
        );

        $paginator = new Zend_Paginator($adapter);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage($this->config->settings->adminShowPerPage);
        $this->view->paginator = $paginator;

        $authoringItems = $this->_createPartialValues($paginator);
        $this->view->authoringItems = $authoringItems;
    }

    /**
     * @return void
     */
    public function authoringDeleteAction()
    {
        $this->_handleDelete('authoring');
        $this->_helper->viewRenderer('confirm');
    }

    /**
     * @return void
     */
    public function publishingDeleteAction()
    {
        $this->_handleDelete('publishing');
        $this->_helper->viewRenderer('confirm');
    }

    /**
     * @param string $stage
     * @return void
     */
    protected function _handleDelete($stage)
    {
        $form = new App_Form_Confirm(array(
            'request' => $this->getRequest(),
        ));
        $form->setAction('/workflow/' . $stage . '-delete');
        $form->getElement('confirm')->setLabel('Confirm delete');
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

        $this->workflowService->delete($values['moduleName'], $values['id']);

        $this->_helper->flashMessenger(array(
            'status' => 'success',
            'message' => 'Delete successful.',
        ));

        $this->_helper->redirector($stage, 'workflow');
    }

    /**
     * @return void
     */
    public function publishingAction()
    {
        $adminControls = $this->_helper->getHelper('AdminControls');
        $this->view->adminMenuControl = $adminControls->createAdminMenuControl();

        $adapter = new ZendExt_Paginator_Adapter_Callback(
            function ($offset, $itemCountPerPage) {
                return $this->workflowService->getContentInPublishing($offset, $itemCountPerPage);
            },
            function () {
                return $this->workflowService->countContentInPublishing();
            }
        );

        $paginator = new Zend_Paginator($adapter);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage($this->config->settings->adminShowPerPage);
        $this->view->paginator = $paginator;

        $publishingItems = $this->_createPartialValues($paginator);
        $this->view->publishingItems = $publishingItems;
    }

    /**
     * @return void
     */
    public function sendBackAction()
    {
        $moduleName = $this->_getParam('moduleName');
        $id = $this->_getParam('id');
        $notes = $this->_getParam('notes');

        $this->workflowService->sendBackToAuthor($moduleName, $id, $notes);

        $this->_helper->flashMessenger(array(
            'status' => 'success',
            'message' => 'Content sent back to author successfully.',
        ));

        $this->_helper->redirector('publishing', 'workflow');
    }

    /**
     *
     * @param Zend_Paginator $paginator
     * @return array
     */
    protected function _createPartialValues(Zend_Paginator $paginator)
    {
        $items = array();

        foreach ($paginator as $item) {

            $items[] = array(
                'typeIcon' => $item->typeIcon,
                'typeName' => $item->typeName,
                'name' => $item->name,
                'id' => $item->id,
                'workflowStatus' => $item->workflowStatus,
                'workflowStage' => $item->workflowStage,
                'authoredTime' => $item->authoredTime,
                'authorUsername' => $item->authorUsername,
                'controllerName' => $item->controllerName,
                'actionName' => $item->actionName,
                'notes' => $item->notes,
                'availableTemplates' => $item->availableTemplates,
            );
        }

        return $items;
    }

}
