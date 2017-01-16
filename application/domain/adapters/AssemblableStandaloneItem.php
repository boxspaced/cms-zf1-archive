<?php

class App_Domain_Adapter_AssemblableStandaloneItem implements App_Service_Assembler_Standalone_AssemblableContentInterface
{

    /**
     * @var App_Domain_Item
     */
    protected $_item;

    /**
     * @param App_Domain_Item $item
     */
    public function __construct(App_Domain_Item $item)
    {
        $this->_item = $item;
    }

    /**
     * @return App_Domain_Route
     */
    public function getRoute()
    {
        return $this->_item->getRoute();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->_item->getId();
    }

    /**
     * @return App_Service_Standalone_AssemblableContentTypeInterface
     */
    public function getType()
    {
        return new App_Domain_Adapter_AssemblableStandaloneItemType($this->_item->getType());
    }

    /**
     * @return DateTime
     */
    public function getLiveFrom()
    {
        return $this->_item->getLiveFrom();
    }

    /**
     * @return DateTime
     */
    public function getExpiresEnd()
    {
        return $this->_item->getExpiresEnd();
    }

    /**
     * @return string
     */
    public function getNavText()
    {
        return $this->_item->getNavText();
    }

}
