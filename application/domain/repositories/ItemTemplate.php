<?php

class App_Domain_Repository_ItemTemplate
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
     * @return App_Domain_ItemTemplate
     */
    public function getById($id)
    {
        return $this->_entityManager->find('App_Domain_ItemTemplate', $id);
    }

    /**
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getAll()
    {
        return $this->_entityManager->findAll('App_Domain_ItemTemplate');
    }

    /**
     * @param App_Domain_ItemTemplate $entity
     * @return App_Domain_Repository_ItemTemplate
     */
    public function delete(App_Domain_ItemTemplate $entity)
    {
        $this->_entityManager->delete($entity);
        return $this;
    }

}
