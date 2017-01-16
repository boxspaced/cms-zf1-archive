<?php

class App_Service_Helpdesk
{

    /**
     * @var Zend_Log
     */
    protected $_log;

    /**
     * @var Zend_Config
     */
    protected $_config;

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_adapter;

    /**
     * @var Zend_Auth
     */
    protected $_auth;

    /**
     * @var App_Domain_User
     */
    protected $_user;

    /**
     * @var \Boxspaced\EntityManager\EntityManager
     */
    protected $_entityManager;

    /**
     * @var App_Domain_Repository_User
     */
    protected $_userRepository;

    /**
     * @var App_Domain_Repository_HelpdeskTicket
     */
    protected $_helpdeskTicketRepository;

    /**
     * @var App_Service_Assembler_Helpdesk
     */
    protected $_dtoAssembler;

    /**
     * @var App_Domain_Factory
     */
    protected $_domainFactory;

    /**
     * @param Zend_Log $log
     * @param Zend_Config $config
     * @param Zend_Db_Adapter_Abstract $adapter
     * @param Zend_Auth $auth
     * @param \Boxspaced\EntityManager\EntityManager $entityManager
     * @param App_Domain_Repository_User $userRepository
     * @param App_Domain_Repository_HelpdeskTicket $helpdeskTicketRepository
     * @param App_Service_Assembler_Helpdesk $dtoAssembler
     * @param App_Domain_Factory $domainFactory
     */
    public function __construct(
        Zend_Log $log,
        Zend_Config $config,
        Zend_Db_Adapter_Abstract $adapter,
        Zend_Auth $auth,
        \Boxspaced\EntityManager\EntityManager $entityManager,
        App_Domain_Repository_User $userRepository,
        App_Domain_Repository_HelpdeskTicket $helpdeskTicketRepository,
        App_Service_Assembler_Helpdesk $dtoAssembler,
        App_Domain_Factory $domainFactory
    )
    {
        $this->_log = $log;
        $this->_config = $config;
        $this->_adapter = $adapter;
        $this->_auth = $auth;
        $this->_entityManager = $entityManager;
        $this->_userRepository = $userRepository;
        $this->_helpdeskTicketRepository = $helpdeskTicketRepository;
        $this->_dtoAssembler = $dtoAssembler;
        $this->_domainFactory = $domainFactory;

        if ($this->_auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            $this->_user = $userRepository->getById($identity->id);
        }
    }

    /**
     * @param int $id
     * @return App_Service_Dto_HelpdeskTicket
     */
    public function getTicket($id)
    {
        $ticket = $this->_helpdeskTicketRepository->getById($id);

        if (null === $ticket) {
            throw new App_Service_Exception('Unable to find a ticket with given ID');
        }

        return $this->_dtoAssembler->assembleTicketDto($ticket);
    }

    /**
     * @param App_Service_Dto_HelpdeskTicket $data
     * @return int
     */
    public function createNewTicket(App_Service_Dto_HelpdeskTicket $data, $attachmentFileName = null)
    {
        $ticket = $this->_domainFactory->createEntity('App_Domain_HelpdeskTicket');
        $ticket->setSubject($data->subject);
        $ticket->setIssue($data->issue);
        $ticket->setStatus(App_Domain_HelpdeskTicket::STATUS_OPEN);
        $ticket->setCreatedAt(new DateTime());
        $ticket->setUser($this->_user);

        if ($attachmentFileName) {

            $attachment = $this->_domainFactory->createEntity('App_Domain_HelpdeskTicketAttachment');
            $attachment->setFileName($attachmentFileName);
            $attachment->setCreatedAt(new DateTime());
            $attachment->setUser($this->_user);

            $ticket->addAttachment($attachment);
        }

        $this->_entityManager->flush();

        try {

            $mail = new Zend_Mail();

            foreach ($this->_config->settings->helpdeskManagers as $recipient) {
                $mail->addTo($recipient);
            }

            $mail->setSubject('Helpdesk ticket: ' . $data->subject);
            $mail->setBodyText($data->issue . PHP_EOL . PHP_EOL . 'Have a look (please don\'t reply to this email): ' . $this->_config->settings->secureHost . '/helpdesk/view-ticket/id/' . $ticket->getId());
            $mail->send();

        } catch (Exception $e) {
            $this->_log->err($e);
        }

        return (int) $ticket->getId();
    }

