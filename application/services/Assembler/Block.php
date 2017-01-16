<?php

class App_Service_Assembler_Block
{

    /**
     * @param App_Domain_Block $block
     * @return App_Service_Dto_Block
     */
    public function assembleBlockDto(App_Domain_Block $block)
    {
        $dto = new App_Service_Dto_Block();
        $dto->id = $block->getId();

        foreach ($block->getFields() as $field) {

            $dtoField = new App_Service_Dto_BlockField();
            $dtoField->name = $field->getName();
            $dtoField->value = $field->getValue();

            $dto->fields[] = $dtoField;
        }

        return $dto;
    }

    /**
     * @param App_Domain_Block $block
     * @return App_Service_Dto_BlockMeta
     */
    public function assembleBlockMetaDto(App_Domain_Block $block)
    {

        $dto = new App_Service_Dto_BlockMeta();

        if ($block->getVersionOf()) {
            $dto->name = $block->getVersionOf()->getName();
        } else {
            $dto->name = $block->getName();
        }

        $dto->authorId = $block->getAuthor()->getId();

        $type = $block->getType();

        $dto->typeId = (int) $type->getId();
        $dto->typeName = $type->getName();
        $dto->typeIcon = $type->getIcon();

        foreach ($block->getNotes() as $note) {

            $noteDto = new App_Service_Dto_BlockNote();
            $noteDto->username = $note->getUser()->getUsername();
            $noteDto->text = $note->getText();
            $noteDto->time = $note->getCreatedTime();

            $dto->notes[] = $noteDto;
        }

        return $dto;
    }

    /**
     * @param App_Domain_BlockType $blockType
     * @return App_Service_Dto_BlockType
     */
    public function assembleBlockTypeDto(App_Domain_BlockType $blockType)
    {
        $dto = new App_Service_Dto_BlockType();
        $dto->id = $blockType->getId();
        $dto->name = $blockType->getName();

        foreach ($blockType->getTemplates() as $template) {

            $templateDto = new App_Service_Dto_BlockTemplate();
            $templateDto->id = (int) $template->getId();
            $templateDto->name = $template->getName();
            $templateDto->viewScript = $template->getViewScript();
            $templateDto->description = $template->getDescription();

            $dto->templates[] = $templateDto;
        }

        return $dto;
    }

}
