<?php

class App_Domain_ItemType extends \Boxspaced\EntityManager\Entity\AbstractEntity
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
     * @return App_Domain_ItemType
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
     * @return App_Domain_ItemType
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
     * @return App_Domain_ItemType
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
     * @return App_Domain_ItemType
     */
    public function setDescription($description)
    {
        $this->set('description', $description);
		return $this;
    }

    /**
     * @return bool
     */
    public function getMultipleParts()
    {
        return $this->get('multipleParts');
    }

    /**
     * @param bool $multipleParts
     * @return App_Domain_ItemType
     */
    public function setMultipleParts($multipleParts)
    {
        $this->set('multipleParts', $multipleParts);
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
     * @param App_Domain_ItemTemplate $template
     * @return App_Domain_ItemType
     */
    public function addTemplate(App_Domain_ItemTemplate $template)
    {
        $template->setParentType($this);
        $this->getTemplates()->add($template);
		return $this;
    }

    /**
     * @param App_Domain_ItemTemplate $template
     * @return App_Domain_ItemType
     */
    public function deleteTemplate(App_Domain_ItemTemplate $template)
    {
        $this->getTemplates()->delete($template);
		return $this;
    }

    /**
     * @return App_Domain_ItemType
     */
    public function deleteAllTemplates()
    {
        foreach ($this->getTemplates() as $template) {
            $this->deleteTemplate($template);
        }
		return $this;
    }

    /**
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getTeaserTemplates()
    {
        return $this->get('teaserTemplates');
    }

    /**
     * @param App_Domain_ItemTeaserTemplate $teaserTemplate
     * @return App_Domain_ItemType
     */
    public function addTeaserTemplate(App_Domain_ItemTeaserTemplate $teaserTemplate)
    {
        $teaserTemplate->setParentType($this);
        $this->getTeaserTemplates()->add($teaserTemplate);
		return $this;
    }

    /**
     * @param App_Domain_ItemTeaserTemplate $teaserTemplate
     * @return App_Domain_ItemType
     */
    public function deleteTeaserTemplate(App_Domain_ItemTeaserTemplate $teaserTemplate)
    {
        $this->getTeaserTemplates()->delete($teaserTemplate);
		return $this;
    }

    /**
     * @return App_Domain_ItemType
     */
    public function deleteAllTeaserTemplates()
    {
        foreach ($this->getTeaserTemplates() as $teaserTemplate) {
            $this->deleteTeaserTemplate($teaserTemplate);
        }
		return $this;
    }

}
