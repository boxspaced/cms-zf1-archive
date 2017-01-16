<?php

class App_Service_Assembler_Workflow
{

    /**
     * @param App_Service_Assembler_Workflow_AssemblableContentInterface $content
     * @return App_Service_Dto_WorkflowContent
     */
    public function assembleContentDto(App_Service_Assembler_Workflow_AssemblableContentInterface $content)
    {
        $dto = new App_Service_Dto_WorkflowContent();

        if ($content->isRoutable()) {

            if ($content->getVersionOf()) {
                $dto->name = $content->getVersionOf()->getRoute()->getSlug();
            } else {
                $dto->name = $content->getRoute()->getSlug();
            }

        } else {
            
            if ($content->getVersionOf()) {
                $dto->name = $content->getVersionOf()->getName();
            } else {
                $dto->name = $content->getName();
            }
        }

        if ($content->isRoutable()) {

            if ($content->getVersionOf()) {
                $dto->controllerName = $content->getVersionOf()->getRoute()->getModule()->getRouteController();
                $dto->actionName = $content->getVersionOf()->getRoute()->getModule()->getRouteAction();
            } else {
                $dto->controllerName = $content->getRoute()->getModule()->getRouteController();
                $dto->actionName = $content->getRoute()->getModule()->getRouteAction();
            }

        } else {
            $dto->controllerName = 'block';
        }

        $dto->id = $content->getId();
        $dto->workflowStage = $content->getWorkflowStage();
        $dto->authoredTime = $content->getAuthoredTime();
        $dto->authorUsername = $content->getAuthor()->getUsername();

        $type = $content->getType();

        $dto->typeName = $type->getName();
        $dto->typeIcon = $type->getIcon();

        foreach ($type->getTemplates() as $template) {

            $templateDto = new App_Service_Dto_WorkflowContentTemplate();
            $templateDto->id = (int) $template->getId();
            $templateDto->name = $template->getName();

            $dto->availableTemplates[] = $templateDto;
        }

        if ($content->getStatus() === App_Domain_Service_VersionableInterface::STATUS_REVISION) {
            $dto->workflowStatus = App_Service_Workflow::WORKFLOW_STATUS_UPDATE;
        } else {
            $dto->workflowStatus = App_Service_Workflow::WORKFLOW_STATUS_NEW;
        }

        foreach ($content->getNotes() as $note) {

            $noteDto = new App_Service_Dto_WorkflowNote();
            $noteDto->username = $note->getUser()->getUsername();
            $noteDto->text = $note->getText();
            $noteDto->time = $note->getCreatedTime();

            $dto->notes[] = $noteDto;
        }

        return $dto;
    }

}
