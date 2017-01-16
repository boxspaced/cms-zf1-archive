<?php

class App_Domain_BlockType extends \Boxspaced\EntityManager\Entity\AbstractEntity
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
     * @return App_Domain_BlockType
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
     * @return App_Domain_BlockType
     */
    public function setName($name)
    {
        $this->set('name', $name);
		return $this;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->get('icon');
    }

    /**
     * @param string $icon
     * @return App_Domain_BlockType
     */
    public function setIcon($icon)
    {
        $this->set('icon', $icon);
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
     * @return App_Domain_BlockType
     */
    public function setDescription($description)
    {
        $this->set('description', $description);
		return $this;
    }

    /**
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getTemplates()
    {
        return $this->get('templates');
    }

    /**
     * @param App_Domain_BlockTemplate $template
     * @return App_Domain_BlockType
     */
    public function addTemplate(App_Domain_BlockTemplate $template)
    {
        $template->setParentType($this);
        $this->getTemplates()->add($template);
        return $this;
    }

    /**
     * @param App_Domain_BlockTemplate $template
     * @return App_Domain_BlockType
     */
    public function deleteTemplate(App_Domain_BlockTemplate $template)
    {
        $this->getTemplates()->delete($template);
        return $this;
    }

    /**
     * @return App_Domain_BlockType
     */
    public function deleteAllTemplates()
    {
        foreach ($this->getTemplates() as $template) {
            $this->deleteTemplate($template);
        }
        return $this;
    }

}
