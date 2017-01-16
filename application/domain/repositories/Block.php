<?php

class App_Domain_Repository_Block
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
     * @return App_Domain_Block
     */
    public function getById($id)
    {
        return $this->_entityManager->find('App_Domain_Block', $id);
    }

    /**
     * @todo Database should have unique constraint on name column
     * @param string $name
     * @return App_Domain_Block
     */
    public function getByName($name)
    {
        $conditions = $this->_entityManager->createConditions();
        $conditions->field('name')->eq($name);

        return $this->_entityManager->findOne('App_Domain_Block', $conditions);
    }

    /**
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getAll()
    {
        return $this->_entityManager->findAll('App_Domain_Block');
    }

    /**
     * @param int $offset
     * @param int $showPerPage
     * @param string $orderBy
     * @param string $dir
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getAllPublished($offset = null, $showPerPage = null, $orderBy = 'name', $dir = 'ASC')
    {
        $conditions = $this->_entityManager->createConditions();
        $conditions->field('status')->eq(App_Domain_Service_VersionableInterface::STATUS_PUBLISHED);

        if (null !== $offset && null !== $showPerPage) {
            $conditions->paging($offset, $showPerPage);
        }

        $conditions->order($orderBy, $dir);

        return $this->_entityManager->findAll('App_Domain_Block', $conditions);
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

        return $this->_entityManager->findAll('App_Domain_Block', $conditions);
    }

    /**
     * @param int $versionOfId
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getAllVersionOf($versionOfId)
    {
        $conditions = $this->_entityManager->createConditions();
        $conditions->field('versionOf.id')->eq($versionOfId);

        return $this->_entityManager->findAll('App_Domain_Block', $conditions);
    }

    /**
     * @param App_Domain_Block $entity
     * @return App_Domain_Repository_Block
     */
    public function delete(App_Domain_Block $entity)
    {
        $this->_entityManager->delete($entity);
        return $this;
    }

}
