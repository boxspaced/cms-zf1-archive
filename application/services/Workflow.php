<?php

class App_Service_Workflow
{

    const WORKFLOW_STATUS_CURRENT = 'Current';
    const WORKFLOW_STATUS_UPDATE = 'Update';
    const WORKFLOW_STATUS_NEW = 'New';

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
     * @var App_Service_Assembler_Workflow
     */
    protected $_dtoAssembler;

    /**
     * @var App_Domain_Service_Workflow
     */
    protected $_workflowDomainService;

    /**
     * @var App_Domain_Factory
     */
    protected $_domainFactory;

    /**
     * @param Zend_Db_Adapter_Abstract $adapter
     * @param Zend_Auth $auth
     * @param \Boxspaced\EntityManager\EntityManager $entityManager
     * @param App_Domain_Repository_User $userRepository
     * @param App_Service_Assembler_Workflow $dtoAssembler
     * @param App_Domain_Service_Workflow $workflowDomainService
     * @param App_Domain_Factory $domainFactory
     */
    public function __construct(
        Zend_Db_Adapter_Abstract $adapter,
        Zend_Auth $auth,
        \Boxspaced\EntityManager\EntityManager $entityManager,
        App_Domain_Repository_User $userRepository,
        App_Service_Assembler_Workflow $dtoAssembler,
        App_Domain_Service_Workflow $workflowDomainService,
        App_Domain_Factory $domainFactory
    )
    {
        $this->_adapter = $adapter;
        $this->_auth = $auth;
        $this->_entityManager = $entityManager;
        $this->_userRepository = $userRepository;
        $this->_dtoAssembler = $dtoAssembler;
        $this->_workflowDomainService = $workflowDomainService;
        $this->_domainFactory = $domainFactory;

        if ($this->_auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            $this->_user = $userRepository->getById($identity->id);
        }
    }

    /**
     * @param string $module
     * @param int $id
     * @return string
     */
    public function getStatus($module, $id)
    {
        $content = $this->_getContent($module, $id);
        $workflowableContent = $this->_getWorkflowableContent($content);

        if ($workflowableContent->getStatus() === App_Domain_Service_VersionableInterface::STATUS_PUBLISHED) {
            return static::WORKFLOW_STATUS_CURRENT;
        }

        if ($workflowableContent->getStatus() === App_Domain_Service_VersionableInterface::STATUS_REVISION) {
            return static::WORKFLOW_STATUS_UPDATE;
        }

        return static::WORKFLOW_STATUS_NEW;
    }

    /**
     * @todo delete via Item or Block service, like you would edit
     * @param string $module
     * @param int $id
     * @return void
     */
    public function delete($module, $id)
    {
        $content = $this->_getContent($module, $id);

        if (!in_array($content->getStatus(), array(
            App_Domain_Service_VersionableInterface::STATUS_DRAFT,
            App_Domain_Service_VersionableInterface::STATUS_REVISION,
        ))) {
            throw new App_Service_Exception('You can only delete a draft or revision');
        }

        if (!$content->getWorkflowStage()) {
            throw new App_Service_Exception('Content is not in workflow');
        }

        if (
            $content->getAuthor() !== $this->_user
            && $content->getWorkflowStage() !== App_Domain_Service_WorkflowableInterface::WORKFLOW_STAGE_PUBLISHING
        ) {
            throw new App_Service_Exception('Content is not authored by user');
        }

        if (
            !($content instanceof App_Domain_Block)
            && $content->getStatus() === App_Domain_Service_VersionableInterface::STATUS_DRAFT
        ) {

            if ($content->getRoute()) {
                $this->_entityManager->delete($content->getRoute());
                $content->setRoute(null);
            }

            if ($content->getProvisionalLocation()) {
                $this->_entityManager->delete($content->getProvisionalLocation());
                $content->setProvisionalLocation(null);
            }
        }

        $this->_entityManager->delete($content);

        $this->_entityManager->flush();
    }

    /**
     * @param string $module
     * @param int $id
     * @return void
     */
    public function moveToPublishing($module, $id)
    {
        $content = $this->_getContent($module, $id);
        $workflowableContent = $this->_getWorkflowableContent($content);

        $this->_workflowDomainService->moveToPublishing($workflowableContent);

        $this->_entityManager->flush();
    }

    /**
     * @param string $module
     * @param int $id
     * @param string $noteText
     * @return void
     */
    public function sendBackToAuthor($module, $id, $noteText)
    {
        $content = $this->_getContent($module, $id);
        $workflowableContent = $this->_getWorkflowableContent($content);

        $this->_workflowDomainService->sendBackToAuthor($workflowableContent);

        if ($noteText) {

            $note = $this->_domainFactory->createEntity("App_Domain_{$type}Note");
            $note->setText($noteText);
            $note->setUser($this->_user);
            $note->setCreatedTime(new DateTime());

            $content->addNote($note);
        }

        $this->_entityManager->flush();
    }

