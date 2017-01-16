<?php

class App_Service_Item
{

    const MODULE_NAME = 'item';
    const PUBLISH_TO_MENU = 'Menu';
    const PUBLISH_TO_STANDALONE = 'Standalone';
    const CURRENT_PUBLISHING_OPTIONS_CACHE_ID = 'currentPublishingOptionsItem%d';
    const ITEM_CACHE_ID = 'item%d';
    const ITEM_META_CACHE_ID = 'itemMeta%d';
    const ITEM_TYPE_CACHE_ID = 'itemType%d';

    /**
     * @var Zend_Cache_Manager
     */
    protected $_cacheManager;

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
     * @var App_Domain_Repository_ItemType
     */
    protected $_itemTypeRepository;

    /**
     * @var App_Domain_Repository_Item
     */
    protected $_itemRepository;

    /**
     * @var App_Domain_Repository_ItemTeaserTemplate
     */
    protected $_itemTeaserTemplateRepository;

    /**
     * @var App_Domain_Repository_ItemTemplate
     */
    protected $_itemTemplateRepository;

    /**
     * @var App_Domain_Repository_User
     */
    protected $_userRepository;

    /**
     * @var App_Domain_Repository_Module
     */
    protected $_moduleRepository;

    /**
     * @var App_Domain_Repository_Block
     */
    protected $_blockRepository;

    /**
     * @var App_Domain_Repository_Menu
     */
    protected $_menuRepository;

