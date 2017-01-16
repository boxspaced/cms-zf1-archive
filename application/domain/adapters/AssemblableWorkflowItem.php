<?php

class App_Domain_Adapter_AssemblableWorkflowItem implements App_Service_Assembler_Workflow_AssemblableContentInterface
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
     * @return string
     */
    public function getStatus()
    {
        return $this->_item->getStatus();
    }

    /**
     * @return bool
     */
    public function isRoutable()
    {
        return true;
    }

    /**
     * @return App_Service_Workflow_AssemblableContentInterface
     */
    public function getVersionOf()
    {
        if (null === $this->_item->getVersionOf()) {
            return null;
        }

        return new static($this->_item->getVersionOf());
    }

    /**
     * @return string
     */
    public function getName()
    {
        return '';
    }

    /**
     * @return App_Domain_Route
     */
    public function getRoute()
    {
        return $this->_item->getRoute();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->_item->getId();
    }

    /**
     * @return App_Domain_User
     */
    public function getAuthor()
    {
        return $this->_item->getAuthor();
    }

    /**
     * @return DateTime
     */
    public function getAuthoredTime()
    {
        return $this->_item->getAuthoredTime();
    }

    /**
     * @return App_Service_Workflow_AssemblableContentTypeInterface
     */
    public function getType()
    {
        return new App_Domain_Adapter_AssemblableWorkflowItemType($this->_item->getType());
    }

    /**
     * @return App_Service_Workflow_AssemblableContentNoteInterface[]
     */
    public function getNotes()
    {
        $notes = array();
        foreach ($this->_item->getNotes() as $note) {
            $notes[] = new App_Domain_Adapter_AssemblableWorkflowItemNote($note);
        }
        return $notes;
    }

}
