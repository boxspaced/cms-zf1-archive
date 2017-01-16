<?php

class App_Domain_Repository_DigitalGalleryOrder
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
     * @return App_Domain_DigitalGalleryOrder
     */
    public function getById($id)
    {
        return $this->_entityManager->find('App_Domain_DigitalGalleryOrder', $id);
    }

    /**
     * @param string $code
     * @return App_Domain_DigitalGalleryOrder
     */
    public function getByCode($code)
    {
        $conditions = $this->_entityManager->createConditions();
        $conditions->field('code')->eq($code);

        return $this->_entityManager->findOne('App_Domain_DigitalGalleryOrder', $conditions);
    }

    /**
     * @param int $offset
     * @param int $showPerPage
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getAll($offset = null, $showPerPage = null)
    {
        $conditions = $this->_entityManager->createConditions();

        if (null !== $offset && null !== $showPerPage) {
            $conditions->paging($offset, $showPerPage);
        }

        return $this->_entityManager->findAll('App_Domain_DigitalGalleryOrder', $conditions);
    }

    /**
     * @param App_Domain_DigitalGalleryOrder $entity
     * @return App_Domain_Repository_DigitalGalleryOrder
     */
    public function delete(App_Domain_DigitalGalleryOrder $entity)
    {
        $this->_entityManager->delete($entity);
        return $this;
    }

}
