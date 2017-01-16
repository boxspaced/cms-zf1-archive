<?php

class App_Service_Dto_ItemTemplate
{

    /**
     *
     * @var int
     */
    public $id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $viewScript;

    /**
     *
     * @var string
     */
    public $description;

    /**
     *
     * @var bool
     */
    public $commentsAvailable;

    /**
     *
     * @var App_Service_Dto_ItemTemplateBlock[]
     */
    public $blocks = array();

}
