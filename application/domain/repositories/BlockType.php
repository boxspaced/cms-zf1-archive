<?php

class App_Domain_Repository_BlockType
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
     * @return App_Domain_BlockType
     */
    public function getById($id)
    {
        return $this->_entityManager->find('App_Domain_BlockType', $id);
    }

    /**
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getAll()
    {
        return $this->_entityManager->findAll('App_Domain_BlockType');
    }

    /**
     * @param App_Domain_BlockType $entity
     * @return App_Domain_Repository_BlockType
     */
    public function delete(App_Domain_BlockType $entity)
    {
        $this->_entityManager->delete($entity);
        return $this;
    }

}
