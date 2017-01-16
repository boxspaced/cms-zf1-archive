<?php

class App_Domain_ItemPart extends \Boxspaced\EntityManager\Entity\AbstractEntity
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
     * @return App_Domain_ItemPart
     */
    public function setId($id)
    {
        $this->set('id', $id);
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
     * @return App_Domain_ItemPart
     */
    public function setOrderBy($orderBy)
    {
        $this->set('orderBy', $orderBy);
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
     * @return App_Domain_ItemPart
     */
    public function setParentItem(App_Domain_Item $parentItem)
    {
        $this->set('parentItem', $parentItem);
		return $this;
    }

    /**
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getFields()
    {
        return $this->get('fields');
    }

    /**
     * @param App_Domain_ItemPartField $field
     * @return App_Domain_ItemPart
     */
    public function addField(App_Domain_ItemPartField $field)
    {
        $field->setParentPart($this);
        $this->getFields()->add($field);
		return $this;
    }

    /**
     * @param App_Domain_ItemPartField $field
     * @return App_Domain_ItemPart
     */
    public function deleteField(App_Domain_ItemPartField $field)
    {
        $this->getFields()->delete($field);
		return $this;
    }

    /**
     * @return App_Domain_ItemPart
     */
    public function deleteAllFields()
    {
        foreach ($this->getFields() as $field) {
            $this->deleteField($field);
        }
		return $this;
    }

}
