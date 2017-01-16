<?php

class App_Domain_HelpdeskTicketAttachment extends \Boxspaced\EntityManager\Entity\AbstractEntity
{

    /**
     * @return int
     */
    public function getId()
    {
        return $this->get('id');
    }

    /**
     * @param int $id
     * @return App_Domain_HelpdeskTicketAttachment
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return App_Domain_HelpdeskTicket
     */
    public function getTicket()
    {
        return $this->get('ticket');
    }

    /**
     * @param App_Domain_HelpdeskTicket $ticket
     * @return App_Domain_HelpdeskTicketAttachment
     */
    public function setTicket(App_Domain_HelpdeskTicket $ticket)
    {
        $this->set('ticket', $ticket);
		return $this;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->get('fileName');
    }

    /**
     * @param string $fileName
     * @return App_Domain_HelpdeskTicketAttachment
     */
    public function setFileName($fileName)
    {
        $this->set('fileName', $fileName);
		return $this;
    }

    /**
     * @return App_Domain_User
     */
    public function getUser()
    {
        return $this->get('user');
    }

    /**
     * @param App_Domain_user $user
     * @return App_Domain_HelpdeskTicketAttachment
     */
    public function setUser($user)
    {
        $this->set('user', $user);
		return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->get('createdAt');
    }

    /**
     * @param DateTime $createdAt
     * @return App_Domain_HelpdeskTicketAttachment
     */
    public function setCreatedAt(DateTime $createdAt = null)
    {
        $this->set('createdAt', $createdAt);
		return $this;
    }

}
