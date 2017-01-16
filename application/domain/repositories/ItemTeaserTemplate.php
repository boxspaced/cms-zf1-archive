<?php

class App_Domain_Repository_ItemTeaserTemplate
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
     * @return App_Domain_ItemTeaserTemplate
     */
    public function getById($id)
    {
        return $this->_entityManager->find('App_Domain_ItemTeaserTemplate', $id);
    }

    /**
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getAll()
    {
        return $this->_entityManager->findAll('App_Domain_ItemTeaserTemplate');
    }

    /**
     * @param App_Domain_ItemTeaserTemplate $entity
     * @return App_Domain_Repository_ItemTeaserTemplate
     */
    public function delete(App_Domain_ItemTeaserTemplate $entity)
    {
        $this->_entityManager->delete($entity);
        return $this;
    }

}
