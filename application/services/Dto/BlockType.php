<?php

class App_Service_Dto_BlockType
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
     * @var App_Service_Dto_BlockTemplate[]
     */
    public $templates = array();

}
