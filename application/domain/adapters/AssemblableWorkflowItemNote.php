<?php

class App_Domain_Adapter_AssemblableWorkflowItemNote implements App_Service_Assembler_Workflow_AssemblableContentNoteInterface
{

    /**
     * @var App_Domain_ItemNote
     */
    protected $_itemNote;

    /**
     * @param App_Domain_ItemNote $itemNote
     */
    public function __construct(App_Domain_ItemNote $itemNote)
    {
        $this->_itemNote = $itemNote;
    }

    /**
     * @return App_Domain_User
     */
    public function getUser()
    {
        return $this->_itemNote->getUser();
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->_itemNote->getText();
    }

    /**
     * @return DateTime
     */
    public function getCreatedTime()
    {
        return $this->_itemNote->getCreatedTime();
    }

}
