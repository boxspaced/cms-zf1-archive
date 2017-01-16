<?php

interface App_Service_Assembler_Workflow_AssemblableContentNoteInterface
{

    /**
     * @return App_Domain_User
     */
    public function getUser();

    /**
     * @return string
     */
    public function getText();

    /**
     * @return DateTime
     */
    public function getCreatedTime();

}
