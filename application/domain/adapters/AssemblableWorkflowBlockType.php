<?php

class App_Domain_Adapter_AssemblableWorkflowBlockType implements App_Service_Assembler_Workflow_AssemblableContentTypeInterface
{

    /**
     * @var App_Domain_BlockType
     */
    protected $_blockType;

    /**
     * @param App_Domain_BlockType $blockType
     */
    public function __construct(App_Domain_BlockType $blockType)
    {
        $this->_blockType = $blockType;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_blockType->getName();
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->_blockType->getIcon();
    }

    /**
     * @return App_Service_Workflow_AssemblableContentTemplateInterface[]
     */
    public function getTemplates()
    {
        $templates = array();
        foreach ($this->_blockType->getTemplates() as $template) {
            $templates[] = new App_Domain_Adapter_AssemblableWorkflowBlockTemplate($template);
        }
        return $templates;
    }

}
