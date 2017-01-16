<?php

class App_Domain_ItemField extends \Boxspaced\EntityManager\Entity\AbstractEntity
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
     * @return App_Domain_ItemField
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
     * @return App_Domain_ItemField
     */
    public function setName($name)
    {
        $this->set('name', $name);
		return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->get('value');
    }

    /**
     * @param string $value
     * @return App_Domain_ItemField
     */
    public function setValue($value)
    {
        $this->set('value', $value);
		return $this;
    }

    /**
     * @return App_Domain_Item
     */
    public function getParentItem()
    {
        return $this->get('parentItem');
    }

    /**
     * @param App_Domain_Item $parentItem
     * @return App_Domain_ItemField
     */
    public function setParentItem(App_Domain_Item $parentItem)
    {
        $this->set('parentItem', $parentItem);
		return $this;
    }

}
