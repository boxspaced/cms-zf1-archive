<?php

class App_Service_Assembler_WhatsOn
{

    /**
     * @param App_Domain_WhatsOn $whatsOn
     * @return App_Service_Dto_WhatsOn
     */
    public function assembleWhatsOnDto(App_Domain_WhatsOn $whatsOn)
    {
        $dto = new App_Service_Dto_WhatsOn();
        $dto->id = $whatsOn->getId();
        $dto->category = $whatsOn->getCategory();
        $dto->activity = $whatsOn->getActivity();
        $dto->dayTime = $whatsOn->getDayTime();
        $dto->venue = $whatsOn->getVenue();
        $dto->age = $whatsOn->getAge();
        $dto->description = $whatsOn->getDescription();
        $dto->specificDate = $whatsOn->getSpecificDate();

        return $dto;
    }

}
