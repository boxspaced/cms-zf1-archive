<?php

class IndexController extends Zend_Controller_Action
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
    public function indexAction()
    {
        $this->_forward(
            $this->config->settings->homeAction,
            $this->config->settings->homeController,
            $this->config->settings->homeModule,
            array(
                'id' => $this->config->settings->homeIdentifier,
            )
        );
    }

}
