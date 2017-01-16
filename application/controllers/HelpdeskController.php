<?php

class HelpdeskController extends Zend_Controller_Action
{

    /**
     * @var App_Service_Helpdesk
     */
    public $helpdeskService;

    /**
     * @var Zend_Auth
     */
    public $auth;

    /**
     * @var App_Acl_Acl
     */
    public $acl;

    /**
     * @var Zend_Config
     */
    public $config;

    /**
     * @return void
     */
    public function init()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        $container = $bootstrap->getContainer();

        $this->auth = $container['Auth'];
        $this->acl = $container['Acl'];
        $this->helpdeskService = $container['HelpdeskService'];

        $this->_helper->bootstrapResourceInjector();
        $this->_helper->layout->disableLayout();
        $this->_helper->security();
    }

    /**
     * @return void
     */
    public function indexAction()
    {
        $adminControls = $this->_helper->getHelper('AdminControls');
        $this->view->adminMenuControl = $adminControls->createAdminMenuControl();

        $adapter = new ZendExt_Paginator_Adapter_Callback(
            function ($offset, $itemCountPerPage) {
                return $this->helpdeskService->getOpenTickets($offset, $itemCountPerPage);
            },
            function () {
                return $this->helpdeskService->countOpenTickets();
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
    public function createTicketAction()
    {
        $adminControls = $this->_helper->getHelper('AdminControls');
        $this->view->adminMenuControl = $adminControls->createAdminMenuControl();

        $form = new App_Form_HelpdeskTicket(array(
            'request' => $this->getRequest(),
            'attachmentUploadDirectory' => $this->config->settings->helpdeskAttachmentsDirectory,
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

        if ($form->attachment->isUploaded()) {
            $form->attachment->receive();
        }

        $dto = new App_Service_Dto_HelpdeskTicket();
        $dto->subject = $values['subject'];
        $dto->issue = $values['issue'];

        $this->helpdeskService->createNewTicket($dto, $values['attachment']);

        $this->_helper->flashMessenger(array(
            'status' => 'success',
            'message' => 'Create ticket successful.',
        ));

        $this->_helper->redirector('index');
    }

    /**
     * @return void
     */
    public function viewTicketAction()
    {
        $id = $this->_getParam('id');
        $ticket = $this->helpdeskService->getTicket($id);
        $identity = $this->auth->getIdentity();
        $identityRoles = isset($identity->roles) ? $identity->roles : array();

        $this->view->ticket = $ticket;

        $canResolve = $this->acl->isAllowedMultiRoles(
            $identityRoles,
            'default:helpdesk',
            'resolve-ticket'
        );
        $this->view->canResolve = $canResolve;

        $adminControls = $this->_helper->getHelper('AdminControls');
        $this->view->adminMenuControl = $adminControls->createAdminMenuControl();

        $form = new App_Form_HelpdeskComment(array(
            'request' => $this->getRequest(),
            'attachmentUploadDirectory' => $this->config->settings->helpdeskAttachmentsDirectory,
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

        if ($form->attachment->isUploaded()) {
            $form->attachment->receive();
        }

        if (!is_null($form->getUnfilteredValue('resolve')) && $canResolve) {
            $this->helpdeskService->resolveTicket($id, $values['comment'], $values['attachment']);
            $message = 'Ticket resolved.';
        } else {
            $this->helpdeskService->addCommentToTicket($id, $values['comment'], $values['attachment']);
            $message = 'Comment added to ticket.';
        }

        $this->_helper->flashMessenger(array(
            'status' => 'success',
            'message' => $message,
        ));

        $this->_helper->redirector('index');
    }

    /**
     * @return void
     */
    public function viewAttachmentAction()
    {
        $filename = basename($this->_getParam('fileName'));
        $filePath = $this->config->settings->helpdeskAttachmentsDirectory . DIRECTORY_SEPARATOR . $filename;

        $mime = image_type_to_mime_type(exif_imagetype($filePath));

        header('Content-Type: ' . $mime);
        readfile($filePath);
        exit;
    }

}
