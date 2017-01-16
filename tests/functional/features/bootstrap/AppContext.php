<?php

use Behat\Behat\Context\BehatContext;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;

class AppContext extends BehatContext
{

    /**
     * @var Zend_Config
     */
    private $config;

    /**
     * @var Zend_Log
     */
    private $log;

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    private $db;

    /**
     * @var \Pimple\Container
     */
    private $container;

    /**
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        require 'bootstrap.php';
        $bootstrap = $application->getBootstrap();

        $this->setConfig($bootstrap->getResource('config'));
        $this->setLog($bootstrap->getResource('log'));
        $this->setDb($bootstrap->getPluginResource('db')->getDbAdapter());
        $this->setContainer($bootstrap->getContainer());
    }

    /**
     * @return Zend_Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return Zend_Log
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * @return Zend_Db_Adapter_Abstract
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * @return \Pimple\Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param Zend_Config $config
     * @return AppContext
     */
    public function setConfig(Zend_Config $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @param Zend_Log $log
     * @return AppContext
     */
    public function setLog(Zend_Log $log)
    {
        $this->log = $log;
        return $this;
    }

    /**
     * @param Zend_Db_Adapter_Abstract $db
     * @return AppContext
     */
    public function setDb(Zend_Db_Adapter_Abstract $db)
    {
        $this->db = $db;
        return $this;
    }

    /**
     * @param \Pimple\Container $container
     * @return AppContext
     */
    public function setContainer(\Pimple\Container $container)
    {
        $this->container = $container;
        return $this;
    }

    /**
     * @return MinkContext
     */
    public function getMinkContext()
    {
        return $this->getMainContext()->getMinkContext();
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        return $this->getMinkContext()->getSession();
    }

    /**
     * @Given /^a fresh install of the CMS$/
     */
    public function freshInstall()
    {
        $cwd = getcwd();

        chdir(sprintf('%s/../', APPLICATION_PATH));
        exec('sudo bin/phing clear');

        chdir($cwd);

        $this->seedBrowserWithRequest();
    }

    /**
     * Visit something so that browser has a request to detect current URL from.
     *
     * This stops error on first use of MinkContext::visitPage when there
     * hasn't been any pages yet.
     *
     * Don't want to call anything that creates 'routes' cache here as this
     * causes permission problems where content created through CLI e.g. from
     * within this context (using the services) does not remove the cache
     * because it was created by www-data user here.
     *
     * @return void
     */
    protected function seedBrowserWithRequest()
    {
        $this->getMinkContext()->visit('/favicon.ico');
    }

    /**
     * @todo use User service to create users
     *
     * @Given /^there are users:$/
     */
    public function createUsers(TableNode $table)
    {
        foreach ($table->getHash() as $row) {

            if ($row['username'] == 'admin') {
                continue;
            }

            $this->getDb()->insert('user', array(
                'username' => $row['username'],
                'email' => sprintf('%s@localhost', $row['username']),
                'password' => hash($this->getConfig()->settings->passwordHashingAlgorithm, 'password'),
            ));
            $userId = $this->getDb()->lastInsertId();

            $roles = explode(',', $row['roles']);

            foreach ($roles as $role) {

                $sql = "
                    SELECT id
                    FROM role
                    WHERE
                        name = :name
                ";
                $stmt = $this->getDb()->query($sql, array(
                    ':name' => $role,
                ));
                $roleId = $stmt->fetchColumn();

                if (!$roleId) {
                    throw new UnexpectedValueException("Unknown role: {$role}");
                }

                $this->getDb()->insert('user_role', array(
                    'user_id' => $userId,
                    'role_id' => $roleId,
                ));
            }
        }
    }

    /**
     * @todo use Course service to process CSV and avoid using browser
     *
     * @Given /^there is an existing "([^"]*)" import for (course|whats on) module$/
     */
    public function importModuleFile($file, $module)
    {
        $publicPath = sprintf('/assets/documents/%s', $file);

        $source = sprintf('%s/_files/%s', __DIR__, $file);
        $destination = $this->getConfig()->settings->courseUploadDirectory . $publicPath;
        copy($source, $destination);

        $this->getMainContext()->login('admin');
        $this->getMinkContext()->visitPage(sprintf('/%s/import', str_replace(' ', '-', $module)));
        $this->getMinkContext()->fillField('file', $publicPath);
        $this->getMinkContext()->pressButton('Import');
        $this->getMinkContext()->assertPageContainsText('Import successful');
        $this->getMainContext()->logout();
    }

    /**
     * @todo use DigitalGallery service to process CSV and Zip to avoid using browser
     *
     * @Given /^there is an existing "([^"]*)" and "([^"]*)" import for digital gallery module$/
     */
    public function importDigitalGalleryFiles($csv, $zip)
    {
        $publicCsvPath = sprintf('/assets/documents/%s', $csv);

        $source = sprintf('%s/_files/%s', __DIR__, $csv);
        $destination = $this->getConfig()->settings->digitalGalleryCsvUploadDirectory . $publicCsvPath;
        copy($source, $destination);

        // @todo check $zip exists

        $this->getMainContext()->login('admin');
        $this->getMinkContext()->visitPage('/digital-gallery/import');
        $this->getMinkContext()->attachFileToField('images', $zip);
        $this->getMinkContext()->fillField('csv', $publicCsvPath);
        $this->getMinkContext()->pressButton('Import');
        $this->getMinkContext()->assertPageContainsText('Import completed, number of images processed: 25');
        $this->getMainContext()->logout();

        $this->getMainContext()->reindex();
    }

    /**
     * @Given /^there are digital gallery filters:$/
     */
    public function createDigitalGalleryCategories(TableNode $table)
    {
        $service = $this->getService('DigitalGallery', 'admin');

        foreach ($table->getHash() as $row) {

            foreach (array(
                'id',
                'type',
                'text',
            ) as $column) {
                if (!isset($row[$column])) {
                    throw new InvalidArgumentException("Required column: {$column}");
                }
            }

            $type = array_search($row['type'], $service->getCategoryTypes());

            if ($type === false) {
                continue;
            }

            // Using db directly as can't set 'id' via service
            $this->getDb()->insert('digital_gallery_category', array(
                'id' => $row['id'],
                'type' => $type,
                'text' => $row['text'],
            ));
        }
    }

    /**
     * @Given /^there are digital gallery images:$/
     */
    public function createDigitalGalleryImages(TableNode $table)
    {
        $service = $this->getService('DigitalGallery', 'admin');

        foreach ($table->getHash() as $row) {

            $source = sprintf('%s/_files/%s', __DIR__, $row['image']);

            $newImageName = sprintf(
                '%s.%s',
                uniqid(),
                pathinfo($source)['extension']
            );
            $destination = $this->getConfig()->settings->digitalGalleryPrivateDirectory . DIRECTORY_SEPARATOR . $newImageName;

            copy($source, $destination);

            $service->processImage($newImageName);

            $image = new App_Service_Dto_DigitalGalleryImage();
            $image->title = $row['title'];
            $image->keywords = $row['keywords'];
            $image->description = $row['description'];
            $image->imageNo = $row['imageNo'];
            $image->copyright = $row['copyright'];
            $image->price = $row['price'];
            $image->imageName = $newImageName;

            $image->categories = array_merge(
                $this->createCategoryDtos('category', explode(',', $row['decades'])),
                $this->createCategoryDtos('theme', explode(',', $row['locations'])),
                $this->createCategoryDtos('subject', explode(',', $row['subjects']))
            );

            $service->createImage($image);
        }

        $this->getMainContext()->reindex();
    }

    /**
     * @param string $type
     * @param array $texts
     */
    protected function createCategoryDtos($type, array $texts)
    {
        $categories = $this->getService('DigitalGallery', 'admin')->getAllCategories();

        return array_filter($categories, function($category) use ($type, $texts) {
            return (
                $category->type == $type
                && in_array($category->text, $texts)
            );
        });
    }

    /**
     * Create items directly in the database via application service layer
     *
     * @Given /^there are items:$/
     */
    public function createItems(TableNode $table)
    {
        foreach ($table->getHash() as $row) {
            $this->createItem($row);
        }
    }

    /**
     * @param array $data
     * @return void
     * @throws InvalidArgumentException
     */
    protected function createItem($data)
    {
        foreach (array(
            'name',
            'type',
            'version',
            'stage',
        ) as $key) {
            if (!isset($data[$key])) {
                throw new InvalidArgumentException("Required data: {$key}");
            }
        }

        $type = $this->getItemTypeByName($data['type']);

        // New
        if ($data['version'] == 'new') {
            $this->createNewItem($data['name'], $type, $data, $data['stage']);
            return;
        }

        // Update
        if ($data['version'] == 'update') {

            $existing = $this->searchForPublishedItemByName($data['name']);
            $id = isset($existing->id) ? $existing->id : null;

            if (!$id) {
                // Create and publish a new item
                $id = $this->createNewItem($data['name'], $type, $data);
            }

            if ($data['stage'] == 'authoring' || $data['stage'] == 'publishing') {

                $id = $this->getService('Item', 'author')->createRevision($id);
                $item = $this->createItemDto($type->name, $data['name'], 2);
                $this->getService('Item', 'author')->edit($id, $item);

                if ($data['stage'] == 'publishing') {

                    $this->getService('Workflow', 'author')->moveToPublishing('Item', $id);
                }
            }
        }
    }

    /**
     * Create blocks directly in the database via application service layer
     *
     * @Given /^there are blocks:$/
     */
    public function createBlocks(TableNode $table)
    {
        foreach ($table->getHash() as $row) {
            $this->createBlock($row);
        }
    }

    /**
     * @param array $data
     * @return void
     * @throws InvalidArgumentException
     */
    protected function createBlock($data)
    {
        foreach (array(
            'name',
            'type',
            'version',
            'stage',
        ) as $key) {
            if (!isset($data[$key])) {
                throw new InvalidArgumentException("Required data: {$key}");
            }
        }

        $type = $this->getBlockTypeByName($data['type']);

        // New
        if ($data['version'] == 'new') {
            $this->createNewBlock($data['name'], $type, $data, $data['stage']);
            return;
        }

        // Update
        if ($data['version'] == 'update') {

            $existing = $this->searchForPublishedBlockByName($data['name']);
            $id = isset($existing->id) ? $existing->id : null;

            if (!$id) {
                // Create and publish a new block
                $id = $this->createNewBlock($data['name'], $type, $data);
            }

            if ($data['stage'] == 'authoring' || $data['stage'] == 'publishing') {

                $id = $this->getService('Block', 'author')->createRevision($id);
                $block = $this->createBlockDto($type->name, $data['name'], 2);
                $this->getService('Block', 'author')->edit($id, $block);

                if ($data['stage'] == 'publishing') {

                    $this->getService('Workflow', 'author')->moveToPublishing('Block', $id);
                }
            }
        }
    }

    /**
     * @Given /^the menu is empty$/
     */
    public function emptyMenu()
    {
        $this->getDb()->delete('menu_item');
    }

    /**
     * @param string $name
     * @return App_Service_Dto_ItemType
     * @throws Exception
     */
    protected function getItemTypeByName($name)
    {
        $types = $this->getService('Item', 'author')->getTypes();

        $found = null;

        foreach ($types as $type) {

            if ($type->name == $name) {
                $found = $type;
            }
        }

        if (!$found) {
            throw new UnexpectedValueException("Type not found: {$name}");
        }

        return $found;
    }

    /**
     * @param string $name
     * @return App_Service_Dto_BlockType
     * @throws Exception
     */
    protected function getBlockTypeByName($name)
    {
        $types = $this->getService('Block', 'author')->getTypes();

        $found = null;

        foreach ($types as $type) {

            if ($type->name == $name) {
                $found = $type;
            }
        }

        if (!$found) {
            throw new UnexpectedValueException("Type not found: {$name}");
        }

        return $found;
    }

    /**
     * @param string $name
     * @param App_Service_Dto_ItemType $type
     * @param array $data
     * @param string $stage
     * @return int
     */
    protected function createNewItem(
        $name,
        App_Service_Dto_ItemType $type,
        array $data,
        $stage = null
    )
    {
        $id = $this->getService('Item', 'author')->createDraft($name, $type->id);
        $item = $this->createItemDto($type->name, $name, 1);
        $this->getService('Item', 'author')->edit($id, $item);

        if ($stage == 'authoring') {
            // Keep in authoring workflow
            return $id;
        }

        if ($stage == 'publishing') {
            // Move to publishing workflow
            $this->getService('Workflow', 'author')->moveToPublishing('Item', $id);
            return $id;
        }

        // Publish
        $availableLocationOptions = $this->getService('Item', 'publisher')->getAvailableLocationOptions($id);
        $availableColourSchemeOptions = $this->getService('Item', 'publisher')->getAvailableColourSchemeOptions();

        $teaserTemplateId = 4;
        if (isset($data['teaserTemplate'])) {
            foreach ($type->teaserTemplates as $teaserTemplate) {
                if ($teaserTemplate->name == $data['teaserTemplate']) {
                    $teaserTemplateId = $teaserTemplate->id;
                }
            }
        }

        $templateId = 10;
        if (isset($data['template'])) {
            foreach ($type->templates as $template) {
                if ($template->name == $data['template']) {
                    $templateId = $template->id;
                }
            }
        }

        $colourScheme = 'dark-blue';
        if (isset($data['colourScheme'])) {
            foreach ($availableColourSchemeOptions as $option) {
                if ($option->label == $data['colourScheme']) {
                    $colourScheme = $option->value;
                }
            }
        }

        $publishTo = 'Standalone';
        if (isset($data['publishTo'])) {
            foreach ($availableLocationOptions->toOptions as $option) {
                if ($option->label == $data['publishTo']) {
                    $publishTo = $option->value;
                }
            }
        }

        $publishBeneathMenuItemId = 0; // Top level
        if (isset($data['publishBeneathMenuItem'])) {
            foreach ($availableLocationOptions->beneathMenuItemOptions as $option) {
                if ($option->label == $data['publishBeneathMenuItem']) {
                    $publishBeneathMenuItemId = $option->value;
                }
            }
        }

        $liveFrom = new DateTime(isset($data['liveFrom']) ? $data['liveFrom'] : '2000-01-01 00:00:00');
        $expiresEnd = new DateTime(isset($data['expiresEnd']) ? $data['expiresEnd'] : '2038-01-19 00:00:00');

        $publishingOptions = new App_Service_Dto_PublishingOptions();
        $publishingOptions->name = $name;
        $publishingOptions->colourScheme = $colourScheme;
        $publishingOptions->liveFrom = $liveFrom;
        $publishingOptions->expiresEnd = $expiresEnd;
        $publishingOptions->teaserTemplateId = $teaserTemplateId;
        $publishingOptions->templateId = $templateId;
        $publishingOptions->to = $publishTo;
        if ($publishTo == App_Service_Item::PUBLISH_TO_MENU) {
            $publishingOptions->beneathMenuItemId = $publishBeneathMenuItemId;
        }

        if (isset($data['mainImage'])) {

            $publishingOptions->freeBlocks[] = $this->createFreeBlock(
                $id,
                'mainImage',
                $data['mainImage']
            );
        }

        if (isset($data['lowerPromo'])) {

            $publishingOptions->freeBlocks[] = $this->createFreeBlock(
                $id,
                'lowerPromo',
                $data['lowerPromo']
            );
        }

        if (isset($data['leftColumn'])) {

            $publishingOptions->blockSequences[] = $this->createBlockSequence(
                $id,
                'leftColumn',
                explode(',', $data['leftColumn'])
            );
        }

        if (isset($data['rightColumn'])) {

            $publishingOptions->blockSequences[] = $this->createBlockSequence(
                $id,
                'rightColumn',
                explode(',', $data['rightColumn'])
            );
        }

        $this->getService('Item', 'publisher')->publish($id, $publishingOptions);

        return $id;
    }

    /**
     * @param int $contentId
     * @param string $freeBlockName
     * @param string $blockName
     * @return App_Service_Dto_FreeBlock
     */
    protected function createFreeBlock($contentId, $freeBlockName, $blockName)
    {
        $blockOption = $this->getAvailableBlockOption($contentId, 'html', $blockName);

        $freeBlock = new App_Service_Dto_FreeBlock();
        $freeBlock->name = $freeBlockName;
        $freeBlock->id = $blockOption->value;

        return $freeBlock;
    }

    /**
     * @param int $contentId
     * @param string $sequenceName
     * @param string[] $blockNames
     * @return App_Service_Dto_BlockSequence
     */
    protected function createBlockSequence($contentId, $sequenceName, array $blockNames)
    {
        $blockSequence = new App_Service_Dto_BlockSequence();
        $blockSequence->name = $sequenceName;

        foreach ($blockNames as $key => $blockName) {

            $blockOption = $this->getAvailableBlockOption($contentId, 'html', $blockName);

            $blockSequenceBlock = new App_Service_Dto_BlockSequenceBlock();
            $blockSequenceBlock->id = $blockOption->value;
            $blockSequenceBlock->orderBy = $key;

            $blockSequence->blocks[] = $blockSequenceBlock;
        }

        return $blockSequence;
    }

    /**
     * @param int $contentId
     * @param string $blockTypeName
     * @param string $blockName
     * @return App_Service_Dto_AvailableBlockOption
     */
    protected function getAvailableBlockOption($contentId, $blockTypeName, $blockName)
    {
        $availableBlockOptions = $this->getService('Item', 'publisher')->getAvailableBlockOptions($contentId);

        $typeOption = current(array_filter($availableBlockOptions, function($option) use ($blockTypeName) {
            return ($option->name === $blockTypeName);
        }));

        return current(array_filter($typeOption->blockOptions, function($option) use ($blockName) {
            return ($option->label === $blockName);
        }));
    }

    /**
     * @param string $name
     * @param App_Service_Dto_BlockType $type
     * @param array $data
     * @param string $stage
     * @return int
     */
    protected function createNewBlock(
        $name,
        App_Service_Dto_BlockType $type,
        array $data,
        $stage = null
    )
    {
        $id = $this->getService('Block', 'author')->createDraft($name, $type->id);
        $block = $this->createBlockDto($type->name, $name, 1);
        $this->getService('Block', 'author')->edit($id, $block);

        if ($stage == 'authoring') {
            // Keep in authoring workflow
            return $id;
        }

        if ($stage == 'publishing') {
            // Move to publishing workflow
            $this->getService('Workflow', 'author')->moveToPublishing('Block', $id);
            return $id;
        }

        // Publish
        $templateId = 2;
        if (isset($data['template'])) {
            foreach ($type->templates as $template) {
                if ($template->name == $data['template']) {
                    $templateId = $template->id;
                }
            }
        }

        $liveFrom = new DateTime(isset($data['liveFrom']) ? $data['liveFrom'] : '2000-01-01 00:00:00');
        $expiresEnd = new DateTime(isset($data['expiresEnd']) ? $data['expiresEnd'] : '2038-01-19 00:00:00');

        $publishingOptions = new App_Service_Dto_PublishingOptions();
        $publishingOptions->name = $name;
        $publishingOptions->liveFrom = $liveFrom;
        $publishingOptions->expiresEnd = $expiresEnd;
        $publishingOptions->templateId = $templateId;

        $this->getService('Block', 'publisher')->publish($id, $publishingOptions);

        return $id;
    }

    /**
     * @param string $type
     * @param string $name
     * @param int $version
     * @return App_Service_Dto_Item
     */
    protected function createItemDto($type, $name, $version)
    {
        switch ($type) {

            case 'article':

                $navText = sprintf('%s-v%d-navText', $name, $version);
                $title = sprintf('%s-v%d-title', $name, $version);
                $intro = sprintf('%s-v%d-intro', $name, $version);
                $body = sprintf('%s-v%d-body', $name, $version);

                $item = new App_Service_Dto_Item();
                $item->navText = $navText;
                $item->title = $title;

                $part = new App_Service_Dto_ItemPart();
                $part->orderBy = 0;

                $field = new App_Service_Dto_ItemField();
                $field->name = 'intro';
                $field->value = $intro;
                $part->fields[] = $field;

                $field = new App_Service_Dto_ItemField();
                $field->name = 'body';
                $field->value = $body;
                $part->fields[] = $field;

                $item->parts[] = $part;

                return $item;

            default:
                throw new InvalidArgumentException("Unknown type: {$type}");
        }
    }

    /**
     * @param string $type
     * @param string $name
     * @param int $version
     * @return App_Service_Dto_Block
     */
    protected function createBlockDto($type, $name, $version)
    {
        switch ($type) {

            case 'html':

                $html = sprintf('%s-v%d-html', $name, $version);

                $block = new App_Service_Dto_Block();

                $field = new App_Service_Dto_BlockField();
                $field->name = 'html';
                $field->value = $html;
                $block->fields[] = $field;

                return $block;

            default:
                throw new InvalidArgumentException("Unknown type: {$type}");
        }
    }

    /**
     * @param string $name
     * @return App_Service_Dto_Item
     */
    protected function searchForPublishedItemByName($name)
    {
        $module = $this->getContainer()['ModuleRepository']->getByName('item');

        $id = null;
        foreach ($module->getRoutes() as $route) {
            if ($route->getSlug() == $name) {
                $id = $route->getIdentifier();
            }
        }

        if (!$id) {
            return null;
        }

        try {
            $item = $this->getService('Item', 'author')->getItem($id);
            $this->getService('Item', 'author')->getCurrentPublishingOptions($id);
        } catch (Exception $e) {
            return null;
        }

        return $item;
    }

    /**
     * @todo use Block service rather than repository
     *
     * @param string $name
     * @return App_Service_Dto_Block
     */
    protected function searchForPublishedBlockByName($name)
    {
        $block = $this->getContainer()['BlockRepository']->getByName($name);

        if (!$block) {
            return null;
        }

        $id = $block->getId();

        try {
            $block = $this->getService('Block', 'author')->getBlock($id);
            $this->getService('Block', 'author')->getCurrentPublishingOptions($id);
        } catch (Exception $e) {
            return null;
        }

        return $block;
    }

    /**
     * @param string $name
     * @param string $username
     * @return mixed
     */
    protected function getService($name, $username)
    {
        $this->internalAuthentication($username);
        return $this->getContainer()[sprintf('%sService', $name)];
    }

    /**
     * @todo use Auth service
     *
     * @param string $username
     */
    protected function internalAuthentication($username)
    {
        $sql = '
            SELECT id
            FROM user
            WHERE
                username = :username
        ';

        $stmt = $this->getDb()->query($sql, array(
            ':username' => $username,
        ));

        $userId = $stmt->fetchColumn();

        if (!$userId) {
            throw new UnexpectedValueException("Internal authentication failed with username: {$username}");
        }

        $data = new stdClass();
        $data->id = $userId;
        $this->getContainer()['Auth']->getStorage()->write($data);
    }

}
