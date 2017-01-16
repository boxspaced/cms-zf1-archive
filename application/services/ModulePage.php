<?php

/**
 * @todo Rename as Module service and have module related operations
 */
class App_Service_ModulePage
{

    const CURRENT_PUBLISHING_OPTIONS_CACHE_ID = 'currentPublishingOptionsModulePage%d';

    /**
     * @var Zend_Cache_Manager
     */
    protected $_cacheManager;

    /**
     * @var Zend_Log
     */
    protected $_log;

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
     * @var App_Domain_Repository_ModulePage
     */
    protected $_modulePageRepository;

    /**
     * @var App_Domain_Repository_Block
     */
    protected $_blockRepository;

    /**
     * @var App_Domain_Factory
     */
    protected $_domainFactory;

    /**
     * @param Zend_Cache_Manager $cacheManager
     * @param Zend_Log $log
     * @param Zend_Auth $auth
     * @param \Boxspaced\EntityManager\EntityManager $entityManager
     * @param App_Domain_Repository_User $userRepository
     * @param App_Domain_Repository_ModulePage $modulePageRepository
     * @param App_Domain_Repository_Block $blockRepository
     * @param App_Domain_Factory $domainFactory
     */
    public function __construct(
        Zend_Cache_Manager $cacheManager,
        Zend_Log $log,
        Zend_Auth $auth,
        \Boxspaced\EntityManager\EntityManager $entityManager,
        App_Domain_Repository_User $userRepository,
        App_Domain_Repository_ModulePage $modulePageRepository,
        App_Domain_Repository_Block $blockRepository,
        App_Domain_Factory $domainFactory
    )
    {
        $this->_cacheManager = $cacheManager;
        $this->_log = $log;
        $this->_auth = $auth;
        $this->_entityManager = $entityManager;
        $this->_userRepository = $userRepository;
        $this->_modulePageRepository = $modulePageRepository;
        $this->_blockRepository = $blockRepository;
        $this->_domainFactory = $domainFactory;

        if ($this->_auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            $this->_user = $userRepository->getById($identity->id);
        }
    }

    /**
     * @param int $id
     * @return App_Service_Dto_ModulePage
     */
    public function getModulePage($id)
    {
        $page = $this->_modulePageRepository->getById($id);

        if (null === $page) {
            throw new App_Service_Exception('Unable to find a module page with given ID');
        }

        $dto = new App_Service_Dto_ModulePage();
        $dto->id = $page->getId();
        $dto->name = $page->getName();

        return $dto;
    }

    /**
     * @param int $id
     * @return App_Service_Dto_ModulePageBlock[]
     */
    public function getModulePageBlocks($id)
    {
        $page = $this->_modulePageRepository->getById($id);

        if (null === $page) {
            throw new App_Service_Exception('Unable to find a module page with given ID');
        }

        $blocks = array();

        foreach ($page->getBlocks() as $block) {

            $blockDto = new App_Service_Dto_ModulePageBlock();
            $blockDto->name = $block->getName();
            $blockDto->adminLabel = $block->getAdminLabel();
            $blockDto->sequence = (bool) $block->getSequence();

            $blocks[] = $blockDto;
        }

        return $blocks;
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

        $page = $this->_modulePageRepository->getById($id);

        if (null === $page) {
            throw new App_Service_Exception('Unable to find a module page with given ID');
        }

        $dto = new App_Service_Dto_PublishingOptions();

        foreach ($page->getFreeBlocks() as $freeBlock) {

            if ($freeBlock->getBlock()->getStatus() !== App_Domain_Service_VersionableInterface::STATUS_PUBLISHED) {
                continue;
            }

            $freeBlockDto = new App_Service_Dto_FreeBlock();
            $freeBlockDto->name = $freeBlock->getModulePageBlock()->getName();
            $freeBlockDto->id = $freeBlock->getBlock()->getId();

            $dto->freeBlocks[] = $freeBlockDto;
        }

        foreach ($page->getBlockSequences() as $blockSequence) {

            $blockSequenceDto = new App_Service_Dto_BlockSequence();
            $blockSequenceDto->name = $blockSequence->getModulePageBlock()->getName();

            foreach ($blockSequence->getBlocks() as $blockSequenceBlock) {

                if ($blockSequenceBlock->getBlock()->getStatus() !== App_Domain_Service_VersionableInterface::STATUS_PUBLISHED) {
                    continue;
                }

                $blockSequenceBlockDto = new App_Service_Dto_BlockSequenceBlock();
                $blockSequenceBlockDto->id = $blockSequenceBlock->getBlock()->getId();
                $blockSequenceBlockDto->orderBy = $blockSequenceBlock->getOrderBy();

                $blockSequenceDto->blocks[] = $blockSequenceBlockDto;
            }

            $dto->blockSequences[] = $blockSequenceDto;
        }

        $cache->save($dto);

        return $dto;
    }

