<?php

class App_Domain_Repository_Item
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
     * @return App_Domain_Item
     */
    public function getById($id)
    {
        return $this->_entityManager->find('App_Domain_Item', $id);
    }

    /**
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getAll()
    {
        return $this->_entityManager->findAll('App_Domain_Item');
    }

    /**
     * @param int $offset
     * @param int $showPerPage
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getAllLive($offset = null, $showPerPage = null)
    {
        $now = new DateTime();

        $conditions = $this->_entityManager->createConditions();
        $conditions->field('status')->eq(App_Domain_Service_VersionableInterface::STATUS_PUBLISHED);
        $conditions->field('liveFrom')->lt($now);
        $conditions->field('expiresEnd')->gt($now);

        if (null !== $offset && null !== $showPerPage) {
            $conditions->paging($offset, $showPerPage);
        }

        return $this->_entityManager->findAll('App_Domain_Item', $conditions);
    }

    /**  *
     * @param int $versionOfId
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getAllVersionOf($versionOfId)
    {
        $conditions = $this->_entityManager->createConditions();
        $conditions->field('versionOf.id')->eq($versionOfId);

        return $this->_entityManager->findAll('App_Domain_Item', $conditions);
    }

    /**
     * @param App_Domain_Item $entity
     * @return App_Domain_Repository_Item
     */
    public function delete(App_Domain_Item $entity)
    {
        $this->_entityManager->delete($entity);
        return $this;
    }

}
