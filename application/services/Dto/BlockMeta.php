<?php

class App_Service_Dto_BlockMeta
{

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var int
     */
    public $typeId;

    /**
     *
     * @var string
     */
    public $typeName;

    /**
     *
     * @var string
     */
    public $typeIcon;

    /**
     *
     * @var int
     */
    public $authorId;

    /**
     *
     * @var App_Service_Dto_BlockNote[]
     */
    public $notes = array();

}
