<?php

class App_Domain_ItemTeaserTemplate extends \Boxspaced\EntityManager\Entity\AbstractEntity
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
     * @return App_Domain_ItemTeaserTemplate
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
     * @return App_Domain_ItemTeaserTemplate
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
     * @return App_Domain_ItemTeaserTemplate
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
     * @return App_Domain_ItemTeaserTemplate
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
     * @return App_Domain_ItemTeaserTemplate
     */
    public function setDescription($description)
    {
        $this->set('description', $description);
		return $this;
    }

}
