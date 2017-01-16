<?php

class App_Domain_Adapter_AssemblableWorkflowBlock implements App_Service_Assembler_Workflow_AssemblableContentInterface
{

    /**
     * @var App_Domain_Block
     */
    protected $_block;

    /**
     * @param App_Domain_Block $block
     */
    public function __construct(App_Domain_Block $block)
    {
        $this->_block = $block;
    }

    /**
     * @return string
     */
    public function getWorkflowStage()
    {
        return $this->_block->getWorkflowStage();
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->_block->getStatus();
    }

    /**
     * @return bool
     */
    public function isRoutable()
    {
        return false;
    }

    /**
     * @return App_Service_Workflow_AssemblableContentInterface
     */
    public function getVersionOf()
    {
        if (null === $this->_block->getVersionOf()) {
            return null;
        }

        return new static($this->_block->getVersionOf());
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_block->getName();
    }

    /**
     * @return App_Domain_Route
     */
    public function getRoute()
    {
        // No routes for blocks
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->_block->getId();
    }

    /**
     * @return App_Domain_User
     */
    public function getAuthor()
    {
        return $this->_block->getAuthor();
    }

    /**
     * @return DateTime
     */
    public function getAuthoredTime()
    {
        return $this->_block->getAuthoredTime();
    }

    /**
     * @return App_Service_Workflow_AssemblableContentTypeInterface
     */
    public function getType()
    {
        return new App_Domain_Adapter_AssemblableWorkflowBlockType($this->_block->getType());
    }

    /**
     * @return App_Service_Workflow_AssemblableContentNoteInterface[]
     */
    public function getNotes()
    {
        $notes = array();
        foreach ($this->_block->getNotes() as $note) {
            $notes[] = new App_Domain_Adapter_AssemblableWorkflowBlockNote($note);
        }
        return $notes;
    }

}
