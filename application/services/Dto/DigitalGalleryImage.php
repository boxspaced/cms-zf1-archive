<?php

class App_Service_Dto_DigitalGalleryImage
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
    public $keywords;

    /**
     *
     * @var string
     */
    public $title;

    /**
     *
     * @var string
     */
    public $description;

    /**
     *
     * @var string
     */
    public $imageNo;

    /**
     *
     * @var string
     */
    public $credit;

    /**
     *
     * @var string
     */
    public $copyright;

    /**
     *
     * @var float
     */
    public $price;

    /**
     *
     * @var string
     */
    public $imageName;

    /**
     *
     * @var App_Service_Dto_DigitalGalleryCategory[]
     */
    public $categories;

}
