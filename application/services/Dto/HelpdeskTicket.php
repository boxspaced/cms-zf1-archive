<?php

class App_Service_Dto_HelpdeskTicket
{

    /**
     *
     * @var int
     */
    public $id;

    /**
     *
     * @var string
     */
    public $subject;

    /**
     *
     * @var string
     */
    public $issue;

    /**
     *
     * @var string
     */
    public $status;

    /**
     *
     * @var string
     */
    public $username;

    /**
     *
     * @var DateTime
     */
    public $createdAt;

    /**
     *
     * @var App_Service_Dto_HelpdeskTicketComment[]
     */
    public $comments = array();

    /**
     *
     * @var App_Service_Dto_HelpdeskTicketAttachment[]
     */
    public $attachments = array();

}
