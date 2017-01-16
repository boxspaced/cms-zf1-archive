<?php

class App_Service_Dto_Item
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
    public $navText;

    /**
     *
     * @var string
     */
    public $title;

    /**
     *
     * @var string
     */
    public $metaKeywords;

    /**
     *
     * @var string
     */
    public $metaDescription;

    /**
     *
     * @var App_Service_Dto_ItemField[]
     */
    public $fields = array();

    /**
     *
     * @var App_Service_Dto_ItemPart[]
     */
    public $parts = array();

}
