<?php

class App_Domain_ItemTemplate extends \Boxspaced\EntityManager\Entity\AbstractEntity
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
     * @return App_Domain_ItemTemplate
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return App_Domain_ItemType
     */
    public function getForType()
    {
        return $this->get('forType');
    }

    /**
     * @param App_Domain_ItemType $forType
     * @return App_Domain_ItemTemplate
     */
    public function setForType(App_Domain_ItemType $forType)
    {
        $this->set('forType', $forType);
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
     * @return App_Domain_ItemTemplate
     */
    public function setName($name)
    {
        $this->set('name', $name);
		return $this;
    }

    /**
     * @return string
     */
    public function getViewScript()
    {
        return $this->get('viewScript');
    }

    /**
     * @param string $viewScript
     * @return App_Domain_ItemTemplate
     */
    public function setViewScript($viewScript)
    {
        $this->set('viewScript', $viewScript);
		return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->get('description');
    }

    /**
     * @param string $description
     * @return App_Domain_ItemTemplate
     */
    public function setDescription($description)
    {
        $this->set('description', $description);
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
     * @param App_Domain_ItemTemplateBlock $block
     * @return App_Domain_ItemTemplate
     */
    public function addBlock(App_Domain_ItemTemplateBlock $block)
    {
        $block->setParentTemplate($this);
        $this->getBlocks()->add($block);
		return $this;
    }

    /**
     * @param App_Domain_ItemTemplateBlock $block
     * @return App_Domain_ItemTemplate
     */
    public function deleteBlock(App_Domain_ItemTemplateBlock $block)
    {
        $this->getBlocks()->delete($block);
		return $this;
    }

    /**
     * @return App_Domain_ItemTemplate
     */
    public function deleteAllBlocks()
    {
        foreach ($this->getBlocks() as $block) {
            $this->deleteBlock($block);
        }
		return $this;
    }

}
