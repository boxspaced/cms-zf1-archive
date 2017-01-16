<?php

class App_Service_Course
{

    const FILTERS_CACHE_ID = 'courseFilters';
    const COURSE_CACHE_ID = 'course%s';
    const COURSE_CACHE_TAG = 'course';

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
     * @var App_Domain_Repository_Course
     */
    protected $_courseRepository;

    /**
     * @var App_Domain_Repository_User
     */
    protected $_userRepository;

    /**
     * @var App_Service_Assembler_Course
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
     * @param App_Domain_Repository_Course $courseRepository
     * @param App_Domain_Repository_User $userRepository
     * @param App_Service_Assembler_Course $dtoAssembler
     * @param App_Domain_Factory $domainFactory
     */
    public function __construct(
        Zend_Cache_Manager $cacheManager,
        Zend_Log $log,
        Zend_Config $config,
        Zend_Db_Adapter_Abstract $adapter,
        Zend_Auth $auth,
        \Boxspaced\EntityManager\EntityManager $entityManager,
        App_Domain_Repository_Course $courseRepository,
        App_Domain_Repository_User $userRepository,
        App_Service_Assembler_Course $dtoAssembler,
        App_Domain_Factory $domainFactory
    )
    {
        $this->_cacheManager = $cacheManager;
        $this->_log = $log;
        $this->_config = $config;
        $this->_adapter = $adapter;
        $this->_auth = $auth;
        $this->_entityManager = $entityManager;
        $this->_courseRepository = $courseRepository;
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
        $path = $this->_config->settings->courseSearchIndexPath;

        if (!$path) {
            throw new App_Service_Exception('No path provided');
        }

        My_Util::deleteDir($path);

        $index = Zend_Search_Lucene::create($path);

        foreach ($this->_courseRepository->getAll() as $course) {

            $doc = new Zend_Search_Lucene_Document();
            $doc->addField(Zend_Search_Lucene_Field::UnIndexed('identifier', $course->getId(), 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::Text('title', $course->getTitle(), 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::Text('description', $course->getDescription(), 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::Keyword('code', $course->getCode(), 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::Keyword('day', $course->getDay(), 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::Keyword('time', $course->getDayTime() ?: 'All', 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::Keyword('category', $course->getCategory(), 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::Text('venue', $course->getVenue(), 'utf-8'));

            $index->addDocument($doc);
        }

        $index->commit();

        // Clear cache
        $cache = $this->_cacheManager->getCache('long');
        $cache->remove(static::FILTERS_CACHE_ID);
    }

    /**
     * @return App_Service_Dto_CourseFilterOptions
     */
    public function getFilterOptions()
    {
        $cache = $this->_cacheManager->getCache('long');

        if (false !== $cache->test(static::FILTERS_CACHE_ID)) {

            $cached = $cache->load(static::FILTERS_CACHE_ID);

            $categories = $cached[2];
            $venues = $cached[1];
            $times = $cached[0];

        } else {

            $categories = array();
            $times = array();
            $venues = array();

            foreach ($this->_courseRepository->getAll() as $course) {

                $categories[] = $course->getCategory();
                $times[] = $course->getDayTime();
                $venues[] = $course->getVenue();
            }

            $categories = array_unique($categories);
            $times = array_unique($times);
            $venues = array_unique($venues);

            natcasesort($categories);
            natcasesort($times);
            natcasesort($venues);

            $cache->save(array($times, $venues, $categories));
        }

        $days = array(
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
            'Sunday',
        );

        $filterOptions = new App_Service_Dto_CourseFilterOptions();

        // Categories
        foreach ($categories as $category) {

            if ($category) {

                $dto = new App_Service_Dto_FilterOption();
                $dto->value = $category;
                $dto->label = $category;

                $filterOptions->categories[] = $dto;
            }
        }

        // Days
        foreach ($days as $day) {

            if ($day) {

                $dto = new App_Service_Dto_FilterOption();
                $dto->value = $day;
                $dto->label = $day;

                $filterOptions->days[] = $dto;
            }
        }

        // Times
        foreach ($times as $time) {

            if ($time) {

                $dto = new App_Service_Dto_FilterOption();
                $dto->value = $time;
                $dto->label = $time;

                $filterOptions->times[] = $dto;
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

        return $filterOptions;
    }

    /**
     * @param int $id
     * @return App_Service_Dto_Course
     */
    public function getCourse($id)
    {
        $course = $this->_courseRepository->getById($id);

        if (null === $course) {
            throw new App_Service_Exception('Unable to find a course with given ID');
        }

        return $this->_dtoAssembler->assembleCourseDto($course);
    }

    /**
     * @param string $code
     * @return App_Service_Dto_Course
     */
    public function getCacheControlledCourseByCode($code)
    {
        $cache = $this->_cacheManager->getCache('long');

        if (false !== $cache->test(sprintf(static::COURSE_CACHE_ID, $code))) {
            return $cache->load(sprintf(static::COURSE_CACHE_ID, $code));
        }

        $dto = $this->getCourseByCode($code);
        $cache->save($dto, null, array(static::COURSE_CACHE_TAG));

        return $dto;
    }

    /**
     * @param string $code
     * @return App_Service_Dto_Course
     */
    public function getCourseByCode($code)
    {
        $course = $this->_courseRepository->getByCode($code);

        if (null === $course) {
            throw new App_Service_Exception('Unable to find a course with given code');
        }

        return $this->_dtoAssembler->assembleCourseDto($course);
    }

    /**
     * @param int $offset
     * @param int $showPerPage
     * @return App_Service_Dto_Course[]
     */
    public function getAllCourses($offset = null, $showPerPage = null)
    {
        $courses = array();

        foreach ($this->_courseRepository->getAll($offset, $showPerPage) as $course) {

            $courses[] = $this->_dtoAssembler->assembleCourseDto($course);
        }

        return $courses;
    }

    /**
     * @todo need to find a way of using SQL_CALC_FOUND_ROWS, in mappers and returned to repository
     * @return int
     */
    public function countAllCourses()
    {
        $select = $this->_adapter->select()
            ->from('course', 'COUNT(*)');

        $stmt = $select->query();

        return (int) $stmt->fetchColumn();
    }

    /**
     * @param App_Service_Dto_Course[] $courses
     * @return void
     */
    public function importCourses(array $courses)
    {
        foreach ($this->_courseRepository->getAll() as $course) {
            $this->_courseRepository->delete($course);
        }

        foreach ($courses as $course) {

            $this->_domainFactory->createEntity('App_Domain_Course')
                ->setCategory($course->category)
                ->setTitle($course->title)
                ->setCode($course->code)
                ->setDay($course->day)
                ->setStartDate($course->startDate)
                ->setTime($course->time)
                ->setNumWeeks($course->numWeeks)
                ->setHoursPerWeek($course->hoursPerWeek)
                ->setVenue($course->venue)
                ->setFee($course->fee)
                ->setConcession($course->concession)
                ->setDayTime($course->dayTime)
                ->setDescription($course->description);
        }

        $this->_entityManager->flush();

        // Clear cache
        $cache = $this->_cacheManager->getCache('long');
        $cache->clean(
            Zend_Cache::CLEANING_MODE_MATCHING_TAG,
            array(static::COURSE_CACHE_TAG)
        );
    }

}
