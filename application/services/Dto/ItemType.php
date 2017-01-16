<?php

class App_Service_Dto_ItemType
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
     * @var App_Service_Dto_ItemTemplate[]
     */
    public $templates = array();

    /**
     *
     * @var App_Service_Dto_ItemTeaserTemplate[]
     */
    public $teaserTemplates = array();

}