    /**
     * @return App_Service_Dto_HelpdeskTicket[]
     */
    public function getOpenTickets($offset = null, $showPerPage = null)
    {
        $tickets = array();

        foreach ($this->_helpdeskTicketRepository->getAllOpenTickets($offset, $showPerPage) as $ticket) {
            $tickets[] = $this->_dtoAssembler->assembleTicketDto($ticket);
        }

        return $tickets;
    }

    /**
     * @todo need to find a way of using SQL_CALC_FOUND_ROWS, in mappers and returned to repository
     * @return int
     */
    public function countOpenTickets()
    {
        $select = $this->_adapter->select()
            ->from('helpdesk_ticket', 'COUNT(*)')
            ->where('status = ?', App_Domain_HelpdeskTicket::STATUS_OPEN);

        $stmt = $select->query();

        return (int) $stmt->fetchColumn();
    }

    /**
     * @param int $id
     * @param string $commentText
     * @return void
     */
    public function addCommentToTicket($id, $commentText, $attachmentFileName = null)
    {
        $ticket = $this->_helpdeskTicketRepository->getById($id);

        if (null === $ticket) {
            throw new App_Service_Exception('Unable to find a ticket with given ID');
        }

        if ($ticket->getStatus() === App_Domain_HelpdeskTicket::STATUS_RESOLVED) {
            $ticket->setStatus(App_Domain_HelpdeskTicket::STATUS_OPEN);
        }

        $comment = $this->_domainFactory->createEntity('App_Domain_HelpdeskTicketComment');
        $comment->setComment($commentText);
        $comment->setCreatedAt(new DateTime());
        $comment->setUser($this->_user);

        $ticket->addComment($comment);

        if ($attachmentFileName) {

            $attachment = $this->_domainFactory->createEntity('App_Domain_HelpdeskTicketAttachment');
            $attachment->setFileName($attachmentFileName);
            $attachment->setCreatedAt(new DateTime());
            $attachment->setUser($this->_user);

            $ticket->addAttachment($attachment);
        }

        $this->_entityManager->flush();

        try {

            $mail = new Zend_Mail();

            $mail->addTo($ticket->getUser()->getEmail());

            foreach ($this->_config->settings->helpdeskManagers as $recipient) {
                $mail->addTo($recipient);
            }

            $mail->setSubject('Helpdesk comment: ' . $ticket->getSubject());
            $mail->setBodyText($commentText . PHP_EOL . PHP_EOL . 'Have a look (please don\'t reply to this email): ' . $this->_config->settings->secureHost . '/helpdesk/view-ticket/id/' . $ticket->getId());
            $mail->send();

        } catch (Exception $e) {
            $this->_log->err($e);
        }
    }

    /**
     * @param int $id
     * @param string $commentText
     * @return void
     */
    public function resolveTicket($id, $commentText, $attachmentFileName = null)
    {
        $ticket = $this->_helpdeskTicketRepository->getById($id);

        if (null === $ticket) {
            throw new App_Service_Exception('Unable to find a ticket with given ID');
        }

        $comment = $this->_domainFactory->createEntity('App_Domain_HelpdeskTicketComment');
        $comment->setComment($commentText);
        $comment->setCreatedAt(new DateTime());
        $comment->setUser($this->_user);

        $ticket->setStatus(App_Domain_HelpdeskTicket::STATUS_RESOLVED);
        $ticket->addComment($comment);

        if ($attachmentFileName) {

            $attachment = $this->_domainFactory->createEntity('App_Domain_HelpdeskTicketAttachment');
            $attachment->setFileName($attachmentFileName);
            $attachment->setCreatedAt(new DateTime());
            $attachment->setUser($this->_user);

            $ticket->addAttachment($attachment);
        }

        $this->_entityManager->flush();

        try {

            $mail = new Zend_Mail();
            $mail->addTo($ticket->getUser()->getEmail());
            $mail->setSubject('Helpdesk resolution: ' . $ticket->getSubject());
            $mail->setBodyText($commentText . PHP_EOL . PHP_EOL . 'Have a look (please don\'t reply to this email): ' . $this->_config->settings->secureHost . '/helpdesk/view-ticket/id/' . $ticket->getId());
            $mail->send();

        } catch (Exception $e) {
            $this->_log->err($e);
        }
    }

}
