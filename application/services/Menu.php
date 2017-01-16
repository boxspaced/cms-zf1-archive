<?php

class App_Service_Menu
{

    const MOVE_DIRECTION_UP = 'up';
    const MOVE_DIRECTION_DOWN = 'down';
    const MENU_CACHE_ID = 'menu';

    /**
     * @var Zend_Cache_Manager
     */
    protected $_cacheManager;

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_adapter;

    /**
     * @var Zend_Auth
     */
    protected $_auth;

    /**
     * @var App_Domain_User
     */
    protected $_user;

    /**
     * @var \Boxspaced\EntityManager\EntityManager
     */
    protected $_entityManager;

    /**
     * @var App_Domain_Repository_User
     */
    protected $_userRepository;

    /**
     * @var App_Domain_Repository_Menu
     */
    protected $_menuRepository;

    /**
     * @param Zend_Cache_Manager $cacheManager
     * @param Zend_Db_Adapter_Abstract $adapter
     * @param Zend_Auth $auth
     * @param \Boxspaced\EntityManager\EntityManager $entityManager
     * @param App_Domain_Repository_User $userRepository
     * @param App_Domain_Repository_Menu $menuRepository
     */
    public function __construct(
        Zend_Cache_Manager $cacheManager,
        Zend_Db_Adapter_Abstract $adapter,
        Zend_Auth $auth,
        \Boxspaced\EntityManager\EntityManager $entityManager,
        App_Domain_Repository_User $userRepository,
        App_Domain_Repository_Menu $menuRepository
    )
    {
        $this->_cacheManager = $cacheManager;
        $this->_adapter = $adapter;
        $this->_auth = $auth;
        $this->_entityManager = $entityManager;
        $this->_userRepository = $userRepository;
        $this->_menuRepository = $menuRepository;

        if ($this->_auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            $this->_user = $userRepository->getById($identity->id);
        }
    }

    /**
     * @return App_Service_Dto_Menu
     */
    public function getMenu()
    {
        $cache = $this->_cacheManager->getCache('long');

        if (false !== $cache->test(static::MENU_CACHE_ID)) {
            return $cache->load(static::MENU_CACHE_ID);
        }

        $menu = $this->_menuRepository->getByName('main');

        $dto = new App_Service_Dto_Menu();
        $dto->name = $menu->getName();
        $dto->items = $this->_assembleRecursive($menu->getItems());

        $cache->save($dto);

        return $dto;
    }

    /**
     * @param \Boxspaced\EntityManager\Collection\Collection $menuItems
     * @param int $level
     * @return App_Service_Dto_MenuItem[]
     */
    protected function _assembleRecursive(\Boxspaced\EntityManager\Collection\Collection $menuItems, $level = 1)
    {
        $array = array();

        $total = count($menuItems);
        $count = 1;

        foreach ($menuItems as $menuItem) {

            $dto = new App_Service_Dto_MenuItem();

            if ($menuItem->getExternal()) {

                $dto->external = true;
                $dto->slug = $menuItem->getExternal();
                $dto->navText = $menuItem->getNavText();
                $dto->typeIcon = '/images/icons/system_page.png';
                $dto->typeName = 'external';
                $dto->menuItemId = $menuItem->getId();
                $dto->numChildMenuItems = count($menuItem->getItems());
                $dto->level = (int) $level;
                $dto->first = ($count === 1);
                $dto->last = ($count === $total);

                if (count($menuItem->getItems())) {
                    $dto->items = $this->_assembleRecursive($menuItem->getItems(), $level+1);
                }

            } else {

                $identifier = $menuItem->getRoute()->getIdentifier();

                if (is_numeric($identifier)) {

                    $contentItem = $this->_getContentByMenuItem($menuItem);
                    $dto->navText = $contentItem->getNavText();
                    $dto->typeIcon = $contentItem->getType()->getIcon();
                    $dto->typeName = $contentItem->getType()->getName();
                    $dto->liveFrom = $contentItem->getLiveFrom();
                    $dto->expiresEnd = $contentItem->getExpiresEnd();

                } else {

                    $dto->module = true;
                    $dto->navText = $menuItem->getNavText();
                    $dto->typeName = $menuItem->getRoute()->getModule()->getName() . ' page';
                    $dto->typeIcon = '/images/icons/system_page.png';
                }

                $dto->controllerName = $menuItem->getRoute()->getModule()->getRouteController();
                $dto->actionName = $menuItem->getRoute()->getModule()->getRouteAction();
                $dto->identifier = $identifier;
                $dto->menuItemId = $menuItem->getId();
                $dto->slug = $menuItem->getRoute()->getSlug();
                $dto->numChildMenuItems = count($menuItem->getItems());
                $dto->level = (int) $level;
                $dto->first = ($count === 1);
                $dto->last = ($count === $total);

                if (count($menuItem->getItems())) {
                    $dto->items = $this->_assembleRecursive($menuItem->getItems(), $level+1);
                }
            }

            $array[] = $dto;
            $count++;
        }

        return $array;
    }