    /**
     * @param string $module
     * @param int $id
     * @return mixed
     */
    protected function _getContent($module, $id)
    {
        $type = ucfirst(strtolower($module));
        $content = $this->_entityManager->find('App_Domain_' . $type, $id);

        if (null === $content) {
            throw new App_Service_Exception('Unable to find content');
        }

        return $content;
    }

    /**
     * @param mixed $content
     * @return App_Domain_Service_WorkflowableInterface
     */
    protected function _getWorkflowableContent($content)
    {
        $parts = explode('_', get_class($content));

        $adapter = 'App_Domain_Adapter_Workflowable' . array_pop($parts);

        return new $adapter($content);
    }

    /**
     * @param int $offset
     * @param int $showPerPage
     * @return App_Service_Dto_WorkflowContent[]
     */
    public function getContentInAuthoring($offset = null, $showPerPage = null)
    {
        $sql = 'SELECT everything.* '
             . $this->_createAuthoringFromSql()
             . ' ORDER BY authored_time DESC';

        if (!is_null($offset) && !is_null($showPerPage)) {
            $sql .= sprintf(' LIMIT %d, %d', $offset, $showPerPage);
        }

        $stmt = $this->_adapter->query($sql, array(
            ':userId' => $this->_user->getId(),
        ));

        $results = $stmt->fetchAll();

        $contentInAuthoring = array();

        foreach ($results as $result) {

            $content = $this->_entityManager->find($result['type'], $result['id']);
            $assemblableContent = $this->_getAssemblableContent($content);

            $contentInAuthoring[] = $this->_dtoAssembler->assembleContentDto($assemblableContent);
        }

        return $contentInAuthoring;
    }

    /**
     * @return int
     */
    public function countContentInAuthoring()
    {
        $sql = 'SELECT COUNT(*) AS total ' . $this->_createAuthoringFromSql();

        $stmt = $this->_adapter->query($sql, array(
            ':userId' => $this->_user->getId(),
        ));

        return (int) $stmt->fetchColumn();
    }

    /**
     * @return string
     */
    protected function _createAuthoringFromSql()
    {
        return "
            FROM (
                SELECT
                    'App_Domain_Item' AS type,
                    id,
                    authored_time
                FROM item
                WHERE
                    author_id = :userId
                    AND workflow_stage IS NOT NULL
                UNION
                SELECT
                    'App_Domain_Block' AS type,
                    id,
                    authored_time
                FROM block
                WHERE
                    author_id = :userId
                    AND workflow_stage IS NOT NULL
            ) AS everything
        ";
    }

    /**
     * @param int $offset
     * @param int $showPerPage
     * @return App_Service_Dto_WorkflowContent[]
     */
    public function getContentInPublishing($offset = null, $showPerPage = null)
    {
        $sql = 'SELECT everything.* '
             . $this->_createPublishingFromSql()
             . ' ORDER BY authored_time DESC';

        if (!is_null($offset) && !is_null($showPerPage)) {
            $sql .= sprintf(' LIMIT %d, %d', $offset, $showPerPage);
        }

        $stmt = $this->_adapter->query($sql, array(
            ':userId' => $this->_user->getId(),
        ));

        $results = $stmt->fetchAll();

        $contentInPublishing = array();

        foreach ($results as $result) {

            $content = $this->_entityManager->find($result['type'], $result['id']);
            $assemblableContent = $this->_getAssemblableContent($content);

            $contentInPublishing[] = $this->_dtoAssembler->assembleContentDto($assemblableContent);
        }

        return $contentInPublishing;
    }

    /**
     * @return int
     */
    public function countContentInPublishing()
    {
        $sql = 'SELECT COUNT(*) AS total ' . $this->_createPublishingFromSql();

        $stmt = $this->_adapter->query($sql);

        return (int) $stmt->fetchColumn();
    }

    /**
     * @return string
     */
    protected function _createPublishingFromSql()
    {
        return "
            FROM (
                SELECT
                    'App_Domain_Item' AS type,
                    id,
                    authored_time
                FROM item
                WHERE
                    workflow_stage = 'PUBLISHING'
                UNION
                SELECT
                    'App_Domain_Block' AS type,
                    id,
                    authored_time
                FROM block
                WHERE
                    workflow_stage = 'PUBLISHING'
            ) AS everything
        ";
    }

    /**
     * @param mixed $content
     * @return App_Service_Workflow_AssemblableContentInterface
     */
    protected function _getAssemblableContent($content)
    {
        $parts = explode('_', get_class($content));

        $adapter = 'App_Domain_Adapter_AssemblableWorkflow' . array_pop($parts);

        return new $adapter($content);
    }

}
