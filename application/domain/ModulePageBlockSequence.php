<?php

class App_Domain_ModulePageBlockSequence extends \Boxspaced\EntityManager\Entity\AbstractEntity
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
     * @return App_Domain_ModulePageBlockSequence
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
     * @return App_Domain_ModulePageBlockSequence
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
     * @return App_Domain_ModulePageBlockSequence
     */
    public function setModulePageBlock(App_Domain_ModulePageBlock $modulePageBlock)
    {
        $this->set('modulePageBlock', $modulePageBlock);
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
     * @param App_Domain_ModulePageBlockSequenceBlock $block
     * @return App_Domain_ModulePageBlockSequence
     */
    public function addBlock(App_Domain_ModulePageBlockSequenceBlock $block)
    {
        $block->setParentBlockSequence($this);
        $this->getBlocks()->add($block);
		return $this;
    }

    /**
     * @param App_Domain_ModulePageBlockSequenceBlock $block
     * @return App_Domain_ModulePageBlockSequence
     */
    public function deleteBlock(App_Domain_ModulePageBlockSequenceBlock $block)
    {
        $this->getBlocks()->delete($block);
		return $this;
    }

    /**
     * @return App_Domain_ModulePageBlockSequence
     */
    public function deleteAllBlocks()
    {
        foreach ($this->getBlocks() as $block) {
            $this->deleteBlock($block);
        }
		return $this;
    }

}
