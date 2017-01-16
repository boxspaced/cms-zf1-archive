<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    /**
     * @return void
     */
    protected function _initCustomResourceLoader()
    {
        $resourceLoader = $this->getResourceLoader();
        $resourceLoader->addResourceTypes($this->getCustomResourceLoaderTypes());
    }

    /**
     * @return Zend_Controller_Front
     */
    protected function _initFront()
    {
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new App_Plugin_AccessControl());
        $front->registerPlugin(new App_Plugin_Routes());
        return $front;
    }

    /**
     * @return Zend_Config
     */
    protected function _initConfig()
    {
        $config = new Zend_Config($this->getOptions(), true);
        return $config;
    }

    /**
     * @return void
     */
    protected function _initHelpers()
    {
        Zend_Controller_Action_HelperBroker::addPath(
                APPLICATION_PATH . '/controllers/helpers', 'Controller_Helper');
    }

    /**
     * @return void
     */
    protected function _initContainer()
    {
        $container = new \Pimple\Container();

        foreach ($this->getContainer() as $key => $instance) {
            $container->$key = $instance;
        }

        $this->setContainer($container);
    }

    /**
     * @return void
     */
    protected function _initSearch()
    {
        Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding('utf-8');
        Zend_Search_Lucene_Analysis_Analyzer::setDefault(
            new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive()
        );
    }

    /**
     * @return void
     */
    protected function _initDependencyContainer()
    {
        $container = $this->getContainer();
        $bootstrap = $this;

        // Auth
        $container['Auth'] = function($container) {
            return Zend_Auth::getInstance();
        };
        $container['Acl'] = function($container) {
            return new App_Acl_Acl($container['Auth']);
        };

        // Entity manager
        $container['EntityManager'] = function($container) {
            return new \Boxspaced\EntityManager\EntityManager(require 'configs/entity-manager.php');
        };

        // Domain factory
        $container['DomainFactory'] = function($container) {
            return new App_Domain_Factory(
                $container['EntityManager']
            );
        };

        // Repositories
        $container['UserRepository'] = function($container) {
            return new App_Domain_Repository_User(
                $container['EntityManager']
            );
        };
        $container['ModuleRepository'] = function($container) {
            return new App_Domain_Repository_Module(
                $container['EntityManager']
            );
        };
        $container['MenuRepository'] = function($container) {
            return new App_Domain_Repository_Menu(
                $container['EntityManager']
            );
        };
        $container['BlockRepository'] = function($container) {
            return new App_Domain_Repository_Block(
                $container['EntityManager']
            );
        };
        $container['BlockTypeRepository'] = function($container) {
            return new App_Domain_Repository_BlockType(
                $container['EntityManager']
            );
        };
        $container['BlockTemplateRepository'] = function($container) {
            return new App_Domain_Repository_BlockTemplate(
                $container['EntityManager']
            );
        };
        $container['ItemRepository'] = function($container) {
            return new App_Domain_Repository_Item(
                $container['EntityManager']
            );
        };
        $container['ItemTypeRepository'] = function($container) {
            return new App_Domain_Repository_ItemType(
                $container['EntityManager']
            );
        };
        $container['ItemTeaserTemplateRepository'] = function($container) {
            return new App_Domain_Repository_ItemTeaserTemplate(
                $container['EntityManager']
            );
        };
        $container['ItemTemplateRepository'] = function($container) {
            return new App_Domain_Repository_ItemTemplate(
                $container['EntityManager']
            );
        };
        $container['CourseRepository'] = function($container) {
            return new App_Domain_Repository_Course(
                $container['EntityManager']
            );
        };
        $container['WhatsOnRepository'] = function($container) {
            return new App_Domain_Repository_WhatsOn(
                $container['EntityManager']
            );
        };
        $container['ModulePageRepository'] = function($container) {
            return new App_Domain_Repository_ModulePage(
                $container['EntityManager']
            );
        };
        $container['DigitalGalleryImageRepository'] = function($container) {
            return new App_Domain_Repository_DigitalGalleryImage(
                $container['EntityManager']
            );
        };
        $container['DigitalGalleryCategoryRepository'] = function($container) {
            return new App_Domain_Repository_DigitalGalleryCategory(
                $container['EntityManager']
            );
        };
        $container['DigitalGalleryOrderRepository'] = function($container) {
            return new App_Domain_Repository_DigitalGalleryOrder(
                $container['EntityManager']
            );
        };
        $container['HelpdeskTicketRepository'] = function($container) {
            return new App_Domain_Repository_HelpdeskTicket(
                $container['EntityManager']
            );
        };

        // Services
        $container['ItemService'] = function($container) use ($bootstrap) {
            return new App_Service_Item(
                $bootstrap->getPluginResource('cacheManager')->getCacheManager(),
                $bootstrap->getResource('log'),
                $bootstrap->getResource('config'),
                $bootstrap->getPluginResource('db')->getDbAdapter(),
                $container['Auth'],
                $container['EntityManager'],
                $container['ItemTypeRepository'],
                $container['ItemRepository'],
                $container['ItemTeaserTemplateRepository'],
                $container['ItemTemplateRepository'],
                $container['UserRepository'],
                $container['ModuleRepository'],
                $container['BlockRepository'],
                $container['MenuRepository'],
                $container['ItemServiceAssembler'],
                $container['VersioningDomainService'],
                $container['WorkflowDomainService'],
                $container['DomainFactory']
            );
        };
        $container['BlockService'] = function($container) use ($bootstrap) {
            return new App_Service_Block(
                $bootstrap->getPluginResource('cacheManager')->getCacheManager(),
                $bootstrap->getPluginResource('db')->getDbAdapter(),
                $container['Auth'],
                $container['EntityManager'],
                $container['BlockTypeRepository'],
                $container['BlockRepository'],
                $container['BlockTemplateRepository'],
                $container['UserRepository'],
                $container['BlockServiceAssembler'],
                $container['VersioningDomainService'],
                $container['WorkflowDomainService'],
                $container['DomainFactory']
            );
        };
        $container['MenuService'] = function($container) use ($bootstrap) {
            return new App_Service_Menu(
                $bootstrap->getPluginResource('cacheManager')->getCacheManager(),
                $bootstrap->getPluginResource('db')->getDbAdapter(),
                $container['Auth'],
                $container['EntityManager'],
                $container['UserRepository'],
                $container['MenuRepository']
            );
        };
        $container['WorkflowService'] = function($container) use ($bootstrap) {
            return new App_Service_Workflow(
                $bootstrap->getPluginResource('db')->getDbAdapter(),
                $container['Auth'],
                $container['EntityManager'],
                $container['UserRepository'],
                $container['WorkflowServiceAssembler'],
                $container['WorkflowDomainService'],
                $container['DomainFactory']
            );
        };
        $container['StandaloneService'] = function($container) use ($bootstrap) {
            return new App_Service_Standalone(
                $bootstrap->getPluginResource('db')->getDbAdapter(),
                $container['Auth'],
                $container['EntityManager'],
                $container['UserRepository'],
                $container['StandaloneServiceAssembler']
            );
        };
        $container['CourseService'] = function($container) use ($bootstrap) {
            return new App_Service_Course(
                $bootstrap->getPluginResource('cacheManager')->getCacheManager(),
                $bootstrap->getResource('log'),
                $bootstrap->getResource('config'),
                $bootstrap->getPluginResource('db')->getDbAdapter(),
                $container['Auth'],
                $container['EntityManager'],
                $container['CourseRepository'],
                $container['UserRepository'],
                $container['CourseServiceAssembler'],
                $container['DomainFactory']
            );
        };
        $container['WhatsOnService'] = function($container) use ($bootstrap) {
            return new App_Service_WhatsOn(
                $bootstrap->getPluginResource('cacheManager')->getCacheManager(),
                $bootstrap->getResource('log'),
                $bootstrap->getResource('config'),
                $bootstrap->getPluginResource('db')->getDbAdapter(),
                $container['Auth'],
                $container['EntityManager'],
                $container['WhatsOnRepository'],
                $container['UserRepository'],
                $container['WhatsOnServiceAssembler'],
                $container['DomainFactory']
            );
        };
        $container['ModulePageService'] = function($container) use ($bootstrap) {
            return new App_Service_ModulePage(
                $bootstrap->getPluginResource('cacheManager')->getCacheManager(),
                $bootstrap->getResource('log'),
                $container['Auth'],
                $container['EntityManager'],
                $container['UserRepository'],
                $container['ModulePageRepository'],
                $container['BlockRepository'],
                $container['DomainFactory']
            );
        };
        $container['DigitalGalleryService'] = function($container) use ($bootstrap) {
            return new App_Service_DigitalGallery(
                $bootstrap->getPluginResource('cacheManager')->getCacheManager(),
                $bootstrap->getResource('log'),
                $bootstrap->getResource('config'),
                $bootstrap->getPluginResource('db')->getDbAdapter(),
                $container['Auth'],
                $container['EntityManager'],
                $container['DigitalGalleryImageRepository'],
                $container['DigitalGalleryCategoryRepository'],
                $container['DigitalGalleryOrderRepository'],
                $container['UserRepository'],
                $container['DigitalGalleryServiceAssembler'],
                $container['DomainFactory']
            );
        };
        $container['HelpdeskService'] = function($container) use ($bootstrap) {
            return new App_Service_Helpdesk(
                $bootstrap->getResource('log'),
                $bootstrap->getResource('config'),
                $bootstrap->getPluginResource('db')->getDbAdapter(),
                $container['Auth'],
                $container['EntityManager'],
                $container['UserRepository'],
                $container['HelpdeskTicketRepository'],
                $container['HelpdeskServiceAssembler'],
                $container['DomainFactory']
            );
        };

        // Service DTO assemblers
        $container['ItemServiceAssembler'] = function($container) {
            return new App_Service_Assembler_Item();
        };
        $container['BlockServiceAssembler'] = function($container) {
            return new App_Service_Assembler_Block();
        };
        $container['WorkflowServiceAssembler'] = function($container) {
            return new App_Service_Assembler_Workflow();
        };
        $container['StandaloneServiceAssembler'] = function($container) {
            return new App_Service_Assembler_Standalone();
        };
        $container['CourseServiceAssembler'] = function($container) {
            return new App_Service_Assembler_Course();
        };
        $container['WhatsOnServiceAssembler'] = function($container) {
            return new App_Service_Assembler_WhatsOn();
        };
        $container['DigitalGalleryServiceAssembler'] = function($container) {
            return new App_Service_Assembler_DigitalGallery();
        };
        $container['HelpdeskServiceAssembler'] = function($container) {
            return new App_Service_Assembler_Helpdesk();
        };

        // Domain services
        $container['VersioningDomainService'] = function($container) {
            return new App_Domain_Service_Versioning();
        };
        $container['WorkflowDomainService'] = function($container) {
            return new App_Domain_Service_Workflow();
        };
    }

    /**
     * @return array
     */
    public function getCustomResourceLoaderTypes()
    {
        return array(
            'cli' => array(
                'namespace' => 'Cli',
                'path' => 'cli',
            ),
            'acls' => array(
                'namespace' => 'Acl',
                'path' => 'acls',
            ),
            'resources' => array(
                'namespace' => 'Resource',
                'path' => 'resources',
            ),
            'domain' => array(
                'namespace' => 'Domain',
                'path' => 'domain',
            ),
            'domain-repositories' => array(
                'namespace' => 'Domain_Repository',
                'path' => 'domain/repositories',
            ),
            'domain-services' => array(
                'namespace' => 'Domain_Service',
                'path' => 'domain/services',
            ),
            'domain-adapters' => array(
                'namespace' => 'Domain_Adapter',
                'path' => 'domain/adapters',
            ),
        );
    }

}
