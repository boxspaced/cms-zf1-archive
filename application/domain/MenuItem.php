<?php

class App_Domain_MenuItem extends \Boxspaced\EntityManager\Entity\AbstractEntity
{

    /**
     * @return int
     */
    public function getId()
    {
        return $this->get('id');
    }

    /**
     * @param int $id
     * @return App_Domain_MenuItem
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return App_Domain_Menu
     */
    public function getMenu()
    {
        return $this->get('menu');
    }

    /**
     * @param App_Domain_Menu $menu
     * @return App_Domain_MenuItem
     */
    public function setMenu(App_Domain_Menu $menu)
    {
        $this->set('menu', $menu);
		return $this;
    }

    /**
     * @return App_Domain_MenuItem
     */
    public function getParentMenuItem()
    {
        return $this->get('parentMenuItem');
    }

    /**
     * @param App_Domain_MenuItem $parentMenuItem
     * @return App_Domain_MenuItem
     */
    public function setParentMenuItem(App_Domain_MenuItem $parentMenuItem)
    {
        $this->set('parentMenuItem', $parentMenuItem);
		return $this;
    }

    /**
     * @return int
     */
    public function getOrderBy()
    {
        return $this->get('orderBy');
    }

    /**
     * @param int $orderBy
     * @return App_Domain_MenuItem
     */
    public function setOrderBy($orderBy)
    {
        $this->set('orderBy', $orderBy);
		return $this;
    }

    /**
     * @return App_Domain_Route
     */
    public function getRoute()
    {
        return $this->get('route');
    }

    /**
     * @param App_Domain_Route $route
     * @return App_Domain_MenuItem
     */
    public function setRoute(App_Domain_Route $route)
    {
        $this->set('route', $route);
		return $this;
    }

    /**
     * @return string
     */
    public function getNavText()
    {
        return $this->get('navText');
    }

    /**
     * @param string $navText
     * @return App_Domain_MenuItem
     */
    public function setNavText($navText)
    {
        $this->set('navText', $navText);
		return $this;
    }

    /**
     * @return string
     */
    public function getExternal()
    {
        return $this->get('external');
    }

    /**
     * @param string $external
     * @return App_Domain_MenuItem
     */
    public function setExternal($external)
    {
        $this->set('external', $external);
		return $this;
    }

    /**
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getItems()
    {
        return $this->get('items');
    }

    /**
     * @param App_Domain_MenuItem $item
     * @return App_Domain_MenuItem
     */
    public function addItem(App_Domain_MenuItem $item)
    {
        $item->setMenu($this->getMenu());
        $item->setParentMenuItem($this);
        $this->getItems()->add($item);
		return $this;
    }

    /**
     * @param App_Domain_MenuItem $item
     * @return App_Domain_MenuItem
     */
    public function deleteItem(App_Domain_MenuItem $item)
    {
        $this->getItems()->delete($item);
		return $this;
    }

    /**
     * @return App_Domain_MenuItem
     */
    public function deleteAllItems()
    {
        foreach ($this->getItems() as $item) {
            $this->deleteItem($item);
        }
		return $this;
    }

}
