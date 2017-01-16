<?php

class App_Domain_ModulePage extends \Boxspaced\EntityManager\Entity\AbstractEntity
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
     * @return App_Domain_ModulePage
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return App_Domain_Module
     */
    public function getParentModule()
    {
        return $this->get('parentModule');
    }

    /**
     * @param App_Domain_Module $parentModule
     * @return App_Domain_ModulePage
     */
    public function setParentModule(App_Domain_Module $parentModule)
    {
        $this->set('parentModule', $parentModule);
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
     * @return App_Domain_ModulePage
     */
    public function setName($name)
    {
        $this->set('name', $name);
		return $this;
    }

    /**
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getFreeBlocks()
    {
        return $this->get('freeBlocks');
    }

    /**
     * @param App_Domain_ModulePageFreeBlock $freeBlock
     * @return App_Domain_ModulePage
     */
    public function addFreeBlock(App_Domain_ModulePageFreeBlock $freeBlock)
    {
        $freeBlock->setParentModulePage($this);
        $this->getFreeBlocks()->add($freeBlock);
		return $this;
    }

    /**
     * @param App_Domain_ModulePageFreeBlock $freeBlock
     * @return App_Domain_ModulePage
     */
    public function deleteFreeBlock(App_Domain_ModulePageFreeBlock $freeBlock)
    {
        $this->getFreeBlocks()->delete($freeBlock);
		return $this;
    }

    /**
     * @return App_Domain_ModulePage
     */
    public function deleteAllFreeBlocks()
    {
        foreach ($this->getFreeBlocks() as $freeBlock) {
            $this->deleteFreeBlock($freeBlock);
        }
		return $this;
    }

    /**
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getBlockSequences()
    {
        return $this->get('blockSequences');
    }

    /**
     * @param App_Domain_ModulePageBlockSequence $blockSequence
     * @return App_Domain_ModulePage
     */
    public function addBlockSequence(App_Domain_ModulePageBlockSequence $blockSequence)
    {
        $blockSequence->setParentModulePage($this);
        $this->getBlockSequences()->add($blockSequence);
		return $this;
    }

    /**
     * @param App_Domain_ModulePageBlockSequence $blockSequence
     * @return App_Domain_ModulePage
     */
    public function deleteBlockSequence(App_Domain_ModulePageBlockSequence $blockSequence)
    {
        $this->getBlockSequences()->delete($blockSequence);
		return $this;
    }

    /**
     * @return App_Domain_ModulePage
     */
    public function deleteAllBlockSequences()
    {
        foreach ($this->getBlockSequences() as $blockSequence) {
            $this->deleteBlockSequence($blockSequence);
        }
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
     * @param App_Domain_ModulePageBlock $block
     * @return App_Domain_ModulePage
     */
    public function addPageBlock(App_Domain_ModulePageBlock $block)
    {
        $block->setParentModulePage($this);
        $this->getBlocks()->add($block);
		return $this;
    }

    /**
     * @param App_Domain_ModulePageBlock $block
     * @return App_Domain_ModulePage
     */
    public function deleteBlock(App_Domain_ModulePageBlock $block)
    {
        $this->getBlocks()->delete($block);
		return $this;
    }

    /**
     * @return App_Domain_ModulePage
     */
    public function deleteAllBlocks()
    {
        foreach ($this->getBlocks() as $block) {
            $this->deleteBlock($block);
        }
		return $this;
    }

}
