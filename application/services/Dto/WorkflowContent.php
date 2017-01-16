<?php

class App_Service_Dto_WorkflowContent
{

    /**
     *
     * @var string
     */
    public $typeIcon;

    /**
     *
     * @var string
     */
    public $typeName;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var int
     */
    public $id;

    /**
     *
     * @var string
     */
    public $workflowStatus;

    /**
     *
     * @var string
     */
    public $workflowStage;

    /**
     *
     * @var DateTime
     */
    public $authoredTime;

    /**
     *
     * @var string
     */
    public $authorUsername;

    /**
     *
     * @var string
     */
    public $controllerName;

    /**
     *
     * @var string
     */
    public $actionName;

    /**
     *
     * @var App_Service_Dto_WorkflowContentTemplate[]
     */
    public $availableTemplates = array();

    /**
     *
     * @var App_Service_Dto_WorkflowNote[]
     */
    public $notes = array();

}
