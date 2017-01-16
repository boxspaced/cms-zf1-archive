<?php

interface App_Service_Assembler_Standalone_AssemblableContentInterface
{

    /**
     * @return App_Domain_Route
     */
    public function getRoute();

    /**
     * @return int
     */
    public function getId();

    /**
     * @return App_Service_Standalone_AssemblableContentTypeInterface
     */
    public function getType();

    /**
     * @return DateTime
     */
    public function getLiveFrom();

    /**
     * @return DateTime
     */
    public function getExpiresEnd();

    /**
     * @return string
     */
    public function getNavText();

}
