<?php

class App_Domain_ModulePageFreeBlock extends \Boxspaced\EntityManager\Entity\AbstractEntity
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
     * @return App_Domain_ModulePageFreeBlock
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return App_Domain_ModulePage
     */
    public function getParentModulePage()
    {
        return $this->get('parentModulePage');
    }

    /**
     * @param App_Domain_ModulePage $parentModulePage
     * @return App_Domain_ModulePageFreeBlock
     */
    public function setParentModulePage(App_Domain_ModulePage $parentModulePage)
    {
        $this->set('parentModulePage', $parentModulePage);
		return $this;
    }

    /**
     * @return App_Domain_ModulePageBlock
     */
    public function getModulePageBlock()
    {
        return $this->get('modulePageBlock');
    }

    /**
     * @param App_Domain_ModulePageBlock $modulePageBlock
     * @return App_Domain_ModulePageFreeBlock
     */
    public function setModulePageBlock(App_Domain_ModulePageBlock $modulePageBlock)
    {
        $this->set('modulePageBlock', $modulePageBlock);
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
     * @return App_Domain_ModulePageFreeBlock
     */
    public function setBlock($block)
    {
        $this->set('block', $block);
		return $this;
    }

}