    /**
     * @param App_Domain_MenuItem $menuItem
     * @return \Boxspaced\EntityManager\Entity\AbstractEntity
     */
    protected function _getContentByMenuItem(App_Domain_MenuItem $menuItem)
    {
        $route = $menuItem->getRoute();

        if (is_numeric($route->getIdentifier())) {

            $module = $route->getModule();

            $entityName = rtrim($module->getName(), 's');
            $entityName = ucfirst(Zend_Filter::filterStatic($entityName, 'Word_DashToCamelCase'));

            return $this->_entityManager->find('App_Domain_' . $entityName, $route->getIdentifier());
        }

        return null;
    }

    /**
     * @param int $id
     * @param string $direction
     * @return void
     */
    public function moveItem($id, $direction)
    {
        if (!in_array($direction, array(
            static::MOVE_DIRECTION_UP,
            static::MOVE_DIRECTION_DOWN,
        ))) {
            throw new App_Service_Exception('Invalid direction');
        }

        $menu = $this->_menuRepository->getByName('main');

        $flattenedMenu = array();
        $this->_flattenMenuRecursive($menu->getItems(), $flattenedMenu);

        $itemToMove = array_shift(
            array_filter($flattenedMenu, function($flattenedMenuItem) use ($id) {
                return ($flattenedMenuItem->getId() == $id);
            })
        );

        if (null === $itemToMove) {
            throw new App_Service_Exception('Menu item not found with given id');
        }

        // Get all siblings
        if ($itemToMove->getParentMenuItem()) {
            $siblings = $itemToMove->getParentMenuItem()->getItems();
        } else {
            $siblings = $menu->getItems();
        }

        // Get next and prev
        $current = $siblings->rewind()->current();

        while ($current !== null) {

            if ($current === $itemToMove) {

                $prevSibling = $siblings->prev();

                if (!$prevSibling) {
                    $siblings->first();
                } else {
                    $siblings->next(); // Back to item to move
                }

                $nextSibling = $siblings->next();
                break;
            }

            $current = $siblings->next();
        }

        // Move
        if ($direction === static::MOVE_DIRECTION_UP) {

            if ($prevSibling) {

                $prevSiblingOrderBy = $prevSibling->getOrderBy();
                $itemToMoveOrderBy = $itemToMove->getOrderBy();

                $itemToMove->setOrderBy($prevSiblingOrderBy);
                $prevSibling->setOrderBy($itemToMoveOrderBy);

            } else {
                throw new App_Service_Exception('Menu item can not be moved up any further');
            }

        } elseif ($direction === static::MOVE_DIRECTION_DOWN) {

            if ($nextSibling) {

                $nextSiblingOrderBy = $nextSibling->getOrderBy();
                $itemToMoveOrderBy = $itemToMove->getOrderBy();

                $itemToMove->setOrderBy($nextSiblingOrderBy);
                $nextSibling->setOrderBy($itemToMoveOrderBy);

            } else {
                throw new App_Service_Exception('Menu item can not be moved down any further');
            }
        }

        $this->_entityManager->flush();

        // Clear cache
        $cache = $this->_cacheManager->getCache('long');
        $cache->remove(static::MENU_CACHE_ID);
    }

    /**
     * @param \Boxspaced\EntityManager\Collection\Collection $items
     * @param array $flattened
     * @return void
     */
    protected function _flattenMenuRecursive(\Boxspaced\EntityManager\Collection\Collection $items, array &$flattened)
    {
        foreach ($items as $item) {

            $flattened[] = $item;

            if (count($item->getItems())) {
                $this->_flattenMenuRecursive($item->getItems(), $flattened);
            }
        }
    }

}
