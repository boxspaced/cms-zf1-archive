<?php

class App_Domain_Repository_DigitalGalleryImage
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
     * @return App_Domain_DigitalGalleryImage
     */
    public function getById($id)
    {
        return $this->_entityManager->find('App_Domain_DigitalGalleryImage', $id);
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

        return $this->_entityManager->findAll('App_Domain_DigitalGalleryImage', $conditions);
    }

    /**
     * @param App_Domain_DigitalGalleryImage $entity
     * @return App_Domain_Repository_DigitalGalleryImage
     */
    public function delete(App_Domain_DigitalGalleryImage $entity)
    {
        $this->_entityManager->delete($entity);
        return $this;
    }

}
