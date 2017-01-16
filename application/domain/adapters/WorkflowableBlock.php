<?php

class App_Domain_Adapter_WorkflowableBlock implements App_Domain_Service_WorkflowableInterface
{

    /**
     * @var App_Domain_Block
     */
    protected $_block;

    /**
     * @param App_Domain_Block $item
     */
    public function __construct(App_Domain_Block $item)
    {
        $this->_block = $item;
    }

    /**
     * @return string
     */
    public function getWorkflowStage()
    {
        return $this->_block->getWorkflowStage();
    }

    /**
     * @param string $stage
     * @return App_Domain_Service_WorkflowableInterface
     */
    public function setWorkflowStage($stage)
    {
        $this->_block->setWorkflowStage($stage);
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->_block->getStatus();
    }

}
