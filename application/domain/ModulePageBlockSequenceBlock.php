<?php

class App_Domain_ModulePageBlockSequenceBlock extends \Boxspaced\EntityManager\Entity\AbstractEntity
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
     * @return App_Domain_ModulePageBlockSequenceBlock
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return App_Domain_ModulePageBlockSequence
     */
    public function getParentBlockSequence()
    {
        return $this->get('parentBlockSequence');
    }

    /**
     * @param App_Domain_ModulePageBlockSequence $parentBlockSequence
     * @return App_Domain_ModulePageBlockSequenceBlock
     */
    public function setParentBlockSequence(App_Domain_ModulePageBlockSequence $parentBlockSequence)
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
     * @return App_Domain_ModulePageBlockSequenceBlock
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
     * @return App_Domain_ModulePageBlockSequenceBlock
     */
    public function setOrderBy($orderBy)
    {
        $this->set('orderBy', $orderBy);
		return $this;
    }

}
