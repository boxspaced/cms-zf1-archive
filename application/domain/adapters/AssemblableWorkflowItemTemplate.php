<?php

class App_Domain_Adapter_AssemblableWorkflowItemTemplate implements App_Service_Assembler_Workflow_AssemblableContentTemplateInterface
{

    /**
     * @var App_Domain_ItemTemplate
     */
    protected $_itemTemplate;

    /**
     * @param App_Domain_ItemTemplate $itemTemplate
     */
    public function __construct(App_Domain_ItemTemplate $itemTemplate)
    {
        $this->_itemTemplate = $itemTemplate;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->_itemTemplate->getId();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_itemTemplate->getName();
    }

}
