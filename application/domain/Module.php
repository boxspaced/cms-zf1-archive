<?php

class App_Domain_Module extends \Boxspaced\EntityManager\Entity\AbstractEntity
{

    // @todo Name constants e.g. item, container, block, digital-gallery etc.

    /**
     * @return int
     */
    public function getId()
    {
        return $this->get('id');
    }

    /**
     * @param int $id
     * @return App_Domain_Module
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
     * @return App_Domain_Module
     */
    public function setName($name)
    {
        $this->set('name', $name);
		return $this;
    }

    /**
     * @return bool
     */
    public function getEnabled()
    {
        return $this->get('enabled');
    }

    /**
     * @param bool $enabled
     * @return App_Domain_Module
     */
    public function setEnabled($enabled)
    {
        $this->set('enabled', $enabled);
		return $this;
    }

    /**
     * @return string
     */
    public function getRouteController()
    {
        return $this->get('routeController');
    }

    /**
     * @param string $routeController
     * @return App_Domain_Module
     */
    public function setRouteController($routeController)
    {
        $this->set('routeController', $routeController);
		return $this;
    }

    /**
     * @return string
     */
    public function getRouteAction()
    {
        return $this->get('routeAction');
    }

    /**
     * @param string $routeAction
     * @return App_Domain_Module
     */
    public function setRouteAction($routeAction)
    {
        $this->set('routeAction', $routeAction);
		return $this;
    }

    /**
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getRoutes()
    {
        return $this->get('routes');
    }

    /**
     * @param App_Domain_Route $route
     * @return App_Domain_Module
     */
    public function addRoute(App_Domain_Route $route)
    {
        $route->setModule($this);
        $this->getRoutes()->add($route);
		return $this;
    }

    /**
     * @param App_Domain_Route $route
     * @return App_Domain_Module
     */
    public function deleteRoute(App_Domain_Route $route)
    {
        $this->getRoutes()->delete($route);
		return $this;
    }

    /**
     * @return App_Domain_Module
     */
    public function deleteAllRoutes()
    {
        foreach ($this->getRoutes() as $route) {
            $this->deleteRoute($route);
        }
		return $this;
    }

    /**
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getPages()
    {
        return $this->get('pages');
    }

    /**
     * @param App_Domain_ModulePage $page
     * @return App_Domain_Module
     */
    public function addPage(App_Domain_ModulePage $page)
    {
        $page->setParentModule($this);
        $this->getPages()->add($page);
		return $this;
    }

    /**
     * @param App_Domain_ModulePage $page
     * @return App_Domain_Module
     */
    public function deletePage(App_Domain_ModulePage $page)
    {
        $this->getPages()->delete($page);
		return $this;
    }

    /**
     * @return App_Domain_Module
     */
    public function deleteAllPages()
    {
        foreach ($this->getPages() as $page) {
            $this->deletePage($page);
        }
		return $this;
    }

}
