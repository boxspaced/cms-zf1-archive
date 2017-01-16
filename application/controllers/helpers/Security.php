<?php

class Controller_Helper_Security extends Zend_Controller_Action_Helper_Abstract
{

    /**
     * @return void
     */
    public function enforceHttps()
    {
        if (APPLICATION_ENV !== 'development' && 'https' !== $this->getRequest()->getScheme()) {
            throw new Zend_Controller_Exception('Scheme must be https');
        }
    }

    /**
     * @return void
     */
    public function direct()
    {
        return $this->enforceHttps();
    }

}
