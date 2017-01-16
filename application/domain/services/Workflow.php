<?php

class App_Domain_Service_Workflow
{

    /**
     * @param App_Domain_Service_WorkflowableInterface $content
     * @return void
     */
    public function moveToAuthoring(App_Domain_Service_WorkflowableInterface $content)
    {
        $content->setWorkflowStage($content::WORKFLOW_STAGE_AUTHORING);
    }

    /**
     * @param App_Domain_Service_WorkflowableInterface $content
     * @return void
     */
    public function sendBackToAuthor(App_Domain_Service_WorkflowableInterface $content)
    {
        if (!in_array($content->getStatus(), array(
            App_Domain_Service_VersionableInterface::STATUS_DRAFT,
            App_Domain_Service_VersionableInterface::STATUS_REVISION,
        ))) {
            throw new App_Domain_Service_Exception('You can only move a draft or revision');
        }

        if ($content->getWorkflowStage() !== $content::WORKFLOW_STAGE_PUBLISHING) {
            throw new App_Domain_Service_Exception('Content not in publishing');
        }

        $content->setWorkflowStage($content::WORKFLOW_STAGE_REJECTED);
    }

    /**
     * @param App_Domain_Service_WorkflowableInterface $contentItem
     * @return void
     */
    public function moveToPublishing(App_Domain_Service_WorkflowableInterface $content)
    {
        if (!in_array($content->getStatus(), array(
            App_Domain_Service_VersionableInterface::STATUS_DRAFT,
            App_Domain_Service_VersionableInterface::STATUS_REVISION,
        ))) {
            throw new App_Domain_Service_Exception('You can only move a draft or revision');
        }

        if (!in_array($content->getWorkflowStage(), array(
            $content::WORKFLOW_STAGE_AUTHORING,
            $content::WORKFLOW_STAGE_REJECTED,
        ))) {
            throw new App_Domain_Service_Exception('Content not in authoring');
        }

        $content->setWorkflowStage($content::WORKFLOW_STAGE_PUBLISHING);
    }

    /**
     * @param App_Domain_Service_WorkflowableInterface $content
     * @return void
     */
    public function removeFromWorkflow(App_Domain_Service_WorkflowableInterface $content)
    {
        $content->setWorkflowStage(null);
    }

}
