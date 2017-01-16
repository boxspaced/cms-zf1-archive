<?php

class App_Service_Block
{

    const MODULE_NAME = 'block';
    const CURRENT_PUBLISHING_OPTIONS_CACHE_ID = 'currentPublishingOptionsBlock%d';
    const BLOCK_CACHE_ID = 'block%d';
    const BLOCK_META_CACHE_ID = 'blockMeta%d';
    const BLOCK_TYPE_CACHE_ID = 'blockType%d';

    /**
     * @var Zend_Cache_Manager
     */
    protected $_cacheManager;

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
     * @var App_Domain_Repository_BlockType
     */
    protected $_blockTypeRepository;

    /**
     * @var App_Domain_Repository_Block
     */
    protected $_blockRepository;

    /**
     * @var App_Domain_Repository_BlockTemplate
     */
    protected $_blockTemplateRepository;

    /**
     * @var App_Domain_Repository_User
     */
    protected $_userRepository;

    /**
     * @var App_Service_Assembler_Block
     */
    protected $_dtoAssembler;

    /**
     * @var App_Domain_Service_Versioning
     */
    protected $_versioningDomainService;

    /**
     * @var App_Domain_Service_Workflow
     */
    protected $_workflowDomainService;

    /**
     * @var App_Domain_Factory
     */
    protected $_domainFactory;

    /**
     * @param Zend_Cache_Manager $cacheManager
     * @param Zend_Db_Adapter_Abstract $adapter
     * @param Zend_Auth $auth
     * @param \Boxspaced\EntityManager\EntityManager $entityManager
     * @param App_Domain_Repository_BlockType $blockTypeRepository
     * @param App_Domain_Repository_Block $blockRepository
     * @param App_Domain_Repository_BlockTemplate $blockTemplateRepository
     * @param App_Domain_Repository_User $userRepository
     * @param App_Service_Assembler_Block $dtoAssembler
     * @param App_Domain_Service_Versioning $versioningDomainService
     * @param App_Domain_Service_Workflow $workflowDomainService
     * @param App_Domain_Factory $domainFactory
     */
    public function __construct(
        Zend_Cache_Manager $cacheManager,
        Zend_Db_Adapter_Abstract $adapter,
        Zend_Auth $auth,
        \Boxspaced\EntityManager\EntityManager $entityManager,
        App_Domain_Repository_BlockType $blockTypeRepository,
        App_Domain_Repository_Block $blockRepository,
        App_Domain_Repository_BlockTemplate $blockTemplateRepository,
        App_Domain_Repository_User $userRepository,
        App_Service_Assembler_Block $dtoAssembler,
        App_Domain_Service_Versioning $versioningDomainService,
        App_Domain_Service_Workflow $workflowDomainService,
        App_Domain_Factory $domainFactory
    )
    {
        $this->_cacheManager = $cacheManager;
        $this->_adapter = $adapter;
        $this->_auth = $auth;
        $this->_entityManager = $entityManager;
        $this->_blockTypeRepository = $blockTypeRepository;
        $this->_blockRepository = $blockRepository;
        $this->_blockTemplateRepository = $blockTemplateRepository;
        $this->_userRepository = $userRepository;
        $this->_dtoAssembler = $dtoAssembler;
        $this->_versioningDomainService = $versioningDomainService;
        $this->_workflowDomainService = $workflowDomainService;
        $this->_domainFactory = $domainFactory;

        if ($this->_auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            $this->_user = $userRepository->getById($identity->id);
        }
    }

    /**
     * @return App_Service_Dto_BlockType[]
     */
    public function getTypes()
    {
        $types = array();

        foreach ($this->_blockTypeRepository->getAll() as $type) {

            $types[] = $this->_dtoAssembler->assembleBlockTypeDto($type);
        }

        return $types;
    }

    /**
     * @param int $offset
     * @param int $showPerPage
     * @return App_Service_Dto_Block[]
     */
    public function getPublishedBlocks($offset = null, $showPerPage = null)
    {
        $blocks = array();

        foreach ($this->_blockRepository->getAllPublished($offset, $showPerPage) as $block) {

            $blocks[] = $this->_dtoAssembler->assembleBlockDto($block);
        }

        return $blocks;
    }

    /**
     * @todo need to find a way of using SQL_CALC_FOUND_ROWS, in mappers and returned to repository
     * @return int
     */
    public function countPublishedBlocks()
    {
        $select = $this->_adapter->select()
            ->from('block', 'COUNT(*)')
            ->where('status = ?', App_Domain_Service_VersionableInterface::STATUS_PUBLISHED);

        $stmt = $select->query();

        return (int) $stmt->fetchColumn();
    }

    /**
     * @return App_Service_Dto_BlockType
     */
    public function getType($id)
    {
        $cache = $this->_cacheManager->getCache('long');

        if (false !== $cache->test(sprintf(static::BLOCK_TYPE_CACHE_ID, $id))) {
            return $cache->load(sprintf(static::BLOCK_TYPE_CACHE_ID, $id));
        }

        $type = $this->_blockTypeRepository->getById($id);

        if (null === $type) {
            throw new App_Service_Exception('Unable to find type with given ID');
        }

        $dto = $this->_dtoAssembler->assembleBlockTypeDto($type);
        $cache->save($dto);

        return $dto;
    }

