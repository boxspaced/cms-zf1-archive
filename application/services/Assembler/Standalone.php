<?php

class App_Service_Assembler_Standalone
{

    /**
     * @param App_Service_Assembler_Standalone_AssemblableContentInterface $content
     * @return App_Service_Dto_StandaloneContent
     */
    public function assembleContentDto(App_Service_Assembler_Standalone_AssemblableContentInterface $content)
    {
        $dto = new App_Service_Dto_StandaloneContent();
        $dto->id = $content->getId();
        $dto->name = $content->getRoute()->getSlug();
        $dto->typeIcon = $content->getType()->getIcon();
        $dto->typeName = $content->getType()->getName();
        $dto->controllerName = $content->getRoute()->getModule()->getRouteController();
        $dto->liveFrom = $content->getLiveFrom();
        $dto->expiresEnd = $content->getExpiresEnd();
        $dto->navText = $content->getNavText();

        return $dto;
    }

}
