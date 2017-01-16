<?php

class App_Domain_ItemPartField extends \Boxspaced\EntityManager\Entity\AbstractEntity
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
     * @return App_Domain_ItemPartField
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
     * @return App_Domain_ItemPartField
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
     * @return App_Domain_ItemPartField
     */
    public function setValue($value)
    {
        $this->set('value', $value);
		return $this;
    }

    /**
     * @return App_Domain_ItemPart
     */
    public function getParentPart()
    {
        return $this->get('parentPart');
    }

    /**
     * @param App_Domain_ItemPart $parentPart
     * @return App_Domain_ItemPartField
     */
    public function setParentPart(App_Domain_ItemPart $parentPart)
    {
        $this->set('parentPart', $parentPart);
		return $this;
    }

}
