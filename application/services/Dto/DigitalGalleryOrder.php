<?php

class App_Service_Dto_DigitalGalleryOrder
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
    public $dayPhone;

    /**
     *
     * @var string
     */
    public $email;

    /**
     *
     * @var string
     */
    public $message;

    /**
     *
     * @var DateTime
     */
    public $createdTime;

    /**
     *
     * @var string
     */
    public $code;

    /**
     *
     * @var App_Service_Dto_DigitalGalleryImage[]
     */
    public $images;

}
