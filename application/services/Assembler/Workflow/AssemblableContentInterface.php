<?php

interface App_Service_Assembler_Workflow_AssemblableContentInterface
{

    /**
     * @return string
     */
    public function getWorkflowStage();

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @return bool
     */
    public function isRoutable();

    /**
     * @return App_Service_Workflow_AssemblableContentInterface
     */
    public function getVersionOf();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return App_Domain_Route
     */
    public function getRoute();

    /**
     * @return int
     */
    public function getId();

    /**
     * @return App_Domain_User
     */
    public function getAuthor();

    /**
     * @return DateTime
     */
    public function getAuthoredTime();

    /**
     * @return App_Service_Workflow_AssemblableContentTypeInterface
     */
    public function getType();

    /**
     * @return App_Service_Workflow_AssemblableContentNoteInterface[]
     */
    public function getNotes();

}
