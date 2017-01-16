<?php

class ErrorController extends Zend_Controller_Action
{

    /**
     * @var Zend_Config
     */
    public $config;

    /**
     * @return void
     */
    public function init()
    {
        $this->_helper->bootstrapResourceInjector();
    }

    /**
     * @return void
     */
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');

        if (!$errors || !$errors instanceof ArrayObject) {
            $this->view->message = 'You have reached the error page';
            return;
        }

        switch ($errors->type) {

            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:

                $this->getResponse()->setHttpResponseCode(404);
                $this->view->navText = 'Page Not Found';
                $this->view->message = 'Page Not Found';
                $this->view->code = 404;
                break;

            default:

                if ($log = $this->getLog()) {
                    $log->err($errors->exception);
                }

                $this->getResponse()->setHttpResponseCode(500);
                $this->view->navText = 'Application Error';
                $this->view->message = 'Application Error';
                $this->view->code = 500;
                break;
        }

        if (true === $this->getInvokeArg('displayExceptions')) {
            $this->view->exception = $errors->exception;
        }

        $navigation = $this->_helper->getHelper('Navigation')->createFrontendNavigation();
        $this->view->getHelper('navigation')->setContainer($navigation);

        $this->view->isStandalone = true;
        $this->view->request = $errors->request;
    }

    /**
     * @return Zend_Log|bool
     */
    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');

        if (!$bootstrap->hasResource('Log')) {
            return false;
        }

        return $bootstrap->getResource('Log');
    }

}
