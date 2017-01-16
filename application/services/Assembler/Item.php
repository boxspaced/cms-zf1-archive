<?php

class App_Service_Assembler_Item
{

    /**
     * @param App_Domain_Item $item
     * @return App_Service_Dto_Item
     */
    public function assembleItemDto(App_Domain_Item $item)
    {
        $dto = new App_Service_Dto_Item();
        $dto->id = $item->getId();
        $dto->navText = $item->getNavText();
        $dto->title = $item->getTitle();
        $dto->metaKeywords = $item->getMetaKeywords();
        $dto->metaDescription = $item->getMetaDescription();

        foreach ($item->getFields() as $field) {

            $dtoField = new App_Service_Dto_ItemField();
            $dtoField->name = $field->getName();
            $dtoField->value = $field->getValue();

            $dto->fields[] = $dtoField;
        }

        foreach ($item->getParts() as $part) {

            $dtoPart = new App_Service_Dto_ItemPart();
            $dtoPart->orderBy = $part->getOrderBy();

            foreach ($part->getFields() as $field) {

                $dtoField = new App_Service_Dto_ItemField();
                $dtoField->name = $field->getName();
                $dtoField->value = $field->getValue();

                $dtoPart->fields[] = $dtoField;
            }

            $dto->parts[] = $dtoPart;
        }

        return $dto;
    }

    /**
     * @param App_Domain_Item $item
     * @return App_Service_Dto_ItemMeta
     */
    public function assembleItemMetaDto(App_Domain_Item $item)
    {

        $dto = new App_Service_Dto_ItemMeta();

        if ($item->getVersionOf()) {
            $dto->name = $item->getVersionOf()->getRoute()->getSlug();
        } else {
            $dto->name = $item->getRoute()->getSlug();
        }

        $type = $item->getType();

        $dto->typeId = (int) $type->getId();
        $dto->typeName = $type->getName();
        $dto->typeIcon = $type->getIcon();
        $dto->multipleParts = ($type->getMultipleParts() === 1);
        $dto->authorId = $item->getAuthor()->getId();

        foreach ($item->getNotes() as $note) {

            $noteDto = new App_Service_Dto_ItemNote();
            $noteDto->username = $note->getUser()->getUsername();
            $noteDto->text = $note->getText();
            $noteDto->time = $note->getCreatedTime();

            $dto->notes[] = $noteDto;
        }

        return $dto;
    }

    /**
     * @param App_Domain_ProvisionalLocation $provisionalLocation
     * @return App_Service_Dto_ProvisionalLocation
     */
    public function assembleProvisionalLocationDto(App_Domain_ProvisionalLocation $provisionalLocation)
    {
        $dto = new App_Service_Dto_ProvisionalLocation();
        $dto->to = $provisionalLocation->getTo();
        $dto->beneathMenuItemId = (int) $provisionalLocation->getBeneathMenuItemId();

        return $dto;
    }

    /**
     * @param App_Domain_ItemType $itemType
     * @return App_Service_Dto_ItemType
     */
    public function assembleItemTypeDto(App_Domain_ItemType $itemType)
    {
        $dto = new App_Service_Dto_ItemType();
        $dto->id = $itemType->getId();
        $dto->name = $itemType->getName();

        foreach ($itemType->getTemplates() as $template) {

            $templateDto = new App_Service_Dto_ItemTemplate();
            $templateDto->id = (int) $template->getId();
            $templateDto->name = $template->getName();
            $templateDto->viewScript = $template->getViewScript();
            $templateDto->description = $template->getDescription();

            foreach ($template->getBlocks() as $block) {

                $blockDto = new App_Service_Dto_ItemTemplateBlock();
                $blockDto->name = $block->getName();
                $blockDto->adminLabel = $block->getAdminLabel();
                $blockDto->sequence = (bool) $block->getSequence();

                $templateDto->blocks[] = $blockDto;
            }

            $dto->templates[] = $templateDto;
        }

        foreach ($itemType->getTeaserTemplates() as $teaserTemplate) {

            $templateDto = new App_Service_Dto_ItemTeaserTemplate();
            $templateDto->id = (int) $teaserTemplate->getId();
            $templateDto->name = $teaserTemplate->getName();
            $templateDto->viewScript = $teaserTemplate->getViewScript();
            $templateDto->description = $teaserTemplate->getDescription();
            
            $dto->teaserTemplates[] = $templateDto;
        }

        return $dto;
    }

}
