<?php

class App_Domain_Repository_Module
{

    /**
     * @var \Boxspaced\EntityManager\EntityManager
     */
    protected $_entityManager;

    /**
     * @param \Boxspaced\EntityManager\EntityManager $entityManager
     */
    public function __construct(
        \Boxspaced\EntityManager\EntityManager $entityManager
    )
    {
        $this->_entityManager = $entityManager;
    }

    /**
     * @param int $id
     * @return App_Domain_Module
     */
    public function getById($id)
    {
        return $this->_entityManager->find('App_Domain_Module', $id);
    }

    /**
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getAll()
    {
        return $this->_entityManager->findAll('App_Domain_Module');
    }

    /**
     * @param string $name
     * @return App_Domain_Module
     */
    public function getByName($name)
    {
        $conditions = $this->_entityManager->createConditions();
        $conditions->field('name')->eq($name);
        return $this->_entityManager->findOne('App_Domain_Module', $conditions);
    }

    /**
     * @param App_Domain_Module $entity
     * @return App_Domain_Repository_Module
     */
    public function delete(App_Domain_Module $entity)
    {
        $this->_entityManager->delete($entity);
        return $this;
    }

}
