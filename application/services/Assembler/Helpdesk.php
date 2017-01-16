<?php

class App_Service_Assembler_Helpdesk
{

    /**
     * @param App_Domain_HelpdeskTicket $ticket
     * @return App_Service_Dto_HelpdeskTicket
     */
    public function assembleTicketDto(App_Domain_HelpdeskTicket $ticket)
    {
        $dto = new App_Service_Dto_HelpdeskTicket();
        $dto->id = $ticket->getId();
        $dto->subject = $ticket->getSubject();
        $dto->issue = $ticket->getIssue();
        $dto->status = $ticket->getStatus();
        $dto->username = $ticket->getUser()->getUsername();
        $dto->createdAt = $ticket->getCreatedAt();

        foreach ($ticket->getComments() as $comment) {

            $dtoComment = new App_Service_Dto_HelpdeskTicketComment();
            $dtoComment->comment = $comment->getComment();
            $dtoComment->username = $comment->getUser()->getUsername();
            $dtoComment->createdAt = $comment->getCreatedAt();

            $dto->comments[] = $dtoComment;
        }

        foreach ($ticket->getAttachments() as $attachment) {

            $dtoAttachment = new App_Service_Dto_HelpdeskTicketAttachment();
            $dtoAttachment->fileName = $attachment->getFileName();
            $dtoAttachment->username = $attachment->getUser()->getUsername();
            $dtoAttachment->createdAt = $attachment->getCreatedAt();
            
            $dto->attachments[] = $dtoAttachment;
        }

        return $dto;
    }

}
