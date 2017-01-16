<?php

class App_Service_Dto_ItemMeta
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
     * @var bool
     */
    public $multipleParts;

    /**
     *
     * @var App_Service_Dto_ProvisionalLocation
     */
    public $provisionalLocation;

    /**
     *
     * @var int
     */
    public $authorId;

    /**
     *
     * @var App_Service_Dto_ItemNote[]
     */
    public $notes = array();

}
