<?php

class FormController extends Zend_Controller_Action
{

    /**
     * @var App_Service_Block
     */
    public $blockService;

    /**
     * @var App_Service_ModulePage
     */
    public $modulePageService;

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
    protected function _initBackendAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->security();
    }

    /**
     * @return void
     */
    public function contactUsAction()
    {
        $modulePageId = 1;
        $publishingOptions = $this->_getPublishingOptions($modulePageId);

        $recipient = $this->_getRecipient();
        $this->_handleContactForm($recipient, 'Contact Form Submission');

        $this->_setupAdminAndNavigationControls($modulePageId);
        $this->_helper->assignBlocks($publishingOptions);
    }

    /**
     * @return void
     */
    public function thankYouAction()
    {
        $navigation = $this->_helper->getHelper('Navigation')->createFrontendNavigation();
        $this->view->getHelper('navigation')->setContainer($navigation);

        $this->view->navText = 'Thank You';
        $this->view->isStandalone = true;
    }

    /**
     * @return string
     */
    protected function _getRecipient()
    {
        $formConfig = lcfirst(Zend_Filter::filterStatic($this->getRequest()->getActionName(), 'Word_DashToCamelCase'));
        $formConfig = $this->config->forms->$formConfig;
        return $formConfig->recipient;
    }

    /**
     * @param string $recipient
     * @param string $subject
     * @return void
     */
    protected function _handleContactForm($recipient, $subject)
    {
        $formPart = Zend_Filter::filterStatic($this->getRequest()->getActionName(), 'Word_DashToCamelCase');
        $formClass = 'App_Form_' . ucfirst($formPart);
        $form = new $formClass(array(
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

        $values = $form->getValues();

        if (
            !array_key_exists('email', $values)
            || !array_key_exists('name', $values)
        ) {
            throw new Zend_Controller_Action_Exception('name and email values required');
        }

        $body = '';

        foreach ($form->getElements() as $element) {

            $value = $element->getValue();

            if (is_array($value)) {
                $value = implode(', ', $value);
            }

            $body .= $element->getLabel() . ': ' . $value . PHP_EOL;
        }

        $mail = new Zend_Mail();
        $mail->setFrom($values['email'], $values['name']);
        $mail->addTo($recipient);
        $mail->setSubject($subject);
        $mail->setBodyText($body);

        if (!$mail->send()) {

            $this->_helper->flashMessenger(array(
                'status' => 'error',
                'message' => 'Submission failed',
            ));
            return;
        }

        $this->_helper->redirector('thank-you');
    }

    /**
     * @param int $modulePageId
     * @return App_Service_Dto_PublishingOptions
     */
    protected function _getPublishingOptions($modulePageId)
    {
        $preview = $this->_getParam('preview');
        $identity = $this->auth->getIdentity();
        $identityRoles = isset($identity->roles) ? $identity->roles : array();

        $canPublish = $this->acl->isAllowedMultiRoles(
            $identityRoles,
            'default:form',
            'publish'
        );

        if ('publishing' === $preview && $canPublish) {

            // Previewing publishing
            return $this->previewSession->publishing;

        } else {
            return $this->modulePageService->getCurrentPublishingOptions($modulePageId);
        }
    }

    /**
     * @param int $modulePageId
     * @return void
     */
    protected function _setupAdminAndNavigationControls($modulePageId)
    {
        if (!$this->_getParam('preview')) {

            $adminControls = $this->_helper->getHelper('AdminControls');
            $this->view->adminMenuControl = $adminControls->createAdminMenuControl(true);
            $this->view->adminControl = $adminControls->createFrontendModulePageControl(
                'form',
                $this->getRequest()->getActionName(),
                $modulePageId,
                true
            );
        }

        $navigation = $this->_helper->getHelper('Navigation')->createFrontendNavigation();
        $this->view->getHelper('navigation')->setContainer($navigation);
    }

    /**
     * @return void
     */
    public function publishAction()
    {
        $this->_initBackendAction();

        $id = $this->_getParam('id');
        $modulePage = $this->modulePageService->getModulePage($id);

        $this->view->moduleName = 'form';
        $this->view->pageName = $modulePage->name;

        $form = new App_Form_ModulePagePublish(array(
            'request' => $this->getRequest(),
            'modulePageService' => $this->modulePageService,
        ));
        $form->setAction('/form/publish');

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

            $this->_redirect($this->config->settings->host . '/form/' . $modulePage->name);

        } else {

            // Preview
            $this->previewSession->publishing = $publishingOptions;
            $this->view->preview = true;
        }
    }

}
