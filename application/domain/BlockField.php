<?php

class App_Domain_BlockField extends \Boxspaced\EntityManager\Entity\AbstractEntity
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
     * @return App_Domain_BlockField
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
     * @return App_Domain_BlockField
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
     * @return App_Domain_BlockField
     */
    public function setValue($value)
    {
        $this->set('value', $value);
        return $this;
    }

    /**
     * @return App_Domain_Block
     */
    public function getParentBlock()
    {
        return $this->get('parentBlock');
    }

    /**
     * @param App_Domain_Block $parentBlock
     * @return App_Domain_BlockField
     */
    public function setParentBlock(App_Domain_Block $parentBlock)
    {
        $this->set('parentBlock', $parentBlock);
        return $this;
    }

}
