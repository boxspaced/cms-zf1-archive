<?php

interface App_Domain_Service_WorkflowableInterface
{

    const WORKFLOW_STAGE_AUTHORING = 'AUTHORING';
    const WORKFLOW_STAGE_REJECTED = 'REJECTED';
    const WORKFLOW_STAGE_PUBLISHING = 'PUBLISHING';

    /**
     * @return string
     */
    public function getWorkflowStage();

    /**
     * @param string $stage
     * @return App_Domain_Service_WorkflowableInterface
     */
    public function setWorkflowStage($stage);

    /**
     * @return string
     */
    public function getStatus();

}
