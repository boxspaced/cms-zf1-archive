<?php

class AssetController extends Zend_Controller_Action
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

}
