<?php

class App_Domain_Menu extends \Boxspaced\EntityManager\Entity\AbstractEntity
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
     * @return App_Domain_Menu
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->get('name');
    }

    /**
     * @param string $name
     * @return App_Domain_Menu
     */
    public function setName($name)
    {
        $this->set('name', $name);
		return $this;
    }

    /**
     * @return bool
     */
    public function getPrimary()
    {
        return $this->get('primary');
    }

    /**
     * @param bool $primary
     * @return App_Domain_Menu
     */
    public function setPrimary($primary)
    {
        $this->set('primary', $primary);
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
     * @return App_Domain_Menu
     */
    public function addItem(App_Domain_MenuItem $item)
    {
        $item->setMenu($this);
        $this->getItems()->add($item);
		return $this;
    }

    /**
     * @param App_Domain_MenuItem $item
     * @return App_Domain_Menu
     */
    public function deleteItem(App_Domain_MenuItem $item)
    {
        $this->getItems()->delete($item);
		return $this;
    }

    /**
     * @return App_Domain_Menu
     */
    public function deleteAllItems()
    {
        foreach ($this->getItems() as $item) {
            $this->deleteItem($item);
        }
		return $this;
    }

}
