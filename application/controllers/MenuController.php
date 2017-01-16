<?php

class MenuController extends Zend_Controller_Action
{

    /**
     * @var App_Service_Standalone
     */
    public $standaloneService;

    /**
     * @var App_Service_Menu
     */
    public $menuService;

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

        $this->menuService = $container['MenuService'];
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
        $menu = $this->menuService->getMenu();
        $identity = $this->auth->getIdentity();
        $identityRoles = isset($identity->roles) ? $identity->roles : array();

        $adminControls = $this->_helper->getHelper('AdminControls');
        $this->view->adminMenuControl = $adminControls->createAdminMenuControl();
        $this->view->createAtTopLevelDropdown = $adminControls->createCreateInMenuDropdown(0);

        $menuItems = array();
        $items = array();

        $this->_flattenMenuRecursive($menu->items, $items);

        foreach ($items as $item) {

            $createBeneathDropdown = $adminControls->createCreateInMenuDropdown($item->menuItemId);

            if ($item->module) {
                $item->controllerName = 'item';
            }

            $menuItem = array(
                'external' => $item->external,
                'module' => $item->module,
                'createBeneathDropdown' => $createBeneathDropdown,
                'level' => $item->level,
                'first' => $item->first,
                'last' => $item->last,
                'typeIcon' => $item->typeIcon,
                'typeName' => $item->typeName,
                'controllerName' => $item->controllerName,
                'name' => $item->slug,
                'id' => $item->identifier,
                'menuItemId' => $item->menuItemId,
                'numChildMenuItems' => $item->numChildMenuItems,
                'maxMenuLevels' => $this->config->settings->maxMenuLevels,
                'allowViewFormSubmissions' => false,
                'allowShuffleMenu' => $this->acl->isAllowedMultiRoles(
                    $identityRoles,
                    'default:menu',
                    'shuffle'
                ),
            );

            if (!$item->external && !$item->module) {

                $lifespanState = $adminControls->calcLifeSpanState($item->liveFrom, $item->expiresEnd);
                $lifespanTitle = $adminControls->calcLifeSpanTitle($item->liveFrom, $item->expiresEnd);

                $menuItem['lifespanState'] = $lifespanState;
                $menuItem['lifespanTitle'] = $lifespanTitle;
                $menuItem['allowEdit'] = $this->acl->isAllowedMultiRoles(
                    $identityRoles,
                    'default:' . $item->controllerName,
                    'edit'
                );
                $menuItem['allowPublish'] = $this->acl->isAllowedMultiRoles(
                    $identityRoles,
                    'default:' . $item->controllerName,
                    'publish'
                );
                $menuItem['allowDelete'] = $this->acl->isAllowedMultiRoles(
                    $identityRoles,
                    'default:' . $item->controllerName,
                    'delete'
                );
            }

            if (!$item->external) {

                $menuItem['allowCreate'] = $this->acl->isAllowedMultiRoles(
                    $identityRoles,
                    'default:item',
                    'create'
                );
            }

            $menuItems[] = $menuItem;
        }

        $this->view->menuItems = $menuItems;

        $this->view->allowCreateItem = $this->acl->isAllowedMultiRoles(
            $identityRoles,
            'default:item',
            'create'
        );
    }

    /**
     * @param App_Service_Dto_MenuItem[] $items
     * @param App_Service_Dto_MenuItem[] $return
     * @return void
     */
    protected function _flattenMenuRecursive($items, &$return)
    {
        foreach ($items as $item) {

            $return[] = $item;

            if (count($item->items)) {
                $this->_flattenMenuRecursive($item->items, $return);
            }
        }
    }

    /**
     * @return void
     */
    public function shuffleAction()
    {
        $id = $this->_getParam('id');
        $direction = $this->_getParam('direction');

        $this->menuService->moveItem($id, $direction);

        $this->_helper->flashMessenger(array(
            'status' => 'success',
            'message' => 'Item moved successfully.',
        ));

        $this->_helper->redirector('index');
    }

    /**
     * @return void
     */
    public function internalLinksAction()
    {
        $navigation = $this->_helper->getHelper('Navigation')->createFrontendNavigation();
        $this->view->getHelper('navigation')->setContainer($navigation);

        $this->view->standaloneItems = $this->standaloneService->getPublishedStandalone();
    }

}
