<?php

class App_Domain_Adapter_AssemblableWorkflowBlockTemplate implements App_Service_Assembler_Workflow_AssemblableContentTemplateInterface
{

    /**
     * @var App_Domain_BlockTemplate
     */
    protected $_blockTemplate;

    /**
     * @param App_Domain_BlockTemplate $blockTemplate
     */
    public function __construct(App_Domain_BlockTemplate $blockTemplate)
    {
        $this->_blockTemplate = $blockTemplate;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->_blockTemplate->getId();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_blockTemplate->getName();
    }

}
