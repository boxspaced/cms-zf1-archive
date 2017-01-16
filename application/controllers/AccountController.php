<?php

class AccountController extends Zend_Controller_Action
{

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
    }

    /**
     * @return void
     */
    public function loginAction()
    {
        $form = new App_Form_AccountLogin(array(
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

        // @todo create Auth_Adapter for calling a service to authenticate
        $adapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
        $adapter->setTableName('user');
        $adapter->setIdentityColumn('username');
        $adapter->setCredentialColumn('password');
        $adapter->setIdentity($values['username']);
        $adapter->setCredential(hash($this->config->settings->passwordHashingAlgorithm, $values['password']));

        $result = $this->auth->authenticate($adapter);

        if (!$result->isValid()) {

            $this->_helper->flashMessenger(array(
                'status' => 'error',
                'message' => 'The credentials provided are incorrect.',
            ));
            return;
        }

        $data = $adapter->getResultRowObject(null, 'password');

        // @todo getRoles() operation in a service
        $adapter = Zend_Db_Table::getDefaultAdapter();
        $select = $adapter->select();
        $select->from('user_role');
        $select->joinInner('role', 'role.id = user_role.role_id');
        $select->where('user_role.user_id = ?', $data->id);

        $roles = $adapter->fetchAll($select);

        $data->roles = array();

        foreach ($roles as $role) {
            $data->roles[] = $role['name'];
        }

        $this->auth->getStorage()->write($data);

        if ($values['requestUri'] === '/account/login') {
            $this->_helper->redirector('index', 'account');
        } else {
            $this->_redirect($values['requestUri']);
        }
    }

    /**
     * @return void
     */
    public function logoutAction()
    {
        $this->auth->clearIdentity();

        $this->_helper->flashMessenger(array(
            'status' => 'success',
            'message' => 'Logout successful.',
        ));

        $this->_helper->redirector('login', 'account');
    }

    /**
     * @return void
     */
    public function accessDeniedAction()
    {

    }

}