    /**
     * @var App_Service_Assembler_Item
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
     * @param Zend_Log $log
     * @param Zend_Config $config
     * @param Zend_Db_Adapter_Abstract $adapter
     * @param Zend_Auth $auth
     * @param \Boxspaced\EntityManager\EntityManager $entityManager
     * @param App_Domain_Repository_ItemType $itemTypeRepository
     * @param App_Domain_Repository_Item $itemRepository
     * @param App_Domain_Repository_ItemTeaserTemplate $itemTeaserTemplateRepository
     * @param App_Domain_Repository_ItemTemplate $itemTemplateRepository
     * @param App_Domain_Repository_User $userRepository
     * @param App_Domain_Repository_Module $moduleRepository
     * @param App_Domain_Repository_Block $blockRepository
     * @param App_Domain_Repository_Menu $menuRepository
     * @param App_Service_Assembler_Item $dtoAssembler
     * @param App_Domain_Service_Versioning $versioningDomainService
     * @param App_Domain_Service_Workflow $workflowDomainService
     * @param App_Domain_Factory $domainFactory
     */
    public function __construct(
        Zend_Cache_Manager $cacheManager,
        Zend_Log $log,
        Zend_Config $config,
        Zend_Db_Adapter_Abstract $adapter,
        Zend_Auth $auth,
        \Boxspaced\EntityManager\EntityManager $entityManager,
        App_Domain_Repository_ItemType $itemTypeRepository,
        App_Domain_Repository_Item $itemRepository,
        App_Domain_Repository_ItemTeaserTemplate $itemTeaserTemplateRepository,
        App_Domain_Repository_ItemTemplate $itemTemplateRepository,
        App_Domain_Repository_User $userRepository,
        App_Domain_Repository_Module $moduleRepository,
        App_Domain_Repository_Block $blockRepository,
        App_Domain_Repository_Menu $menuRepository,
        App_Service_Assembler_Item $dtoAssembler,
        App_Domain_Service_Versioning $versioningDomainService,
        App_Domain_Service_Workflow $workflowDomainService,
        App_Domain_Factory $domainFactory
    )
    {
        $this->_cacheManager = $cacheManager;
        $this->_log = $log;
        $this->_config = $config;
        $this->_adapter = $adapter;
        $this->_auth = $auth;
        $this->_entityManager = $entityManager;
        $this->_itemTypeRepository = $itemTypeRepository;
        $this->_itemRepository = $itemRepository;
        $this->_itemTeaserTemplateRepository = $itemTeaserTemplateRepository;
        $this->_itemTemplateRepository = $itemTemplateRepository;
        $this->_userRepository = $userRepository;
        $this->_moduleRepository = $moduleRepository;
        $this->_blockRepository = $blockRepository;
        $this->_menuRepository = $menuRepository;
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
     * @return void
     */
    public function reindex()
    {
        $path = $this->_config->settings->siteSearchIndexPath;

        if (!$path) {
            throw new App_Service_Exception('No path provided');
        }

        My_Util::deleteDir($path);

        $index = Zend_Search_Lucene::create($path);

        foreach ($this->_itemRepository->getAllLive() as $item) {

            $doc = new Zend_Search_Lucene_Document();

            $doc->addField(Zend_Search_Lucene_Field::Keyword('module', 'item', 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::Keyword('contentId', $item->getId(), 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::Keyword('slug', $item->getRoute()->getSlug(), 'utf-8'));

            $doc->addField(Zend_Search_Lucene_Field::Text('title', $item->getTitle(), 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::UnStored('keywords', $item->getMetaKeywords(), 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::UnStored('description', $item->getMetaDescription(), 'utf-8'));

            $content = '';

            foreach ($item->getFields() as $field) {
                $content .= strip_tags($field->getValue());
            }

            foreach ($item->getParts() as $part) {
                foreach ($part->getFields() as $field) {
                    $content .= strip_tags($field->getValue());
                }
            }

            $doc->addField(Zend_Search_Lucene_Field::UnStored('contents', $content), 'utf-8');

            $index->addDocument($doc);
        }

        $index->commit();
    }

    /**
     * @return App_Service_Dto_ItemType[]
     */
    public function getTypes()
    {
        $types = array();

        foreach ($this->_itemTypeRepository->getAll() as $type) {

            $types[] = $this->_dtoAssembler->assembleItemTypeDto($type);
        }

        return $types;
    }

    /**
     * @return App_Service_Dto_ItemType
     */
    public function getType($id)
    {
        $cache = $this->_cacheManager->getCache('long');

        if (false !== $cache->test(sprintf(static::ITEM_TYPE_CACHE_ID, $id))) {
            return $cache->load(sprintf(static::ITEM_TYPE_CACHE_ID, $id));
        }

        $type = $this->_itemTypeRepository->getById($id);

        if (null === $type) {
            throw new App_Service_Exception('Unable to find type with given ID');
        }

        $dto = $this->_dtoAssembler->assembleItemTypeDto($type);
        $cache->save($dto);

        return $dto;
    }

    /**
     * @param int $id
     * @return App_Service_Dto_Item
     */
    public function getCacheControlledItem($id)
    {
        $cache = $this->_cacheManager->getCache('long');

        if (false !== $cache->test(sprintf(static::ITEM_CACHE_ID, $id))) {
            return $cache->load(sprintf(static::ITEM_CACHE_ID, $id));
        }

        $dto = $this->getItem($id);
        $cache->save($dto);

        return $dto;
    }

    /**
     * @param int $id
     * @return App_Service_Dto_Item
     */
    public function getItem($id)
    {
        $item = $this->_itemRepository->getById($id);

        if (null === $item) {
            throw new App_Service_Exception('Unable to find an item with given ID');
        }

        return $this->_dtoAssembler->assembleItemDto($item);
    }

    /**
     * @param int $id
     * @return App_Service_Dto_ItemMeta
     */
    public function getCacheControlledItemMeta($id)
    {
        $cache = $this->_cacheManager->getCache('long');

        if (false !== $cache->test(sprintf(static::ITEM_META_CACHE_ID, $id))) {
            return $cache->load(sprintf(static::ITEM_META_CACHE_ID, $id));
        }

        $dto = $this->getItemMeta($id);
        $cache->save($dto);

        return $dto;
    }

    /**
     * @param int $id
     * @return App_Service_Dto_ItemMeta
     */
    public function getItemMeta($id)
    {
        $item = $this->_itemRepository->getById($id);

        if (null === $item) {
            throw new App_Service_Exception('Unable to find an item with given ID');
        }

        return $this->_dtoAssembler->assembleItemMetaDto($item);
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

        $item = $this->_itemRepository->getById($id);

        if (null === $item) {
            throw new App_Service_Exception('Unable to find an item with given ID');
        }

        if ($item->getStatus() !== App_Domain_Service_VersionableInterface::STATUS_PUBLISHED) {
            // @todo return null
            throw new App_Service_Exception('Item is not published');
        }

        $dto = new App_Service_Dto_PublishingOptions();
        $dto->name = $item->getRoute()->getSlug();
        $dto->colourScheme = $item->getColourScheme();
        $dto->teaserTemplateId = $item->getTeaserTemplate()->getId();
        $dto->templateId = $item->getTemplate()->getId();
        $dto->liveFrom = $item->getLiveFrom();
        $dto->expiresEnd = $item->getExpiresEnd();
        $dto->to = $item->getPublishedTo();

        if ($item->getPublishedTo() === static::PUBLISH_TO_MENU) {

            foreach ($this->_getFlattenedMenu() as $flattenedMenuItem) {

                $menuItem = $flattenedMenuItem['item'];

                if ($this->_getContentByMenuItem($menuItem) !== $item) {
                    continue;
                }

                if ($menuItem->getParentMenuItem()) {
                    $dto->beneathMenuItemId = $menuItem->getParentMenuItem()->getId();
                } else {
                    $dto->beneathMenuItemId = 0;
                }
            }

        }

        foreach ($item->getFreeBlocks() as $freeBlock) {

            if ($freeBlock->getBlock()->getStatus() !== App_Domain_Service_VersionableInterface::STATUS_PUBLISHED) {
                continue;
            }

            $freeBlockDto = new App_Service_Dto_FreeBlock();
            $freeBlockDto->name = $freeBlock->getTemplateBlock()->getName();
            $freeBlockDto->id = $freeBlock->getBlock()->getId();

            $dto->freeBlocks[] = $freeBlockDto;
        }

        foreach ($item->getBlockSequences() as $blockSequence) {

            $blockSequenceDto = new App_Service_Dto_BlockSequence();
            $blockSequenceDto->name = $blockSequence->getTemplateBlock()->getName();

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
     * @param App_Domain_MenuItem $menuItem
     * @return \Boxspaced\EntityManager\Entity\AbstractEntity
     */
    protected function _getContentByMenuItem(App_Domain_MenuItem $menuItem)
    {
        $route = $menuItem->getRoute();

        if (is_numeric($route->getIdentifier())) {

            $module = $route->getModule();

            $entityName = rtrim($module->getName(), 's');
            $entityName = ucfirst(Zend_Filter::filterStatic($entityName, 'Word_DashToCamelCase'));

            return $this->_entityManager->find('App_Domain_' . $entityName, $route->getIdentifier());
        }

        return null;
    }

    /**
     * @param int $id
     * @return App_Service_Dto_ProvisionalLocation
     */
    public function getProvisionalLocation($id)
    {
        $item = $this->_itemRepository->getById($id);

        if (null === $item) {
            throw new App_Service_Exception('Unable to find an item with given ID');
        }

        if ($item->getProvisionalLocation()) {
            return $this->_dtoAssembler->assembleProvisionalLocationDto($item->getProvisionalLocation());
        }

        return null;
    }

    /**
     * @param int $id
     * @return App_Service_Dto_AvailableLocationOptions
     */
    public function getAvailableLocationOptions($id)
    {
        $item = $this->_itemRepository->getById($id);

        if (null === $item) {
            throw new App_Service_Exception('Unable to find an item with given ID');
        }

        $dto = new App_Service_Dto_AvailableLocationOptions();

        // Available locations
        $availableToOptions = array(
            static::PUBLISH_TO_MENU => 'Menu',
            static::PUBLISH_TO_STANDALONE => 'Standalone',
        );

        // Check if has child menu items and can therefore go standalone
        foreach ($this->_getFlattenedMenu() as $flattenedMenuItem) {

            $menuItem = $flattenedMenuItem['item'];

            if ($this->_getContentByMenuItem($menuItem) !== $item) {
                continue;
            }

            if (count($menuItem->getItems())) {
                unset($availableToOptions[static::PUBLISH_TO_STANDALONE]);
            }

            break;
        }

        foreach ($availableToOptions as $value => $label) {

            $toOptionDto = new App_Service_Dto_AvailableLocationOption();
            $toOptionDto->value = $value;
            $toOptionDto->label = $label;

            $dto->toOptions[] = $toOptionDto;
        }

        // Available menu positions
        foreach ($this->_getFlattenedMenu() as $flattenedMenuItem) {

            $menuItem = $flattenedMenuItem['item'];
            $level = $flattenedMenuItem['level'];

            if ($this->_getContentByMenuItem($menuItem) === $item) {
                continue;
            }

            if ($level >= $this->_config->settings->maxMenuLevels) {
                continue;
            }

            $menuItemDto = new App_Service_Dto_AvailableLocationOption();
            $menuItemDto->value = $menuItem->getId();

            if ($menuItem->getExternal()) {
                $menuItemDto->label = $menuItem->getExternal();
            } else {
                $menuItemDto->label = $menuItem->getRoute()->getSlug();
            }

            $menuItemDto->level = $level;

            $dto->beneathMenuItemOptions[] = $menuItemDto;
        }

        return $dto;
    }

    /**
     * @param int $id
     * @return App_Service_Dto_AvailableBlockTypeOption[]
     */
    public function getAvailableBlockOptions($id)
    {
        $item = $this->_itemRepository->getById($id);

        if (null === $item) {
            throw new App_Service_Exception('Unable to find an item with given ID');
        }

        $blockTypes = array();

        foreach ($this->_blockRepository->getAllPublished() as $block) {

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
     * @return App_Service_Dto_AvailableColourSchemeOption[]
     */
    public function getAvailableColourSchemeOptions()
    {
        $colours = array(
            'dark-blue',
            'light-blue',
            'lime-green',
            'red',
        );

        $colourSchemeOptions = array();

        foreach ($colours as $colour) {

            $dto = new App_Service_Dto_AvailableColourSchemeOption();
            $dto->value = $colour;
            $dto->label = $colour;

            $colourSchemeOptions[] = $dto;
        }

        return $colourSchemeOptions;
    }

    /**
     * @param string $name
     * @param int $typeId
     * @param App_Service_Dto_ProvisionalLocation $provisionalLocation
     * @return int
     */
    public function createDraft(
        $name,
        $typeId,
        App_Service_Dto_ProvisionalLocation $provisionalLocation = null
    )
    {
        $type = $this->_itemTypeRepository->getById($typeId);

        if ($type === null) {
            throw new App_Service_Exception('Unable to find type provided');
        }

        if ($provisionalLocation) {

            $to = $provisionalLocation->to;
            $beneathMenuItemId = $provisionalLocation->beneathMenuItemId;

            if (!in_array($to, array(
                static::PUBLISH_TO_MENU,
                static::PUBLISH_TO_STANDALONE,
            ))) {
                throw new App_Service_Exception('Provisional location: \'to\' is invalid');
            }

            $provisionalLocation = $this->_domainFactory->createEntity('App_Domain_ProvisionalLocation');
            $provisionalLocation->setTo($to);
            $provisionalLocation->setBeneathMenuItemId($beneathMenuItemId);
        }

        $draft = $this->_domainFactory->createEntity('App_Domain_Item');
        $draft->setType($type);

        if ($provisionalLocation) {
            $draft->setProvisionalLocation($provisionalLocation);
        }

        $versionableDraft = new App_Domain_Adapter_VersionableItem($draft);
        $workflowableDraft = new App_Domain_Adapter_WorkflowableItem($draft);

        $this->_versioningDomainService->createDraft($versionableDraft, $this->_user);
        $this->_workflowDomainService->moveToAuthoring($workflowableDraft);

        $this->_entityManager->flush();

        $module = $this->_moduleRepository->getByName(static::MODULE_NAME);

        $route = $this->_domainFactory->createEntity('App_Domain_Route');
        $route->setSlug($name);
        $route->setIdentifier($draft->getId());

        $draft->setRoute($route);
        $module->addRoute($route);

        $this->_entityManager->flush();

        // Clear cache
        $cache = $this->_cacheManager->getCache('long');
        $cache->remove(App_Plugin_Routes::ROUTES_CACHE_ID);

        return $draft->getId();
    }

    /**
     * @param int $id Published item's ID
     * @return int
     */
    public function createRevision($id)
    {
        $revisionOf = $this->_itemRepository->getById($id);

        if (null === $revisionOf) {
            throw new App_Service_Exception('Unable to find an item with given ID');
        }

        if ($revisionOf->getStatus() !== App_Domain_Service_VersionableInterface::STATUS_PUBLISHED) {
            throw new App_Service_Exception('The item you are creating a revision of must be published');
        }

        $revision = $this->_domainFactory->createEntity('App_Domain_Item');
        $revision->setType($revisionOf->getType());

        $versionableRevision = new App_Domain_Adapter_VersionableItem($revision);
        $versionableRevisionOf = new App_Domain_Adapter_VersionableItem($revisionOf);
        $workflowableRevision = new App_Domain_Adapter_WorkflowableItem($revision);

        $this->_versioningDomainService->createRevision($versionableRevision, $versionableRevisionOf, $this->_user);
        $this->_workflowDomainService->moveToAuthoring($workflowableRevision);

        $this->_entityManager->flush();

        return $revision->getId();
    }

    /**
     * @param int $id Draft or revision ID
     * @param App_Service_Dto_Item $data
     * @param string $noteText
     * @return void
     */
    public function edit($id, App_Service_Dto_Item $data, $noteText = '')
    {
        $item = $this->_itemRepository->getById($id);

        if (null === $item) {
            throw new App_Service_Exception('Unable to find item');
        }

        if (!in_array($item->getStatus(), array(
            App_Domain_Service_VersionableInterface::STATUS_DRAFT,
            App_Domain_Service_VersionableInterface::STATUS_REVISION,
        ))) {
            throw new App_Service_Exception('You can only edit a draft or revision');
        }

        $item->deleteAllFields();
        $item->deleteAllParts();

        $item->setNavText($data->navText);
        $item->setTitle($data->title);
        $item->setMetaKeywords($data->metaKeywords);
        $item->setMetaDescription($data->metaDescription);

        foreach ($data->fields as $dataField) {

            $field = $this->_domainFactory->createEntity('App_Domain_ItemField');
            $field->setName($dataField->name);
            $field->setValue($dataField->value);

            $item->addField($field);
        }

        foreach ($data->parts as $key => $dataPart) {

            $part = $this->_domainFactory->createEntity('App_Domain_ItemPart');
            $part->setOrderBy($key);

            $item->addPart($part);

            foreach ($dataPart->fields as $dataPartField) {

                $field = $this->_domainFactory->createEntity('App_Domain_ItemPartField');
                $field->setName($dataPartField->name);
                $field->setValue($dataPartField->value);

                $part->addField($field);
            }
        }

        if ($noteText) {

            $note = $this->_domainFactory->createEntity('App_Domain_ItemNote');
            $note->setText($noteText);
            $note->setUser($this->_user);
            $note->setCreatedTime(new DateTime());

            $item->addNote($note);
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
        $item = $this->_itemRepository->getById($id);

        if (null === $item) {
            throw new App_Service_Exception('Unable to find item');
        }

        if (null === $options && in_array($item->getStatus(), array(
            App_Domain_Service_VersionableInterface::STATUS_PUBLISHED,
            App_Domain_Service_VersionableInterface::STATUS_DRAFT,
        ))) {
            throw new App_Service_Exception('Item status requires publishing options');
        }

        $versionableItem = new App_Domain_Adapter_VersionableItem($item);
        $workflowableItem = new App_Domain_Adapter_WorkflowableItem($item);

        switch ($item->getStatus()) {

            case App_Domain_Service_VersionableInterface::STATUS_PUBLISHED:
            case App_Domain_Service_VersionableInterface::STATUS_DRAFT:

                $item->getRoute()->setSlug($options->name);
                $item->setColourScheme($options->colourScheme);
                $item->setLiveFrom($options->liveFrom);
                $item->setExpiresEnd($options->expiresEnd);
                $item->setPublishedTo($options->to);

                $teaserTemplate = $this->_itemTeaserTemplateRepository->getById($options->teaserTemplateId);
                $item->setTeaserTemplate($teaserTemplate);

                $template = $this->_itemTemplateRepository->getById($options->templateId);
                $item->setTemplate($template);

                if ($options->to === static::PUBLISH_TO_MENU) {
                    $this->_publishToMenu($item, $options);
                } else {
                    $this->_publishToStandalone($item);
                }

                $this->_applyBlockPublishingOptions($item, $options);

                if ($item->getStatus() === App_Domain_Service_VersionableInterface::STATUS_DRAFT) {

                    if ($item->getProvisionalLocation()) {

                        $this->_entityManager->delete($item->getProvisionalLocation());
                        $item->setProvisionalLocation(null);
                    }

                    $this->_versioningDomainService->publishDraft($versionableItem);
                    $this->_workflowDomainService->removeFromWorkflow($workflowableItem);
                }
                break;

            case App_Domain_Service_VersionableInterface::STATUS_REVISION:

                $this->_versioningDomainService->publishRevision($versionableItem);
                $this->_workflowDomainService->removeFromWorkflow($workflowableItem);
                break;

            case App_Domain_Service_VersionableInterface::STATUS_ROLLBACK:

                $this->_versioningDomainService->restoreRollback($versionableItem);
                break;

            case App_Domain_Service_VersionableInterface::STATUS_DELETED:

                $this->_versioningDomainService->restoreDeleted($versionableItem);
                break;

            default:
                // No default
        }

        $this->_entityManager->flush();

        // Clear caches
        $cache = $this->_cacheManager->getCache('long');
        $cache->remove(App_Service_Menu::MENU_CACHE_ID);
        $cache->remove(App_Plugin_Routes::ROUTES_CACHE_ID);
        $cache->remove(sprintf(static::CURRENT_PUBLISHING_OPTIONS_CACHE_ID, $id));
        $versionOf = $item->getVersionOf();
        if ($versionOf) {
            $cache->remove(sprintf(static::ITEM_CACHE_ID, $versionOf->getId()));
            $cache->remove(sprintf(static::ITEM_META_CACHE_ID, $versionOf->getId()));
        }
    }

    /**
     * @param App_Domain_Item $item
     * @param App_Service_Dto_PublishingOptions $options
     * @return App_Service_Item
     * @throws App_Service_Exception
     */
    protected function _publishToMenu(
        App_Domain_Item $item,
        App_Service_Dto_PublishingOptions $options
    )
    {
        if ($item->getStatus() === App_Domain_Service_VersionableInterface::STATUS_PUBLISHED) {

            $currentOptions = $this->getCurrentPublishingOptions($item->getId());

            if ($currentOptions->beneathMenuItemId == $options->beneathMenuItemId) {
                // Already in correct position of menu
                return $this;
            }

            foreach ($this->_getFlattenedMenu() as $flattenedMenuItem) {

                if ($this->_getContentByMenuItem($flattenedMenuItem['item']) !== $item) {
                    continue;
                }

                // Already in menu but moving...
                $menuItem = $flattenedMenuItem['item'];

                if (!$options->beneathMenuItemId) {

                    // Move to top level
                    $menuItem->setParentMenuItem(null);
                    return $this;
                }

                // Move beneath an existing item
                $beneathMenuItem = null;
                foreach ($this->_getFlattenedMenu() as $flattenedMenuItem) {

                    if ($flattenedMenuItem['item']->getId() == $options->beneathMenuItemId) {

                        $beneathMenuItem = $flattenedMenuItem['item'];
                        break;
                    }
                }

                if (null === $beneathMenuItem) {
                    throw new App_Service_Exception('Unable to find menu item to put beneath');
                }

                $beneathMenuItem->addItem($menuItem);

                return $this;
            }
        }

        if ($options->beneathMenuItemId) {

            // Publish beneath an existing item
            $beneathMenuItem = null;
            foreach ($this->_getFlattenedMenu() as $flattenedMenuItem) {

                $menuItem = $flattenedMenuItem['item'];

                if ($menuItem->getId() == $options->beneathMenuItemId) {

                    $beneathMenuItem = $menuItem;
                    break;
                }
            }

            if (!$beneathMenuItem) {
                throw new App_Service_Exception('Unable to find menu item to put beneath');
            }

            // Calculate order by
            $existingSubItems = $beneathMenuItem->getItems();

            if (count($existingSubItems)) {
                $orderBy = $existingSubItems->last()->getOrderBy() + 1;
            } else {
                $orderBy = 0;
            }

            $newMenuItem = $this->_domainFactory->createEntity('App_Domain_MenuItem');
            $newMenuItem->setOrderBy($orderBy);
            $newMenuItem->setRoute($item->getRoute());

            $beneathMenuItem->addItem($newMenuItem);

            return $this;
        }

        // Publish at top level
        $menu = $this->_menuRepository->getByName('main');

        // Calculate order by
        $existingTopLevel = $menu->getItems();
        if (count($existingTopLevel)) {
            $orderBy = $existingTopLevel->last()->getOrderBy() + 1;
        } else {
            $orderBy = 0;
        }

        $newMenuItem = $this->_domainFactory->createEntity('App_Domain_MenuItem');
        $newMenuItem->setOrderBy($orderBy);
        $newMenuItem->setRoute($item->getRoute());

        $menu->addItem($newMenuItem);

        return $this;
    }

    /**
     * @param App_Domain_Item $item
     * @return App_Service_Item
     * @throws App_Service_Exception
     */
    protected function _publishToStandalone(App_Domain_Item $item)
    {
        foreach ($this->_getFlattenedMenu() as $flattenedMenuItem) {

            $menuItem = $flattenedMenuItem['item'];

            if ($this->_getContentByMenuItem($menuItem) !== $item) {
                continue;
            }

            // Cant make an item standalone if it has child items
            if (count($menuItem->getItems())) {
                throw new App_Service_Exception('Item has child menu items');
            }

            if ($menuItem->getParentMenuItem()) {
                $menuItem->getParentMenuItem()->deleteItem($menuItem);
            } else {
                $menu = $this->_menuRepository->getByName('main');
                $menu->deleteItem($menuItem);
            }
        }

        return $this;
    }

    /**
     * @param App_Domain_Item $item
     * @param App_Service_Dto_PublishingOptions $options
     * @return App_Service_Item
     */
    protected function _applyBlockPublishingOptions(
        App_Domain_Item $item,
        App_Service_Dto_PublishingOptions $options
    )
    {
        // Remove all blocks
        $item->deleteAllFreeBlocks();
        $item->deleteAllBlockSequences();

        $template = $this->_itemTemplateRepository->getById($options->templateId);

        // Free blocks
        foreach ($options->freeBlocks as $freeBlock) {

            if (null === $freeBlock->id || null === $freeBlock->name) {
                continue;
            }

            $templateBlocks = $template->getBlocks()->filter(function($templateBlock) use ($freeBlock) {
                return $templateBlock->getName() === $freeBlock->name;
            });
            $templateBlock = $templateBlocks->first();

            $block = $this->_blockRepository->getById($freeBlock->id);

            if (null === $templateBlock || null === $block) {
                $this->_log->warn('Ignoring block');
                continue;
            }

            $itemFreeBlock = $this->_domainFactory->createEntity('App_Domain_ItemFreeBlock');
            $itemFreeBlock->setTemplateBlock($templateBlock);
            $itemFreeBlock->setBlock($block);

            $item->addFreeBlock($itemFreeBlock);
        }

        // Block sequences
        foreach ($options->blockSequences as $blockSequence) {

            if (!$blockSequence->blocks) {
                continue;
            }

            $templateBlocks = $template->getBlocks()->filter(function($templateBlock) use ($blockSequence) {
                return $templateBlock->getName() === $blockSequence->name;
            });
            $templateBlock = $templateBlocks->first();

            if (null === $templateBlock) {
                $this->_log->warn('Ignoring block sequence');
                continue;
            }

            $itemBlockSequence = $this->_domainFactory->createEntity('App_Domain_ItemBlockSequence');
            $itemBlockSequence->setTemplateBlock($templateBlock);

            // Blocks
            foreach ($blockSequence->blocks as $blockSequenceBlock) {

                $block = $this->_blockRepository->getById($blockSequenceBlock->id);

                if (null === $block) {
                    $this->_log->warn('Ignoring block sequence block');
                    continue;
                }

                $itemBlockSequenceBlock = $this->_domainFactory->createEntity('App_Domain_ItemBlockSequenceBlock');
                $itemBlockSequenceBlock->setOrderBy($blockSequenceBlock->orderBy);
                $itemBlockSequenceBlock->setBlock($block);

                $itemBlockSequence->addBlock($itemBlockSequenceBlock);
            }

            $item->addBlockSequence($itemBlockSequence);
        }

        return $this;
    }

    /**
     * @param int $id Published item's ID
     * @return void
     */
    public function delete($id)
    {
        $item = $this->_itemRepository->getById($id);

        if (null === $item) {
            throw new App_Service_Exception('Unable to find item');
        }

        if ($item->getStatus() !== App_Domain_Service_VersionableInterface::STATUS_PUBLISHED) {
            throw new App_Service_Exception('Item must be published');
        }

        // Check no child items
        foreach ($this->_getFlattenedMenu() as $flattenedMenuItem) {

            $menuItem = $flattenedMenuItem['item'];

            if ($this->_getContentByMenuItem($menuItem) !== $item) {
                continue;
            }

            if (count($menuItem->getItems())) {
                throw new App_Service_Exception('Item has child menu items');
            }
        }

        $item->setTemplate(null);
        $item->setTeaserTemplate(null);
        $item->setLiveFrom(null);
        $item->setExpiresEnd(null);
        $item->setPublishedTo(null);

        if ($item->getRoute()) {
            $this->_entityManager->delete($item->getRoute());
            $item->setRoute(null);
        }

        $versionableItem = new App_Domain_Adapter_VersionableItem($item);
        $this->_versioningDomainService->deletePublished($versionableItem);

        // Remove from menu
        foreach ($this->_getFlattenedMenu() as $flattenedMenuItem) {

            $menuItem = $flattenedMenuItem['item'];

            if ($this->_getContentByMenuItem($menuItem) !== $item) {
                continue;
            }

            if ($menuItem->getParentMenuItem()) {
                $menuItem->getParentMenuItem()->deleteItem($menuItem);
            } else {
                $menu = $this->_menuRepository->getByName('main');
                $menu->deleteItem($menuItem);
            }
        }

        $versionsOf = $this->_itemRepository->getAllVersionOf($item->getId());

        foreach ($versionsOf as $versionOf) {
            $this->_itemRepository->delete($versionOf);
        }

        $this->_entityManager->flush();

        // Clear cache
        $cache = $this->_cacheManager->getCache('long');
        $cache->remove(App_Service_Menu::MENU_CACHE_ID);
        $cache->remove(App_Plugin_Routes::ROUTES_CACHE_ID);
        $cache->remove(sprintf(static::CURRENT_PUBLISHING_OPTIONS_CACHE_ID, $id));
        $cache->remove(sprintf(static::ITEM_CACHE_ID, $id));
        $cache->remove(sprintf(static::ITEM_META_CACHE_ID, $id));
    }

    /**
     * @return array
     */
    protected function _getFlattenedMenu()
    {
        $flattened = array();

        $menu = $this->_menuRepository->getByName('main');
        $this->_flattenMenuRecursive($menu->getItems(), $flattened);

        return $flattened;
    }

    /**
     * @param type $items
     * @param type $flattened
     * @param type $level
     * @return void
     */
    protected function _flattenMenuRecursive(\Boxspaced\EntityManager\Collection\Collection $items, array &$flattened, $level = 1)
    {
        foreach ($items as $item) {

            $flattened[] = array(
                'item' => $item,
                'level' => $level,
            );

            if (count($item->getItems())) {
                $this->_flattenMenuRecursive($item->getItems(), $flattened, $level+1);
            }
        }
    }

}