    /**
     * @param int $id
     * @return App_Service_Dto_AvailableBlockTypeOption[]
     */
    public function getAvailableBlockOptions($id)
    {
        $page = $this->_modulePageRepository->getById($id);

        if (null === $page) {
            throw new App_Service_Exception('Unable to find a module page with given ID');
        }

        $blockTypes = array();

        foreach ($this->_blockRepository->getAllLive() as $block) {

            $type = $block->getType();
            $blockTypes[$type->getName()][$block->getId()] = $block->getName();
        }

        $blockOptions = array();

        foreach ($blockTypes as $name => $blocks) {

            $blockTypeOptionDto = new App_Service_Dto_AvailableBlockTypeOption();
            $blockTypeOptionDto->name = $name;

            foreach ($blocks as $value => $label) {

                $blockOptionDto = new App_Service_Dto_AvailableBlockOption();
                $blockOptionDto->value = $value;
                $blockOptionDto->label = $label;

                $blockTypeOptionDto->blockOptions[] = $blockOptionDto;
            }

            $blockOptions[] = $blockTypeOptionDto;
        }

        return $blockOptions;
    }

    /**
     * @param int $id
     * @param App_Service_Dto_PublishingOptions $options
     * @return void
     */
    public function publish($id, App_Service_Dto_PublishingOptions $options = null)
    {
        $page = $this->_modulePageRepository->getById($id);

        if (null === $page) {
            throw new App_Service_Exception('Unable to find module page');
        }

        // Remove all blocks
        $page->deleteAllFreeBlocks();
        $page->deleteAllBlockSequences();

        // Free blocks
        foreach ($options->freeBlocks as $freeBlock) {

            $pageBlocks = $page->getBlocks()->filter(function($pageBlock) use ($freeBlock) {
                return $pageBlock->getName() === $freeBlock->name;
            });
            $pageBlock = $pageBlocks->first();

            $block = $this->_blockRepository->getById($freeBlock->id);

            if (null === $pageBlock || null === $block) {
                $this->_log->warn('Ignoring block');
                continue;
            }

            $pageFreeBlock = $this->_domainFactory->createEntity('App_Domain_ModulePageFreeBlock');
            $pageFreeBlock->setModulePageBlock($pageBlock);
            $pageFreeBlock->setBlock($block);

            $page->addFreeBlock($pageFreeBlock);
        }

        // Block sequences
        foreach ($options->blockSequences as $blockSequence) {

            if (!$blockSequence->blocks) {
                continue;
            }

            $pageBlocks = $page->getBlocks()->filter(function($pageBlock) use ($blockSequence) {
                return $pageBlock->getName() === $blockSequence->name;
            });
            $pageBlock = $pageBlocks->first();

            if (null === $pageBlock) {
                $this->_log->warn('Ignoring block sequence');
                continue;
            }

            $pageBlockSequence = $this->_domainFactory->createEntity('App_Domain_ModulePageBlockSequence');
            $pageBlockSequence->setModulePageBlock($pageBlock);

            // Blocks
            foreach ($blockSequence->blocks as $blockSequenceBlock) {

                $block = $this->_blockRepository->getById($blockSequenceBlock->id);

                if (null === $block) {
                    $this->_log->warn('Ignoring block sequence block');
                    continue;
                }

                $pageBlockSequenceBlock = $this->_domainFactory->createEntity('App_Domain_ModulePageBlockSequenceBlock');
                $pageBlockSequenceBlock->setOrderBy($blockSequenceBlock->orderBy);
                $pageBlockSequenceBlock->setBlock($block);

                $pageBlockSequence->addBlock($pageBlockSequenceBlock);
            }

            $page->addBlockSequence($pageBlockSequence);
        }

        $this->_entityManager->flush();

        // Clear cache
        $cache = $this->_cacheManager->getCache('long');
        $cache->remove(sprintf(static::CURRENT_PUBLISHING_OPTIONS_CACHE_ID, $id));
    }

}
