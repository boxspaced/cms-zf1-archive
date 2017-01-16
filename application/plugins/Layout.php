<?php

class App_Plugin_Layout extends Zend_Controller_Plugin_Abstract
{

    /**
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        Zend_Layout::getMvcInstance()->setLayout($request->getModuleName());
    }

}