    /**
     * @param int $id
     * @return App_Service_Dto_Block
     */
    public function getCacheControlledBlock($id)
    {
        $cache = $this->_cacheManager->getCache('long');

        if (false !== $cache->test(sprintf(static::BLOCK_CACHE_ID, $id))) {
            return $cache->load(sprintf(static::BLOCK_CACHE_ID, $id));
        }

        $dto = $this->getBlock($id);
        $cache->save($dto);

        return $dto;
    }

    /**
     * @param int $id
     * @return App_Service_Dto_Block
     */
    public function getBlock($id)
    {
        $block = $this->_blockRepository->getById($id);

        if (null === $block) {
            throw new App_Service_Exception('Unable to find an block with given ID');
        }

        return $this->_dtoAssembler->assembleBlockDto($block);
    }

    /**
     * @param int $id
     * @return App_Service_Dto_BlockMeta
     */
    public function getCacheControlledBlockMeta($id)
    {
        $cache = $this->_cacheManager->getCache('long');

        if (false !== $cache->test(sprintf(static::BLOCK_META_CACHE_ID, $id))) {
            return $cache->load(sprintf(static::BLOCK_META_CACHE_ID, $id));
        }

        $dto = $this->getBlockMeta($id);
        $cache->save($dto);

        return $dto;
    }

    /**
     * @param int $id
     * @return App_Service_Dto_BlockMeta
     */
    public function getBlockMeta($id)
    {
        $block = $this->_blockRepository->getById($id);

        if (null === $block) {
            throw new App_Service_Exception('Unable to find an block with given ID');
        }

        return $this->_dtoAssembler->assembleBlockMetaDto($block);
    }

    /**
     * @param int $id
     * @return App_Service_Dto_PublishingOptions
     */
    public function getCurrentPublishingOptions($id)
    {
        $cache = $this->_cacheManager->getCache('long');

        if (false !== $cache->test(sprintf(static::CURRENT_PUBLISHING_OPTIONS_CACHE_ID, $id))) {
            return $cache->load(sprintf(static::CURRENT_PUBLISHING_OPTIONS_CACHE_ID, $id));
        }

        $block = $this->_blockRepository->getById($id);

        if (null === $block) {
            throw new App_Service_Exception('Unable to find an block with given ID');
        }

        if ($block->getStatus() !== App_Domain_Service_VersionableInterface::STATUS_PUBLISHED) {
            throw new App_Service_Exception('Block is not published');
        }

        $dto = new App_Service_Dto_PublishingOptions();
        $dto->name = $block->getName();
        $dto->templateId = $block->getTemplate()->getId();
        $dto->liveFrom = $block->getLiveFrom();
        $dto->expiresEnd = $block->getExpiresEnd();

        $cache->save($dto);

        return $dto;
    }

    /**
     * @param string $name
     * @param int $typeId
     * @return int
     */
    public function createDraft($name, $typeId)
    {
        $type = $this->_blockTypeRepository->getById($typeId);

        if (null === $type) {
            throw new App_Service_Exception('Unable to find type provided');
        }

        $draft = $this->_domainFactory->createEntity('App_Domain_Block');
        $draft->setName($name);
        $draft->setType($type);

        $versionableDraft = new App_Domain_Adapter_VersionableBlock($draft);
        $workflowableDraft = new App_Domain_Adapter_WorkflowableBlock($draft);

        $this->_versioningDomainService->createDraft($versionableDraft, $this->_user);
        $this->_workflowDomainService->moveToAuthoring($workflowableDraft);

        $this->_entityManager->flush();

        return $draft->getId();
    }

    /**
     * @param int $id Published block's ID
     * @return int
     */
    public function createRevision($id)
    {
        $revisionOf = $this->_blockRepository->getById($id);

        if (null === $revisionOf) {
            throw new App_Service_Exception('Unable to find an block with given ID');
        }

        if ($revisionOf->getStatus() !== App_Domain_Service_VersionableInterface::STATUS_PUBLISHED) {
            throw new App_Service_Exception('The block you are creating a revision of must be published');
        }

        $revision = $this->_domainFactory->createEntity('App_Domain_Block');
        $revision->setType($revisionOf->getType());

        $versionableRevision = new App_Domain_Adapter_VersionableBlock($revision);
        $versionableRevisionOf = new App_Domain_Adapter_VersionableBlock($revisionOf);
        $workflowableRevision = new App_Domain_Adapter_WorkflowableBlock($revision);

        $this->_versioningDomainService->createRevision($versionableRevision, $versionableRevisionOf, $this->_user);
        $this->_workflowDomainService->moveToAuthoring($workflowableRevision);

        $this->_entityManager->flush();

        return $revision->getId();
    }

