<?php

class App_Service_Dto_AvailableLocationOption
{

    /**
     *
     * @var string
     */
    public $value;

    /**
     *
     * @var string
     */
    public $label;

    /**
     *
     * @var int
     */
    public $level;

    /**
     *
     * @var App_Service_Dto_AvailableLocationOption[]
     */
    public $subOptions = array();

}
