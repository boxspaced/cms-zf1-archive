<?php

interface App_Service_Assembler_Workflow_AssemblableContentTypeInterface
{

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getIcon();

    /**
     * @return App_Service_Workflow_AssemblableContentTemplateInterface[]
     */
    public function getTemplates();

}
