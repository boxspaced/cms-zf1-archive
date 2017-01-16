<?php

class App_Plugin_Routes extends Zend_Controller_Plugin_Abstract
{

    const ROUTES_CACHE_ID = 'routes';

    /**
     * @todo Should be using ModuleService::getRoutes()
     * @param Zend_Controller_Request_Abstract $request
     * @return Zend_Controller_Router_Interface
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $front = Zend_Controller_Front::getInstance();
        $cacheManager = $front->getParam('bootstrap')->getPluginResource('cacheManager')->getCacheManager();

        $cache = $cacheManager->getCache('long');

        if (false !== $cache->test(static::ROUTES_CACHE_ID)) {
            return $this->_buildRouter($cache->load(static::ROUTES_CACHE_ID));
        }

        $routes = array();

        $container = $front->getParam('bootstrap')->getContainer();
        $modules = $container['ModuleRepository']->getAll();

        foreach ($modules as $module) {

            if (!$module->getEnabled()) {
                continue;
            }

            foreach ($module->getRoutes() as $route) {

                $slug = $route->getSlug();

                $defaults = array(
                    'module' => 'default',
                    'controller' => $module->getRouteController(),
                );

                if ($module->getRouteAction()) {
                    $defaults['action'] = $module->getRouteAction();
                    $defaults['id'] = $route->getIdentifier();
                } else {
                    $defaults['action'] = $route->getIdentifier();
                }

                $routes[] = array($slug, $defaults);
            }
        }

        $cache->save($routes);

        return $this->_buildRouter($routes);
    }

    /**
     * @param array $routes
     * @return Zend_Controller_Router_Interface
     */
    protected function _buildRouter(array $routes)
    {
        $router = Zend_Controller_Front::getInstance()->getRouter();

        foreach ($routes as $params) {

            $route = new Zend_Controller_Router_Route_Static($params[0], $params[1]);
            $router->addRoute($params[0], $route);

            if ('home' === $params[0]) {

                $route = new Zend_Controller_Router_Route_Static('/', $params[1]);
                $router->addRoute('/', $route);
            }
        }

        return $router;
    }

}
