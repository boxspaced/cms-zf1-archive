<?php

class App_Domain_HelpdeskTicket extends \Boxspaced\EntityManager\Entity\AbstractEntity
{

    const STATUS_OPEN = 'OPEN';
    const STATUS_RESOLVED = 'RESOLVED';

    /**
     * @return int
     */
    public function getId()
    {
        return $this->get('id');
    }

    /**
     * @param int $id
     * @return App_Domain_HelpdeskTicket
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->get('status');
    }

    /**
     * @param string $status
     * @return App_Domain_HelpdeskTicket
     */
    public function setStatus($status)
    {
        $this->set('status', $status);
		return $this;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->get('subject');
    }

    /**
     * @param string $subject
     * @return App_Domain_HelpdeskTicket
     */
    public function setSubject($subject)
    {
        $this->set('subject', $subject);
		return $this;
    }

    /**
     * @return string
     */
    public function getIssue()
    {
        return $this->get('issue');
    }

    /**
     * @param string $issue
     * @return App_Domain_HelpdeskTicket
     */
    public function setIssue($issue)
    {
        $this->set('issue', $issue);
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
     * @return App_Domain_HelpdeskTicket
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
     * @return App_Domain_HelpdeskTicket
     */
    public function setCreatedAt(DateTime $createdAt = null)
    {
        $this->set('createdAt', $createdAt);
		return $this;
    }

    /**
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getComments()
    {
        return $this->get('comments');
    }

    /**
     * @param App_Domain_HelpdeskTicketComment $helpdeskComment
     * @return App_Domain_HelpdeskTicket
     */
    public function addComment(App_Domain_HelpdeskTicketComment $helpdeskComment)
    {
        $helpdeskComment->setTicket($this);
        $this->getComments()->add($helpdeskComment);
		return $this;
    }

    /**
     * @param App_Domain_HelpdeskTicketComment $helpdeskComment
     * @return App_Domain_HelpdeskTicket
     */
    public function deleteComment(App_Domain_HelpdeskTicketComment $helpdeskComment)
    {
        $this->getComments()->delete($helpdeskComment);
		return $this;
    }

    /**
     * @return App_Domain_HelpdeskTicket
     */
    public function deleteAllComments()
    {
        foreach ($this->getComments() as $comment) {
            $this->deleteComment($comment);
        }
		return $this;
    }

    /**
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getAttachments()
    {
        return $this->get('attachments');
    }

    /**
     * @param App_Domain_HelpdeskTicketAttachment $attachment
     * @return App_Domain_HelpdeskTicket
     */
    public function addAttachment(App_Domain_HelpdeskTicketAttachment $attachment)
    {
        $attachment->setTicket($this);
        $this->getAttachments()->add($attachment);
		return $this;
    }

    /**
     * @param App_Domain_HelpdeskTicketAttachment $attachment
     * @return App_Domain_HelpdeskTicket
     */
    public function deleteAttachment(App_Domain_HelpdeskTicketAttachment $attachment)
    {
        $this->getAttachments()->delete($attachment);
		return $this;
    }

    /**
     * @return App_Domain_HelpdeskTicket
     */
    public function deleteAllAttachments()
    {
        foreach ($this->getAttachments() as $attachment) {
            $this->deleteAttachment($attachment);
        }
		return $this;
    }

}
