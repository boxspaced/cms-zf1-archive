<?php

class Controller_Helper_Navigation extends Zend_Controller_Action_Helper_Abstract
{

    /**
     * @var App_Service_Menu
     */
    public $_menuService;

    /**
     * @return void
     */
    public function init()
    {
        $front = Zend_Controller_Front::getInstance();
        $bootstrap = $front->getParam('bootstrap');
        $container = $bootstrap->getContainer();
        $controller = $this->getActionController();

        $this->_menuService = $container['MenuService'];
    }

    /**
     * @return Zend_Navigation
     */
    public function createFrontendNavigation()
    {
        $menu = $this->_menuService->getMenu();
        $pages = $this->_createNavigationPagesRecursive($menu->items);

        return new Zend_Navigation($pages);
    }

    /**
     * @param App_Service_Dto_MenuItem[] $items
     * @return array
     */
    protected function _createNavigationPagesRecursive(array $items)
    {
        $return = array();

        $hide = array(
            'digital-gallery-results',
            'digital-gallery-image',
            'digital-gallery-basket',
            'course-results',
            'course-details',
            'whats-on-results',
            'whats-on-details',
        );

        foreach ($items as $item) {

            $page = null;

            if ($item->external) {

                $page = array(
                    'label' => $item->navText,
                    'uri' => $item->slug,
                );

            } elseif ($item->module) {

                $page = array(
                    'label' => $item->navText,
                    'route' => $item->slug,
                    'controller' => $item->controllerName,
                    'action' => $item->identifier,
                );

            } elseif ($this->_isLive($item->liveFrom, $item->expiresEnd)) {

                $page = array(
                    'label' => $item->navText,
                    'route' => $item->slug,
                    'controller' => $item->controllerName,
                    'action' => $item->actionName,
                    'params' => array(
                        'id' => $item->identifier,
                    ),
                );
            }

            if (null === $page) {
                continue;
            }

            if (in_array($item->slug, $hide)) {
                $page['class'] = 'hidden-menu-item';
            }

            if ($item->items) {
                $page['pages'] = $this->_createNavigationPagesRecursive($item->items);
            }

            $return[] = $page;
        }

        return $return;
    }

    /**
     * @param DateTime $liveFrom
     * @param DateTime $expiresEnd
     * @return bool
     */
    protected function _isLive(DateTime $liveFrom, DateTime $expiresEnd)
    {
        $now = new DateTime();
        return ($liveFrom < $now && $expiresEnd > $now);
    }

}
