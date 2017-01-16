<?php

class App_Domain_HelpdeskTicketComment extends \Boxspaced\EntityManager\Entity\AbstractEntity
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
     * @return App_Domain_HelpdeskTicketComment
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
     * @return App_Domain_HelpdeskTicketComment
     */
    public function setTicket(App_Domain_HelpdeskTicket $ticket)
    {
        $this->set('ticket', $ticket);
		return $this;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->get('comment');
    }

    /**
     * @param string $comment
     * @return App_Domain_HelpdeskTicketComment
     */
    public function setComment($comment)
    {
        $this->set('comment', $comment);
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
     * @param App_Domain_User $user
     * @return App_Domain_HelpdeskTicketComment
     */
    public function setUser(App_Domain_User $user)
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
     * @return App_Domain_HelpdeskTicketComment
     */
    public function setCreatedAt(DateTime $createdAt = null)
    {
        $this->set('createdAt', $createdAt);
		return $this;
    }

}
