<?php

class App_Service_Assembler_DigitalGallery
{

    /**
     * @param App_Domain_DigitalGalleryImage $image
     * @return App_Service_Dto_DigitalGalleryImage
     */
    public function assembleDigitalGalleryImageDto(App_Domain_DigitalGalleryImage $image)
    {
        $dto = new App_Service_Dto_DigitalGalleryImage();
        $dto->id = $image->getId();
        $dto->keywords = $image->getKeywords();
        $dto->title = $image->getTitle();
        $dto->description = $image->getDescription();
        $dto->imageNo = $image->getImageNo();
        $dto->credit = $image->getCredit();
        $dto->copyright = $image->getCopyright();
        $dto->price = $image->getPrice();
        $dto->imageName = $image->getImageName();

        foreach ($image->getCategories() as $category) {

            $categoryDto = new App_Service_Dto_DigitalGalleryCategory();
            $categoryDto->id = $category->getCategory()->getId();
            $categoryDto->type = $category->getCategory()->getType();
            $categoryDto->text = $category->getCategory()->getText();

            $dto->categories[] = $categoryDto;
        }

        return $dto;
    }

    /**
     * @param App_Domain_DigitalGalleryOrder $order
     * @return App_Service_Dto_DigitalGalleryOrder
     */
    public function assembleDigitalGalleryOrderDto(App_Domain_DigitalGalleryOrder $order)
    {
        $dto = new App_Service_Dto_DigitalGalleryOrder();
        $dto->id = $order->getId();
        $dto->name = $order->getName();
        $dto->dayPhone = $order->getDayPhone();
        $dto->email = $order->getEmail();
        $dto->message = $order->getMessage();
        $dto->createdTime = $order->getCreatedTime();
        $dto->code = $order->getCode();

        foreach ($order->getItems() as $item) {
            
            $image = $item->getImage();
            $dto->images[] = $this->assembleDigitalGalleryImageDto($image);
        }

        return $dto;
    }

}
