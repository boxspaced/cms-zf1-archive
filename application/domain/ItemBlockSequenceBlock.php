<?php

class App_Domain_ItemBlockSequenceBlock extends \Boxspaced\EntityManager\Entity\AbstractEntity
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
     * @return App_Domain_ItemBlockSequenceBlock
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return App_Domain_ItemBlockSequence
     */
    public function getParentBlockSequence()
    {
        return $this->get('parentBlockSequence');
    }

    /**
     * @param App_Domain_ItemBlockSequence $parentBlockSequence
     * @return App_Domain_ItemBlockSequenceBlock
     */
    public function setParentBlockSequence(App_Domain_ItemBlockSequence $parentBlockSequence)
    {
        $this->set('parentBlockSequence', $parentBlockSequence);
		return $this;
    }

    /**
     * @return App_Domain_Block
     */
    public function getBlock()
    {
        return $this->get('block');
    }

    /**
     * @param App_Domain_Block $block
     * @return App_Domain_ItemBlockSequenceBlock
     */
    public function setBlock(App_Domain_Block $block)
    {
        $this->set('block', $block);
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
     * @return App_Domain_ItemBlockSequenceBlock
     */
    public function setOrderBy($orderBy)
    {
        $this->set('orderBy', $orderBy);
		return $this;
    }

}
