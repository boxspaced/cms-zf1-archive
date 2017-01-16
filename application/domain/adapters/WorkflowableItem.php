<?php

class App_Domain_Adapter_WorkflowableItem implements App_Domain_Service_WorkflowableInterface
{

    /**
     * @var App_Domain_Item
     */
    protected $_item;

    /**
     * @param App_Domain_Item $item
     */
    public function __construct(App_Domain_Item $item)
    {
        $this->_item = $item;
    }

    /**
     * @return string
     */
    public function getWorkflowStage()
    {
        return $this->_item->getWorkflowStage();
    }

    /**
     * @param string $stage
     * @return App_Domain_Service_WorkflowableInterface
     */
    public function setWorkflowStage($stage)
    {
        $this->_item->setWorkflowStage($stage);
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->_item->getStatus();
    }

}
