<?php

class App_Service_WhatsOn
{

    const FILTERS_CACHE_ID = 'whatsOnFilters';
    const WHATS_ON_CACHE_ID = 'whatsOn%d';
    const WHATS_ON_CACHE_TAG = 'whatsOn';

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
     * @var App_Domain_Repository_WhatsOn
     */
    protected $_whatsOnRepository;

    /**
     * @var App_Domain_Repository_User
     */
    protected $_userRepository;

    /**
     * @var App_Service_Assembler_WhatsOn
     */
    protected $_dtoAssembler;

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
     * @param App_Domain_Repository_WhatsOn $whatsOnRepository
     * @param App_Domain_Repository_User $userRepository
     * @param App_Service_Assembler_WhatsOn $dtoAssembler
     * @param App_Domain_Factory $domainFactory
     */
    public function __construct(
        Zend_Cache_Manager $cacheManager,
        Zend_Log $log,
        Zend_Config $config,
        Zend_Db_Adapter_Abstract $adapter,
        Zend_Auth $auth,
        \Boxspaced\EntityManager\EntityManager $entityManager,
        App_Domain_Repository_WhatsOn $whatsOnRepository,
        App_Domain_Repository_User $userRepository,
        App_Service_Assembler_WhatsOn $dtoAssembler,
        App_Domain_Factory $domainFactory
    )
    {
        $this->_cacheManager = $cacheManager;
        $this->_log = $log;
        $this->_config = $config;
        $this->_adapter = $adapter;
        $this->_auth = $auth;
        $this->_entityManager = $entityManager;
        $this->_whatsOnRepository = $whatsOnRepository;
        $this->_userRepository = $userRepository;
        $this->_dtoAssembler = $dtoAssembler;
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
        $path = $this->_config->settings->whatsOnSearchIndexPath;

        if (!$path) {
            throw new App_Service_Exception('No path provided');
        }

        My_Util::deleteDir($path);

        $index = Zend_Search_Lucene::create($path);

        foreach ($this->_whatsOnRepository->getAll() as $whatsOn) {

            $doc = new Zend_Search_Lucene_Document();
            $doc->addField(Zend_Search_Lucene_Field::UnIndexed('identifier', $whatsOn->getId(), 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::Text('category', $whatsOn->getCategory(), 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::Text('activity', $whatsOn->getActivity(), 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::Text('dayTime', $whatsOn->getDayTime(), 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::Text('venue', $whatsOn->getVenue(), 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::Keyword('age', $whatsOn->getAge(), 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::Text('description', $whatsOn->getDescription(), 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::Text('specificDate', ($whatsOn->getSpecificDate() instanceof DateTime) ? $whatsOn->getSpecificDate()->format('Y-m-d') : '', 'utf-8'));

            $index->addDocument($doc);
        }

        $index->commit();

        // Clear cache
        $cache = $this->_cacheManager->getCache('long');
        $cache->remove(static::FILTERS_CACHE_ID);
    }

    /**
     * @return App_Service_Dto_WhatsOnFilterOptions
     */
    public function getFilterOptions()
    {
        $cache = $this->_cacheManager->getCache('long');

        if (false !== $cache->test(static::FILTERS_CACHE_ID)) {

            $cached = $cache->load(static::FILTERS_CACHE_ID);

            $categories = $cached[0];
            $dayTimes = $cached[1];
            $venues = $cached[2];
            $ages = $cached[3];

        } else {

            $categories = array();
            $dayTimes = array();
            $venues = array();
            $ages = array();

            foreach ($this->_whatsOnRepository->getAll() as $whatsOn) {

                $categories[] = $whatsOn->getCategory();
                $dayTimes[] = $whatsOn->getDayTime();
                $venues[] = $whatsOn->getVenue();
                $ages[] = $whatsOn->getAge();
            }

            $categories = array_unique($categories);
            $dayTimes = array_unique($dayTimes);
            $venues = array_unique($venues);
            $ages = array_unique($ages);

            natcasesort($categories);
            natcasesort($venues);
            natcasesort($ages);

            $cache->save(array($categories, $dayTimes, $venues, $ages));
        }

        $filterOptions = new App_Service_Dto_WhatsOnFilterOptions();

        // Categories
        foreach ($categories as $category) {

            if ($category) {

                $dto = new App_Service_Dto_FilterOption();
                $dto->value = $category;
                $dto->label = $category;

                $filterOptions->categories[] = $dto;
            }
        }

        // Day/times
        foreach ($dayTimes as $dayTime) {

            if ($dayTime) {

                $dto = new App_Service_Dto_FilterOption();
                $dto->value = $dayTime;
                $dto->label = $dayTime;

                $filterOptions->dayTimes[] = $dto;
            }
        }

        // Venues
        foreach ($venues as $venue) {

            if ($venue) {

                $dto = new App_Service_Dto_FilterOption();
                $dto->value = $venue;
                $dto->label = $venue;

                $filterOptions->venues[] = $dto;
            }
        }

        // Ages
        foreach ($ages as $age) {

            if ($age) {

                $dto = new App_Service_Dto_FilterOption();
                $dto->value = $age;
                $dto->label = $age;

                $filterOptions->ages[] = $dto;
            }
        }

        return $filterOptions;
    }

    /**
     * @param int $id
     * @return App_Service_Dto_WhatsOn
     */
    public function getCacheControlledWhatsOn($id)
    {
        $cache = $this->_cacheManager->getCache('long');

        if (false !== $cache->test(sprintf(static::WHATS_ON_CACHE_ID, $id))) {
            return $cache->load(sprintf(static::WHATS_ON_CACHE_ID, $id));
        }

        $dto = $this->getWhatsOn($id);
        $cache->save($dto, null, array(static::WHATS_ON_CACHE_TAG));

        return $dto;
    }

    /**
     * @param int $id
     * @return App_Service_Dto_WhatsOn
     */
    public function getWhatsOn($id)
    {
        $whatsOn = $this->_whatsOnRepository->getById($id);

        if (null === $whatsOn) {
            throw new App_Service_Exception('Unable to find a whats on with given ID');
        }

        return $this->_dtoAssembler->assembleWhatsOnDto($whatsOn);
    }

    /**
     * @param int $offset
     * @param int $showPerPage
     * @return App_Service_Dto_WhatsOn[]
     */
    public function getAllWhatsOns($offset = null, $showPerPage = null)
    {
        $whatsOns = array();

        foreach ($this->_whatsOnRepository->getAll($offset, $showPerPage) as $whatsOn) {
            $whatsOns[] = $this->_dtoAssembler->assembleWhatsOnDto($whatsOn);
        }

        return $whatsOns;
    }

    /**
     * @todo need to find a way of using SQL_CALC_FOUND_ROWS, in mappers and returned to repository
     * @return int
     */
    public function countAllWhatsOns()
    {
        $select = $this->_adapter->select()->from('whats_on', 'COUNT(*)');

        $stmt = $select->query();

        return (int) $stmt->fetchColumn();
    }

    /**
     * @param App_Service_Dto_WhatsOn[] $whatsOns
     * @return void
     */
    public function importWhatsOns(array $whatsOns)
    {
        foreach ($this->_whatsOnRepository->getAll() as $whatsOn) {
            $this->_whatsOnRepository->delete($whatsOn);
        }

        foreach ($whatsOns as $whatsOn) {

            $this->_domainFactory->createEntity('App_Domain_WhatsOn')
                ->setCategory($whatsOn->category)
                ->setActivity($whatsOn->activity)
                ->setDayTime($whatsOn->dayTime)
                ->setVenue($whatsOn->venue)
                ->setAge($whatsOn->age)
                ->setDescription($whatsOn->description)
                ->setSpecificDate($whatsOn->specificDate);
        }

        $this->_entityManager->flush();

        // Clear cache
        $cache = $this->_cacheManager->getCache('long');
        $cache->clean(
            Zend_Cache::CLEANING_MODE_MATCHING_TAG,
            array(static::WHATS_ON_CACHE_TAG)
        );
    }

}
