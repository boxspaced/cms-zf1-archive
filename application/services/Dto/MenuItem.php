<?php

class App_Service_Dto_MenuItem
{

    /**
     *
     * @var bool
     */
    public $external;

    /**
     *
     * @var bool
     */
    public $module;

    /**
     *
     * @var string
     */
    public $navText;

    /**
     *
     * @var string
     */
    public $slug;

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
    public $controllerName;

    /**
     *
     * @var string
     */
    public $actionName;

    /**
     *
     * @var string
     */
    public $identifier;

    /**
     *
     * @var DateTime
     */
    public $liveFrom;

    /**
     *
     * @var DateTime
     */
    public $expiresEnd;

    /**
     *
     * @var int
     */
    public $menuItemId;

    /**
     *
     * @var int
     */
    public $numChildMenuItems;

    /**
     *
     * @var int
     */
    public $level;

    /**
     *
     * @var bool
     */
    public $first;

    /**
     *
     * @var bool
     */
    public $last;

    /**
     *
     * @var App_Service_Dto_MenuItem[]
     */
    public $items = array();

}
