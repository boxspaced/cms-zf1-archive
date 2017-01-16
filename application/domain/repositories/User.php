<?php

class App_Domain_Repository_User
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
     * @return App_Domain_User
     */
    public function getById($id)
    {
        return $this->_entityManager->find('App_Domain_User', $id);
    }

    /**
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getAll()
    {
        return $this->_entityManager->findAll('App_Domain_User');
    }

    /**
     * @param App_Domain_User $entity
     * @return App_Domain_Repository_User
     */
    public function delete(App_Domain_User $entity)
    {
        $this->_entityManager->delete($entity);
        return $this;
    }

}
