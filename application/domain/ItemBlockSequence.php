<?php

class App_Domain_ItemBlockSequence extends \Boxspaced\EntityManager\Entity\AbstractEntity
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
     * @return App_Domain_ItemBlockSequence
     */
    public function setId($id)
    {
        $this->set('id', $id);
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
     * @return App_Domain_ItemBlockSequence
     */
    public function setParentItem(App_Domain_Item $parentItem)
    {
        $this->set('parentItem', $parentItem);
		return $this;
    }

    /**
     * @return App_Domain_ItemTemplateBlock
     */
    public function getTemplateBlock()
    {
        return $this->get('templateBlock');
    }

    /**
     * @param App_Domain_ItemTemplateBlock $templateBlock
     * @return App_Domain_ItemBlockSequence
     */
    public function setTemplateBlock(App_Domain_ItemTemplateBlock $templateBlock)
    {
        $this->set('templateBlock', $templateBlock);
		return $this;
    }

    /**
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getBlocks()
    {
        return $this->get('blocks');
    }

    /**
     * @param App_Domain_ItemBlockSequenceBlock $block
     * @return App_Domain_ItemBlockSequence
     */
    public function addBlock(App_Domain_ItemBlockSequenceBlock $block)
    {
        $block->setParentBlockSequence($this);
        $this->getBlocks()->add($block);
		return $this;
    }

    /**
     * @param App_Domain_ItemBlockSequenceBlock $block
     * @return App_Domain_ItemBlockSequence
     */
    public function deleteBlock(App_Domain_ItemBlockSequenceBlock $block)
    {
        $this->getBlocks()->delete($block);
		return $this;
    }

    /**
     * @return App_Domain_ItemBlockSequence
     */
    public function deleteAllBlocks()
    {
        foreach ($this->getBlocks() as $block)
        {
            $this->deleteBlock($block);
        }
		return $this;
    }

}
