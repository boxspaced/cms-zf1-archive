<?php

class App_Service_Dto_PublishingOptions
{

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $colourScheme;

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
    public $teaserTemplateId;

    /**
     *
     * @var int
     */
    public $templateId;

    /**
     *
     * @var string
     */
    public $to;

    /**
     *
     * @var int
     */
    public $beneathMenuItemId;

    /**
     *
     * @var App_Service_Dto_FreeBlock[]
     */
    public $freeBlocks = array();

    /**
     *
     * @var App_Service_Dto_BlockSequence[]
     */
    public $blockSequences = array();

}
