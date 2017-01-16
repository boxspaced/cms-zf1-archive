<?php

class App_Domain_ItemFreeBlock extends \Boxspaced\EntityManager\Entity\AbstractEntity
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

    /**
     * @return App_Domain_ItemTemplateBlock
     */
    public function getTemplateBlock()
    {
        return $this->get('templateBlock');
    }

    /**
     * @param App_Domain_ItemTemplateBlock $templateBlock
     * @return App_Domain_ItemFreeBlock
     */
    public function setTemplateBlock(App_Domain_ItemTemplateBlock $templateBlock)
    {
        $this->set('templateBlock', $templateBlock);
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
     * @return App_Domain_ItemFreeBlock
     */
    public function setBlock(App_Domain_Block $block)
    {
        $this->set('block', $block);
		return $this;
    }

}
