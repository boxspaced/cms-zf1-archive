<?php

class App_Domain_Route extends \Boxspaced\EntityManager\Entity\AbstractEntity
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
     * @return App_Domain_Route
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->get('slug');
    }

    /**
     * @param string $slug
     * @return App_Domain_Route
     */
    public function setSlug($slug)
    {
        $this->set('slug', $slug);
		return $this;
    }

    /**
     * @return App_Domain_Module
     */
    public function getModule()
    {
        return $this->get('module');
    }

    /**
     * @param App_Domain_Module $module
     * @return App_Domain_Route
     */
    public function setModule(App_Domain_Module $module)
    {
        $this->set('module', $module);
		return $this;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->get('identifier');
    }

    /**
     * @param string $identifier
     * @return App_Domain_Route
     */
    public function setIdentifier($identifier)
    {
        $this->set('identifier', $identifier);
		return $this;
    }

}
