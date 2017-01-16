<?php

class StandaloneController extends Zend_Controller_Action
{

    /**
     * @var App_Service_Standalone
     */
    public $standaloneService;

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

        $this->standaloneService = $container['StandaloneService'];
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
        $this->view->createDropdown = $adminControls->createCreateStandaloneDropdown();

        $adapter = new ZendExt_Paginator_Adapter_Callback(
            function ($offset, $itemCountPerPage) {
                return $this->standaloneService->getPublishedStandalone($offset, $itemCountPerPage);
            },
            function () {
                return $this->standaloneService->countPublishedStandalone();
            }
        );

        $paginator = new Zend_Paginator($adapter);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage($this->config->settings->adminShowPerPage);
        $this->view->paginator = $paginator;

        $standaloneItems = array();

        foreach ($paginator as $item) {

            $lifespanState = $adminControls->calcLifeSpanState($item->liveFrom, $item->expiresEnd);
            $lifespanTitle = $adminControls->calcLifeSpanTitle($item->liveFrom, $item->expiresEnd);

            $standaloneItems[] = array(
                'typeIcon' => $item->typeIcon,
                'typeName' => $item->typeName,
                'controllerName' => $item->controllerName,
                'name' => $item->name,
                'id' => $item->id,
                'lifespanState' => $lifespanState,
                'lifespanTitle' => $lifespanTitle,
                'allowEdit' => $this->acl->isAllowedMultiRoles(
                    $identityRoles,
                    'default:' . $item->controllerName,
                    'edit'
                ),
                'allowPublish' => $this->acl->isAllowedMultiRoles(
                    $identityRoles,
                    'default:' . $item->controllerName,
                    'publish'
                ),
                'allowDelete' => $this->acl->isAllowedMultiRoles(
                    $identityRoles,
                    'default:' . $item->controllerName,
                    'delete'
                ),
                'allowViewFormSubmissions' => false,
            );
        }

        $this->view->standaloneItems = $standaloneItems;

        $this->view->allowCreateItem = $this->acl->isAllowedMultiRoles(
            $identityRoles,
            'default:item',
            'create'
        );
    }

}
