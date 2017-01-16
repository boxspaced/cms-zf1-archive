<?php

class App_Domain_Repository_ItemType
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
     * @return App_Domain_ItemType
     */
    public function getById($id)
    {
        return $this->_entityManager->find('App_Domain_ItemType', $id);
    }

    /**
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getAll()
    {
        return $this->_entityManager->findAll('App_Domain_ItemType');
    }

    /**
     * @param App_Domain_ItemType $entity
     * @return App_Domain_Repository_ItemType
     */
    public function delete(App_Domain_ItemType $entity)
    {
        $this->_entityManager->delete($entity);
        return $this;
    }

}
