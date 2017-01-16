<?php

class App_Plugin_AccessControl extends Zend_Controller_Plugin_Abstract
{

    /**
     * @param Zend_Controller_Request_Abstract $request
     * @return bool
     */
    protected function _isValidController(Zend_Controller_Request_Abstract $request)
    {
        $dispatcher = Zend_Controller_Front::getInstance()->getDispatcher();
        return $dispatcher->isDispatchable($request);
    }

    /**
     * @param Zend_Controller_Request_Abstract $request
     * @return bool
     */
    protected function _isValidAction(Zend_Controller_Request_Abstract $request)
    {
        $dispatcher = Zend_Controller_Front::getInstance()->getDispatcher();

        $controllerClassName = $dispatcher->getControllerClass($request);
        // loadClass() will format for modules, load and return final name
        $finalControllerClassName = $dispatcher->loadClass($controllerClassName);
        $controllerClassMethods = get_class_methods($finalControllerClassName);

        return in_array($dispatcher->getActionMethod($request), $controllerClassMethods);
    }

    /**
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if (!$this->_isValidController($request) || !$this->_isValidAction($request)) {
            return;
        }

        $front = Zend_Controller_Front::getInstance();
        $bootstrap = $front->getParam('bootstrap');
        $container = $bootstrap->getContainer();

        $auth = $container['Auth'];
        $acl = $container['Acl'];

        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();

        $roles = array('guest');

        if ($auth->hasIdentity()) {

            $identity = $auth->getIdentity();

            foreach ($identity->roles as $role) {

                if ($acl->hasRole($role)) {
                    $roles[] = $role;
                }
            }
        }

        $resource = $module . ':' . $controller;

        if (!$acl->has($resource)) {
            $resource = null;
        }

        if (!$acl->isAllowedMultiRoles($roles, $resource, $action)) {

            if ($auth->hasIdentity()) {

                $request->setModuleName('default');
                $request->setControllerName('account');
                $request->setActionName('access-denied');

            } else {
                
                $request->setModuleName('default');
                $request->setControllerName('account');
                $request->setActionName('login');
            }
        }
    }

}
