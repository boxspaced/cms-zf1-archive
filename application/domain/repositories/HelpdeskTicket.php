<?php

class App_Domain_Repository_HelpdeskTicket
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
     * @return App_Domain_HelpdeskTicket
     */
    public function getById($id)
    {
        return $this->_entityManager->find('App_Domain_HelpdeskTicket', $id);
    }

    /**
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getAll()
    {
        return $this->_entityManager->findAll('App_Domain_HelpdeskTicket');
    }

    /**
     * @param int $offset
     * @param int $showPerPage
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getAllOpenTickets($offset = null, $showPerPage = null)
    {
        $conditions = $this->_entityManager->createConditions();
        $conditions->field('status')->eq(App_Domain_HelpdeskTicket::STATUS_OPEN);

        if (null !== $offset && null !== $showPerPage) {
            $conditions->paging($offset, $showPerPage);
        }

        return $this->_entityManager->findAll('App_Domain_HelpdeskTicket', $conditions);
    }

    /**
     * @param App_Domain_HelpdeskTicket $entity
     * @return App_Domain_Repository_HelpdeskTicket
     */
    public function delete(App_Domain_HelpdeskTicket $entity)
    {
        $this->_entityManager->delete($entity);
        return $this;
    }

}
