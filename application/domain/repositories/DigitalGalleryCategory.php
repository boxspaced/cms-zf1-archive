<?php

class App_Domain_Repository_DigitalGalleryCategory
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
     * @return App_Domain_DigitalGalleryCategory
     */
    public function getById($id)
    {
        return $this->_entityManager->find('App_Domain_DigitalGalleryCategory', $id);
    }

    /**
     * @param int $offset
     * @param int $showPerPage
     * @param string $orderBy
     * @param string $dir
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getAll($offset = null, $showPerPage = null, $orderBy = 'type', $dir = 'ASC')
    {
        $conditions = $this->_entityManager->createConditions();

        if (null !== $offset && null !== $showPerPage) {
            $conditions->paging($offset, $showPerPage);
        }

        $conditions->order($orderBy, $dir);

        return $this->_entityManager->findAll('App_Domain_DigitalGalleryCategory', $conditions);
    }

    /**
     * @param App_Domain_DigitalGalleryCategory $entity
     * @return App_Domain_Repository_DigitalGalleryCategory
     */
    public function delete(App_Domain_DigitalGalleryCategory $entity)
    {
        $this->_entityManager->delete($entity);
        return $this;
    }

}