    /**
     * @param int $id Draft or revision ID
     * @param App_Service_Dto_Block $data
     * @param string $noteText
     * @return void
     */
    public function edit($id, App_Service_Dto_Block $data, $noteText = '')
    {
        $block = $this->_blockRepository->getById($id);

        if (null === $block) {
            throw new App_Service_Exception('Unable to find block');
        }

        if (!in_array($block->getStatus(), array(
            App_Domain_Service_VersionableInterface::STATUS_DRAFT,
            App_Domain_Service_VersionableInterface::STATUS_REVISION,
        ))) {
            throw new App_Service_Exception('You can only edit a draft or revision');
        }

        foreach ($data->fields as $dataField) {

            $field = $this->_domainFactory->createEntity('App_Domain_BlockField');
            $field->setName($dataField->name);
            $field->setValue($dataField->value);

            $block->addField($field);
        }

        if ($noteText) {

            $note = $this->_domainFactory->createEntity('App_Domain_BlockNote');
            $note->setText($noteText);
            $note->setUser($this->_user);
            $note->setCreatedTime(new DateTime());

            $block->addNote($note);
        }

        $this->_entityManager->flush();
    }

    /**
     * @param int $id
     * @param App_Service_Dto_PublishingOptions $options
     * @return void
     */
    public function publish($id, App_Service_Dto_PublishingOptions $options = null)
    {
        $block = $this->_blockRepository->getById($id);

        if (null === $block) {
            throw new App_Service_Exception('Unable to find block');
        }

        if (!$options && in_array($block->getStatus(), array(
            App_Domain_Service_VersionableInterface::STATUS_PUBLISHED,
            App_Domain_Service_VersionableInterface::STATUS_DRAFT,
        ))) {
            throw new App_Service_Exception('Block status requires publishing options');
        }

        $versionableBlock = new App_Domain_Adapter_VersionableBlock($block);
        $workflowableBlock = new App_Domain_Adapter_WorkflowableBlock($block);

        switch ($block->getStatus()) {

            case App_Domain_Service_VersionableInterface::STATUS_PUBLISHED:
            case App_Domain_Service_VersionableInterface::STATUS_DRAFT:

                $block->getName($options->name);
                $block->setLiveFrom($options->liveFrom);
                $block->setExpiresEnd($options->expiresEnd);

                $template = $this->_blockTemplateRepository->getById($options->templateId);
                $block->setTemplate($template);

                if ($block->getStatus() === App_Domain_Service_VersionableInterface::STATUS_DRAFT) {
                    $this->_versioningDomainService->publishDraft($versionableBlock);
                    $this->_workflowDomainService->removeFromWorkflow($workflowableBlock);
                }
                break;

            case App_Domain_Service_VersionableInterface::STATUS_REVISION:

                $this->_versioningDomainService->publishRevision($versionableBlock);
                $this->_workflowDomainService->removeFromWorkflow($workflowableBlock);
                break;

            case App_Domain_Service_VersionableInterface::STATUS_ROLLBACK:

                $this->_versioningDomainService->restoreRollback($versionableBlock);
                break;

            case App_Domain_Service_VersionableInterface::STATUS_DELETED:

                $this->_versioningDomainService->restoreDeleted($versionableBlock);
                break;

            default:
                // No default
        }

        $this->_entityManager->flush();

        // Clear cache
        $cache = $this->_cacheManager->getCache('long');
        $cache->remove(sprintf(static::CURRENT_PUBLISHING_OPTIONS_CACHE_ID, $id));
        $versionOf = $block->getVersionOf();
        if ($versionOf) {
            $cache->remove(sprintf(static::BLOCK_CACHE_ID, $versionOf->getId()));
            $cache->remove(sprintf(static::BLOCK_META_CACHE_ID, $versionOf->getId()));
        }
    }

    /**
     * @param int $id Published block's ID
     * @return void
     */
    public function delete($id)
    {
        $block = $this->_blockRepository->getById($id);

        if (null === $block) {
            throw new App_Service_Exception('Unable to find block');
        }

        if ($block->getStatus() !== App_Domain_Service_VersionableInterface::STATUS_PUBLISHED) {
            throw new App_Service_Exception('Block must be published');
        }

        $block->setTemplate(null);
        $block->setLiveFrom(null);
        $block->setExpiresEnd(null);

        $versionableBlock = new App_Domain_Adapter_VersionableBlock($block);
        $this->_versioningDomainService->deletePublished($versionableBlock);

        $versionsOf = $this->_blockRepository->getAllVersionOf($block->getId());

        foreach ($versionsOf as $versionOf) {
            $this->_blockRepository->delete($versionOf);
        }

        $this->_entityManager->flush();

        // Clear cache
        $cache = $this->_cacheManager->getCache('long');
        $cache->remove(sprintf(static::CURRENT_PUBLISHING_OPTIONS_CACHE_ID, $id));
        $cache->remove(sprintf(static::BLOCK_CACHE_ID, $id));
        $cache->remove(sprintf(static::BLOCK_META_CACHE_ID, $id));
    }

}
