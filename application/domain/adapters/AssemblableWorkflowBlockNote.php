<?php

class App_Domain_Adapter_AssemblableWorkflowBlockNote implements App_Service_Assembler_Workflow_AssemblableContentNoteInterface
{

    /**
     * @var App_Domain_BlockNote
     */
    protected $_blockNote;

    /**
     * @param App_Domain_BlockNote $blockNote
     */
    public function __construct(App_Domain_BlockNote $blockNote)
    {
        $this->_blockNote = $blockNote;
    }

    /**
     * @return App_Domain_User
     */
    public function getUser()
    {
        return $this->_blockNote->getUser();
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->_blockNote->getText();
    }

    /**
     * @return DateTime
     */
    public function getCreatedTime()
    {
        return $this->_blockNote->getCreatedTime();
    }

}
