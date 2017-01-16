<?php

class App_Domain_Factory
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
     * @param string $type
     * @param bool $persist
     * @return \Boxspaced\EntityManager\Entity\AbstractEntity
     */
    public function createEntity($type, $persist = true)
    {
        $entity = $this->_entityManager->createEntity($type);

        if ($persist) {
            $this->_entityManager->persist($entity);
        }

        return $entity;
    }

}
