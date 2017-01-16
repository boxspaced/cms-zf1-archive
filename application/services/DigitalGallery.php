<?php

class App_Service_DigitalGallery
{

    const FILTER_OPTIONS_ADMIN = 'admin';
    const FILTER_OPTIONS_FRONTEND = 'front';
    const TAG_CLOUD_CACHE_ID = 'digitalGalleryTagCloud';
    const FILTERS_CACHE_ID = '%sDigitalGalleryFilters';
    const FILTERS_CACHE_TAG = 'digitalGalleryFilter';
    const IMAGE_CACHE_ID = 'digitalGalleryImage%d';

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
     * @var App_Domain_user
     */
    protected $_user;

    /**
     * @var \Boxspaced\EntityManager\EntityManager
     */
    protected $_entityManager;

    /**
     * @var App_Domain_Repository_DigitalGalleryImage
     */
    protected $_digitalGalleryImageRepository;

    /**
     * @var App_Domain_Repository_DigitalGalleryCategory
     */
    protected $_digitalGalleryCategoryRepository;

    /**
     * @var App_Domain_Repository_DigitalGalleryOrder
     */
    protected $_digitalGalleryOrderRepository;

    /**
     * @var App_Domain_Repository_User
     */
    protected $_userRepository;

    /**
     * @var App_Service_Assembler_DigitalGallery
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
     * @param App_Domain_Repository_DigitalGalleryImage $digitalGalleryImageRepository
     * @param App_Domain_Repository_DigitalGalleryCategory $digitalGalleryCategoryRepository
     * @param App_Domain_Repository_DigitalGalleryOrder $digitalGalleryOrderRepository
     * @param App_Domain_Repository_User $userRepository
     * @param App_Service_Assembler_DigitalGallery $dtoAssembler
     * @param App_Domain_Factory $domainFactory
     */
    public function __construct(
        Zend_Cache_Manager $cacheManager,
        Zend_Log $log,
        Zend_Config $config,
        Zend_Db_Adapter_Abstract $adapter,
        Zend_Auth $auth,
        \Boxspaced\EntityManager\EntityManager $entityManager,
        App_Domain_Repository_DigitalGalleryImage $digitalGalleryImageRepository,
        App_Domain_Repository_DigitalGalleryCategory $digitalGalleryCategoryRepository,
        App_Domain_Repository_DigitalGalleryOrder $digitalGalleryOrderRepository,
        App_Domain_Repository_User $userRepository,
        App_Service_Assembler_DigitalGallery $dtoAssembler,
        App_Domain_Factory $domainFactory
    )
    {
        $this->_cacheManager = $cacheManager;
        $this->_log = $log;
        $this->_config = $config;
        $this->_adapter = $adapter;
        $this->_auth = $auth;
        $this->_entityManager = $entityManager;
        $this->_digitalGalleryImageRepository = $digitalGalleryImageRepository;
        $this->_digitalGalleryCategoryRepository = $digitalGalleryCategoryRepository;
        $this->_digitalGalleryOrderRepository = $digitalGalleryOrderRepository;
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
        $path = $this->_config->settings->digitalGallerySearchIndexPath;

        if (!$path) {
            throw new App_Service_Exception('No path provided');
        }

        My_Util::deleteDir($path);

        $index = Zend_Search_Lucene::create($path);

        foreach ($this->_digitalGalleryImageRepository->getAll() as $image) {

            $doc = new Zend_Search_Lucene_Document();
            $doc->addField(Zend_Search_Lucene_Field::UnIndexed('identifier', $image->getId(), 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::Text('keywords', $image->getKeywords(), 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::Text('title', $image->getTitle(), 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::Text('description', $image->getDescription(), 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::Text('imageNo', $image->getImageNo(), 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::UnIndexed('imageName', $image->getImageName(), 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::UnIndexed('copyright', $image->getCopyright(), 'utf-8'));

            $categories = array();
            $themes = array();
            $subjects = array();

            foreach ($image->getCategories() as $category) {

                $text = $category->getCategory()->getText();

                switch ($category->getCategory()->getType()) {

                    case 'category':
                        $categories[] = $text;
                        break;

                    case 'theme':
                        $themes[] = $text;
                        break;

                    case 'subject':
                        $subjects[] = $text;
                        break;

                    default:
                        // No default
                }
            }

            $doc->addField(Zend_Search_Lucene_Field::Text('categories', implode(' ', $categories), 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::Text('themes', implode(' ', $themes), 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::Text('subjects', implode(' ', $subjects), 'utf-8'));

            $index->addDocument($doc);
        }

        $index->commit();

        // Clear cache
        $cache = $this->_cacheManager->getCache('long');
        $cache->remove(static::TAG_CLOUD_CACHE_ID);
    }

    /**
     * @return App_Service_Dto_TagCloud
     */
    public function getTagCloud()
    {
        $cache = $this->_cacheManager->getCache('long');

        if (false !== $cache->test(static::TAG_CLOUD_CACHE_ID)) {

            $tagKeywords = $cache->load(static::TAG_CLOUD_CACHE_ID);

        } else {

            $tagKeywords = array();

            foreach ($this->_digitalGalleryImageRepository->getAll() as $image) {

                $keywords = explode(',', $image->getKeywords());

                foreach ($keywords as $keyword) {
                    $tagKeywords[] = trim($keyword);
                }
            }

            $tagKeywords = array_unique($tagKeywords);
            natcasesort($tagKeywords);

            $cache->save($tagKeywords);
        }

        $tagCloud = new App_Service_Dto_TagCloud();

        foreach ($tagKeywords as $keyword) {

            $keywordDto = new App_Service_Dto_TagCloudKeyword();
            $keywordDto->text = $keyword;
            $keywordDto->rank = rand(1, 10); // Just random for now

            $tagCloud->keywords[] = $keywordDto;
        }

        return $tagCloud;
    }

    /**
     * @param App_Service_Dto_DigitalGalleryOrder $data
     * @return int
     */
    public function createOrder(App_Service_Dto_DigitalGalleryOrder $data)
    {
        $order = $this->_domainFactory->createEntity('App_Domain_DigitalGalleryOrder');
        $order->setName($data->name);
        $order->setDayPhone($data->dayPhone);
        $order->setEmail($data->email);
        $order->setMessage($data->message);
        $order->setCreatedTime($data->createdTime);
        $order->setCode($data->code);

        foreach ($data->images as $imageDto) {

            $image = $this->_digitalGalleryImageRepository->getById($imageDto->id);

            $item = $this->_domainFactory->createEntity('App_Domain_DigitalGalleryOrderItem');
            $item->setImage($image);

            $order->addItem($item);
        }

        $this->_entityManager->flush();

        return (int) $order->getId();
    }

    /**
     * @param string $code
     * @return App_Service_Dto_DigitalGalleryOrder
     */
    public function getOrderByCode($code)
    {
        $order = $this->_digitalGalleryOrderRepository->getByCode($code);

        if (null === $order) {
            throw new App_Service_Exception('Unable to find an order with given code');
        }

        return $this->_dtoAssembler->assembleDigitalGalleryOrderDto($order);
    }

    /**
     * @param int $id
     * @param string $customerMessage
     * @return void
     */
    public function notifyOrderReceived($id, $customerMessage)
    {
        $order = $this->_digitalGalleryOrderRepository->getById($id);

        if (null === $order) {
            throw new App_Service_Exception('Unable to find an order with given ID');
        }

        $orderDetails = '';
        $orderDetails .= 'Name: ' . $order->getName() . PHP_EOL;
        $orderDetails .= 'Phone: ' . $order->getDayPhone() . PHP_EOL;
        $orderDetails .= 'Email: ' . $order->getEmail() . PHP_EOL;
        $orderDetails .= 'Date/time: ' . ($order->getCreatedTime() instanceof DateTime) ? $order->getCreatedTime()->format('Y-m-d H:i:s') : '' . PHP_EOL;
        $orderDetails .= PHP_EOL . 'Images:' . PHP_EOL;

        foreach ($order->getItems() as $orderItem) {
            $orderDetails .= '#' . $orderItem->getImage()->getImageNo() . ' - '
                           . $orderItem->getImage()->getPrice() . ' GBP' . PHP_EOL;
        }

        $secureHost = $this->_config->settings->secureHost;
        $orderDetails .= PHP_EOL . 'Download link: ' . $secureHost . '/digital-gallery/download/code/' . $order->getCode();

        try {

            $mail = new Zend_Mail();
            $mail->setFrom($order->getEmail(), $order->getName());
            $mail->addTo($this->_config->settings->digitalGalleryOrderRecipient);
            $mail->setSubject('Digital gallery order #' . $id);
            $mail->setBodyText($orderDetails . PHP_EOL . PHP_EOL . 'Customer message:' . PHP_EOL . $customerMessage);
            $mail->send();

        } catch (Exception $e) {
            $this->_log->err($e);
        }
    }

    /**
     * @param string $code
     * @return string Path to download (Zip file)
     */
    public function getDownloadByCode($code)
    {
        $order = $this->_digitalGalleryOrderRepository->getByCode($code);

        if (null === $order) {
            throw new App_Service_Exception('Unable to find an order with given code');
        }

        $privateDirectory = $this->_config->settings->digitalGalleryPrivateDirectory;
        $zipFile = '/tmp/' . $code . '.zip';

        if (!file_exists($zipFile)) {

            $zip = new ZipArchive();
            $zip->open($zipFile, $zip::CREATE);

            foreach ($order->getItems() as $orderItem) {

                $file = realpath($privateDirectory . DIRECTORY_SEPARATOR . $orderItem->getImage()->getImageName());
                $zip->addFile($file, basename($file));
            }

            $zip->close();
        }

        return $zipFile;
    }

    /**
     * @return string[]
     */
    public function getCategoryTypes()
    {
        return array(
            'category' => 'decade',
            'theme' => 'location',
            'subject' => 'subject',
        );
    }

    /**
     * @param App_Service_Dto_DigitalGalleryCategory $data
     * @return int
     */
    public function createCategory(App_Service_Dto_DigitalGalleryCategory $data)
    {
        $category = $this->_domainFactory->createEntity('App_Domain_DigitalGalleryCategory');
        $category->setType($data->type);
        $category->setText($data->text);

        $this->_entityManager->flush();

        // Clear cache
        $cache = $this->_cacheManager->getCache('long');
        $cache->clean(
            Zend_Cache::CLEANING_MODE_MATCHING_TAG,
            array(static::FILTERS_CACHE_TAG)
        );

        return (int) $category->getId();
    }

    /**
     * @return App_Service_Dto_DigitalGalleryCategory[]
     */
    public function getAllCategories($offset = null, $showPerPage = null)
    {
        $categories = array();

        foreach ($this->_digitalGalleryCategoryRepository->getAll($offset, $showPerPage) as $category) {

            $dto = new App_Service_Dto_DigitalGalleryCategory();
            $dto->id = $category->getId();
            $dto->type = $category->getType();
            $dto->text = $category->getText();

            $categories[] = $dto;
        }

        return $categories;
    }

    /**
     * @param int $id
     * @return App_Service_Dto_DigitalGalleryCategory
     */
    public function getCategory($id)
    {
        $category = $this->_digitalGalleryCategoryRepository->getById($id);

        if (null === $category) {
            throw new App_Service_Exception('Unable to find an category with given ID');
        }

        $dto = new App_Service_Dto_DigitalGalleryCategory();
        $dto->id = $category->getId();
        $dto->type = $category->getType();
        $dto->text = $category->getText();

        return $dto;
    }

    /**
     * @param int $id
     * @param App_Service_Dto_DigitalGalleryCategory $data
     * @return void
     */
    public function editCategory($id, App_Service_Dto_DigitalGalleryCategory $data)
    {
        $category = $this->_digitalGalleryCategoryRepository->getById($id);

        if (null === $category) {
            throw new App_Service_Exception('Unable to find category');
        }

        $category->setType($data->type);
        $category->setText($data->text);

        $this->_entityManager->flush();

        // Clear cache
        $cache = $this->_cacheManager->getCache('long');
        $cache->clean(
            Zend_Cache::CLEANING_MODE_MATCHING_TAG,
            array(static::FILTERS_CACHE_TAG)
        );
    }

    /**
     * @param int $id
     * @return void
     */
    public function deleteCategory($id)
    {
        $category = $this->_digitalGalleryCategoryRepository->getById($id);

        if (null === $category) {
            throw new App_Service_Exception('Unable to find category');
        }

        $this->_entityManager->delete($category);
        $this->_entityManager->flush();

        // Clear cache
        $cache = $this->_cacheManager->getCache('long');
        $cache->clean(
            Zend_Cache::CLEANING_MODE_MATCHING_TAG,
            array(static::FILTERS_CACHE_TAG)
        );
    }

    /**
     * @todo need to find a way of using SQL_CALC_FOUND_ROWS, in mappers and returned to repository
     * @return int
     */
    public function countAllCategories()
    {
        $select = $this->_adapter->select()
            ->from('digital_gallery_category', 'COUNT(*)');

        $stmt = $select->query();

        return (int) $stmt->fetchColumn();
    }

    /**
     * @return App_Service_Dto_DigitalGalleryFilterOptions
     */
    public function getAdminFilterOptions()
    {
        return $this->_createFilterOptions(static::FILTER_OPTIONS_ADMIN);
    }

    /**
     * @return App_Service_Dto_DigitalGalleryFilterOptions
     */
    public function getFilterOptions()
    {
        return $this->_createFilterOptions(static::FILTER_OPTIONS_FRONTEND);
    }

    /**
     * @param string $type
     * @return App_Service_Dto_DigitalGalleryFilterOptions
     */
    protected function _createFilterOptions($type = self::FILTER_OPTIONS_ADMIN)
    {
        $cache = $this->_cacheManager->getCache('long');

        if (false !== $cache->test(sprintf(static::FILTERS_CACHE_ID, $type))) {
            return $cache->load(sprintf(static::FILTERS_CACHE_ID, $type));
        }

        $categories = $this->_digitalGalleryCategoryRepository->getAll();

        $cats = $categories->filter(function($category) {
            return $category->getType() === 'category';
        });

        $themes = $categories->filter(function($category) {
            return $category->getType() === 'theme';
        });

        $subjects = $categories->filter(function($category) {
            return $category->getType() === 'subject';
        });

        $filterOptions = new App_Service_Dto_DigitalGalleryFilterOptions();

        // Categories
        foreach ($cats as $cat) {

            $dto = new App_Service_Dto_FilterOption();
            $dto->value = ($type === static::FILTER_OPTIONS_ADMIN) ? $cat->getId() : $cat->getText();
            $dto->label = $cat->getText();

            $filterOptions->categories[] = $dto;
        }

        usort($filterOptions->categories, function ($a, $b) {
            return strnatcmp($a->value, $b->value);
        });

        // Themes
        foreach ($themes as $theme) {

            $dto = new App_Service_Dto_FilterOption();
            $dto->value = ($type === static::FILTER_OPTIONS_ADMIN) ? $theme->getId() : $theme->getText();
            $dto->label = $theme->getText();

            $filterOptions->themes[] = $dto;
        }

        usort($filterOptions->themes, function ($a, $b) {
            return strnatcmp($a->value, $b->value);
        });

        // Subjects
        foreach ($subjects as $subject) {

            $dto = new App_Service_Dto_FilterOption();
            $dto->value = ($type === static::FILTER_OPTIONS_ADMIN) ? $subject->getId() : $subject->getText();
            $dto->label = $subject->getText();

            $filterOptions->subjects[] = $dto;
        }

        usort($filterOptions->subjects, function ($a, $b) {
            return strnatcmp($a->value, $b->value);
        });

        $cache->save($filterOptions, null, array(static::FILTERS_CACHE_TAG));

        return $filterOptions;
    }

    /**
     * @param string $imageName
     * @return void
     */
    public function processImage($imageName)
    {
        $hiResImage = implode(DIRECTORY_SEPARATOR, [
            $this->_config->settings->digitalGalleryPrivateDirectory,
            $imageName
        ]);

        if (!is_file($hiResImage)) {
            throw new App_Service_Exception(sprintf('Unable to read hi-res image: %s', $hiResImage));
        }

        $largeImagePath = $this->createLargeImage($hiResImage);
        $this->createThumbnail($largeImagePath);
    }

    /**
     * @param string $fromPath
     * @return string
     */
    protected function createLargeImage($fromPath)
    {
        $name = basename($fromPath);
        $image = imagecreatefromjpeg($fromPath); // @todo only jpegs

        if (false === $image) {
            throw new App_Service_Exception('Unable to open source image');
        }

        $publicDirectory = $this->_config->settings->digitalGalleryPublicDirectory;

        $width = imagesx($image);
        $height = imagesy($image);

        $newWidth = $this->_config->settings->digitalGalleryLargeImageWidth;
        $newHeight = floor($height * ($newWidth / $width));

        $tmpImage = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($tmpImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        imagedestroy($image);

        $path = $publicDirectory . DIRECTORY_SEPARATOR . $name;
        $imageResult = imagejpeg($tmpImage, $path);

        imagedestroy($tmpImage);

        if ($imageResult === false) {
            throw new App_Service_Exception('Unable to create large image');
        }

        return $path;
    }

    /**
     * @param string $fromPath
     * @return string
     */
    protected function createThumbnail($fromPath)
    {
        $name = basename($fromPath);
        $image = imagecreatefromjpeg($fromPath); // @todo only jpegs

        if (false === $image) {
            throw new App_Service_Exception('Unable to open source image');
        }

        $publicDirectory = $this->_config->settings->digitalGalleryPublicDirectory;

        $width = imagesx($image);
        $height = imagesy($image);

        $newWidth = $this->_config->settings->digitalGalleryThumbnailWidth;
        $newHeight = floor($height * ($newWidth / $width));

        $tmpImage = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($tmpImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        imagedestroy($image);

        $path = $publicDirectory . DIRECTORY_SEPARATOR . '_thumbs' . DIRECTORY_SEPARATOR . $name;
        $thumbResult = imagejpeg($tmpImage, $path);

        imagedestroy($tmpImage);

        if ($thumbResult === false) {
            throw new App_Service_Exception('Unable to create thumbnail');
        }

        return $path;
    }

    /**
     * @param string $imagePath
     */
    protected function watermarkImage($imagePath)
    {
        $image = imagecreatefromjpeg($imagePath); // @todo only jpegs

        if (false === $image) {
            throw new App_Service_Exception('Unable to open source image');
        }

        $width = imagesx($image);
        $height = imagesy($image);

        $watermark = imagecreatefrompng($this->_config->settings->digitalGalleryWatermark);

        $watermarkWidth = imagesx($watermark);
        $watermarkHeight = imagesy($watermark);

        $newWatermarkWidth = $width / 2;
        $newWatermarkHeight = floor($watermarkHeight * ($newWatermarkWidth / $watermarkWidth));

        $tmpWatermark = imagecreatetruecolor($newWatermarkWidth, $newWatermarkHeight);
        imagecopyresized($tmpWatermark, $watermark, 0, 0, 0, 0, $newWatermarkWidth, $newWatermarkHeight, $watermarkWidth, $watermarkHeight);

        imagedestroy($watermark);

        $mergeResult = imagecopymerge($image, $tmpWatermark, (($width / 2)-($newWatermarkWidth / 2)), (($height / 2)-($newWatermarkHeight / 2)), 0, 0, $newWatermarkWidth, $newWatermarkHeight, 50);

        if ($mergeResult === false) {
            throw new App_Service_Exception('Unable to watermark image');
        }

        imagedestroy($tmpWatermark);

        $imageResult = imagejpeg($image, $imagePath);

        imagedestroy($image);

        if ($imageResult === false) {
            throw new App_Service_Exception('Unable to create watermarked image');
        }

        return $imagePath;
    }

    /**
     * @param App_Service_Dto_DigitalGalleryImage $data
     * @return int
     */
    public function createImage(App_Service_Dto_DigitalGalleryImage $data)
    {
        $image = $this->_domainFactory->createEntity('App_Domain_DigitalGalleryImage');
        $image->setKeywords($data->keywords);
        $image->setTitle($data->title);
        $image->setDescription($data->description);
        $image->setImageNo($data->imageNo);
        $image->setCredit($data->credit);
        $image->setCopyright($data->copyright);
        $image->setPrice($data->price);
        $image->setImageName($data->imageName);

        foreach ($data->categories as $category) {

            $cat = $this->_digitalGalleryCategoryRepository->getById($category->id);

            if (!$cat) {
                // @todo log as warning
                continue;
            }

            $imageCategory = $this->_domainFactory->createEntity('App_Domain_DigitalGalleryImageCategory');
            $imageCategory->setCategory($cat);

            $image->addCategory($imageCategory);
        }

        $this->_entityManager->flush();

        return (int) $image->getId();
    }

    /**
     * @param int $id
     * @param App_Service_Dto_DigitalGalleryImage $data
     * @return void
     */
    public function editImage($id, App_Service_Dto_DigitalGalleryImage $data)
    {
        $image = $this->_digitalGalleryImageRepository->getById($id);

        if (null === $image) {
            throw new App_Service_Exception('Unable to find image');
        }

        $image->setTitle($data->title);
        $image->setKeywords($data->keywords);
        $image->setDescription($data->description);
        $image->setImageNo($data->imageNo);
        $image->setCredit($data->credit);
        $image->setCopyright($data->copyright);
        $image->setPrice($data->price);

        $image->deleteAllCategories();

        foreach ($data->categories as $category) {

            $cat = $this->_digitalGalleryCategoryRepository->getById($category->id);

            $imageCategory = $this->_domainFactory->createEntity('App_Domain_DigitalGalleryImageCategory');
            $imageCategory->setCategory($cat);

            $image->addCategory($imageCategory);
        }

        $this->_entityManager->flush();

        // Clear caches
        $cache = $this->_cacheManager->getCache('long');
        $cache->remove(sprintf(static::IMAGE_CACHE_ID, $id));
    }

    /**
     * @param int $id
     * @return void
     */
    public function deleteImage($id)
    {
        $image = $this->_digitalGalleryImageRepository->getById($id);

        if (null === $image) {
            throw new App_Service_Exception('Unable to find image');
        }

        // Delete from file system
        $privateDirectory = $this->_config->settings->digitalGalleryPrivateDirectory;
        $publicDirectory = $this->_config->settings->digitalGalleryPublicDirectory;

        if (
            unlink($privateDirectory . DIRECTORY_SEPARATOR . $image->getImageName())
            && unlink($publicDirectory . DIRECTORY_SEPARATOR . $image->getImageName())
            && unlink($publicDirectory . DIRECTORY_SEPARATOR . '_thumbs' . DIRECTORY_SEPARATOR . $image->getImageName())
        ) {

            // Delete from db
            $this->_entityManager->delete($image);
            $this->_entityManager->flush();

            // Clear caches
            $cache = $this->_cacheManager->getCache('long');
            $cache->remove(sprintf(static::IMAGE_CACHE_ID, $id));

        } else {
            throw new App_Service_Exception('Unable to remove images from file system');
        }
    }

    /**
     * @param int $id
     * @return App_Service_Dto_DigitalGalleryImage
     */
    public function getCacheControlledImage($id)
    {
        $cache = $this->_cacheManager->getCache('long');

        if (false !== $cache->test(sprintf(static::IMAGE_CACHE_ID, $id))) {
            return $cache->load(sprintf(static::IMAGE_CACHE_ID, $id));
        }

        $dto = $this->getImage($id);
        $cache->save($dto);

        return $dto;
    }

    /**
     * @param int $id
     * @return App_Service_Dto_DigitalGalleryImage
     */
    public function getImage($id)
    {
        $image = $this->_digitalGalleryImageRepository->getById($id);

        if (null === $image) {
            throw new App_Service_Exception('Unable to find an image with given ID');
        }

        return $this->_dtoAssembler->assembleDigitalGalleryImageDto($image);
    }

    /**
     * @param int $offset
     * @param int $showPerPage
     * @return App_Service_Dto_DigitalGalleryImage[]
     */
    public function getAllImages($offset = null, $showPerPage = null)
    {
        $images = array();

        foreach ($this->_digitalGalleryImageRepository->getAll($offset, $showPerPage) as $image) {
            $images[] = $this->_dtoAssembler->assembleDigitalGalleryImageDto($image);
        }

        return $images;
    }

    /**
     * @todo need to find a way of using SQL_CALC_FOUND_ROWS, in mappers and returned to repository
     * @return int
     */
    public function countAllImages()
    {
        $select = $this->_adapter->select()
            ->from('digital_gallery_image', 'COUNT(*)');

        $stmt = $select->query();

        return (int) $stmt->fetchColumn();
    }

}
