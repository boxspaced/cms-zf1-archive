<?php

class App_Domain_Adapter_AssemblableWorkflowItemType implements App_Service_Assembler_Workflow_AssemblableContentTypeInterface
{

    /**
     * @var App_Domain_ItemType
     */
    protected $_itemType;

    /**
     * @param App_Domain_ItemType $itemType
     */
    public function __construct(App_Domain_ItemType $itemType)
    {
        $this->_itemType = $itemType;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_itemType->getName();
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->_itemType->getIcon();
    }

    /**
     * @return App_Service_Workflow_AssemblableContentTemplateInterface[]
     */
    public function getTemplates()
    {
        $templates = array();
        foreach ($this->_itemType->getTemplates() as $template) {
            $templates[] = new App_Domain_Adapter_AssemblableWorkflowItemTemplate($template);
        }
        return $templates;
    }

}
