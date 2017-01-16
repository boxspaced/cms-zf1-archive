<?php

class App_Domain_Service_Versioning
{

    /**
     * @param App_Domain_Service_VersionableInterface $draft
     * @param App_Domain_User $user
     * @return void
     */
    public function createDraft(
        App_Domain_Service_VersionableInterface $draft,
        App_Domain_User $user
    )
    {
        $draft->setStatus($draft::STATUS_DRAFT);
        $draft->setAuthor($user);
        $draft->setAuthoredTime(new DateTime());
    }

    /**
     * @param App_Domain_Service_VersionableInterface $revision
     * @param App_Domain_Service_VersionableInterface $published
     * @param App_Domain_User $user
     * @return void
     */
    public function createRevision(
        App_Domain_Service_VersionableInterface $revision,
        App_Domain_Service_VersionableInterface $published,
        App_Domain_User $user
    )
    {
        $revision->setVersionOf($published);
        $revision->setStatus($revision::STATUS_REVISION);
        $revision->setAuthor($user);
        $revision->setAuthoredTime(new DateTime());
    }

    /**
     * @param App_Domain_Service_VersionableInterface $draft
     * @return void
     */
    public function publishDraft(App_Domain_Service_VersionableInterface $draft)
    {
        $draft->setStatus($draft::STATUS_PUBLISHED);
        $draft->setPublishedTime(new DateTime());
    }

    /**
     * @param App_Domain_Service_VersionableInterface $revision
     * @return void
     */
    public function publishRevision(App_Domain_Service_VersionableInterface $revision)
    {
        $published = $revision->getVersionOf();

        $publishedTransferValues = $published->getVersioningTransferValues();
        $revisionTransferValues = $revision->getVersioningTransferValues();

        $published->setVersioningTransferValues($revisionTransferValues);
        $published->setLastModifiedTime(new DateTime());

        $revision->setVersioningTransferValues($publishedTransferValues);
        $revision->setStatus($revision::STATUS_ROLLBACK);
        $revision->setAuthor(null);
        $revision->setAuthoredTime(null);
        $revision->setRollbackStopPoint(new DateTime());
    }

    /**
     * @param App_Domain_Service_VersionableInterface $published
     * @return void
     */
    public function deletePublished(App_Domain_Service_VersionableInterface $published)
    {
        $published->setStatus($published::STATUS_DELETED);
        $published->setLastModifiedTime(new DateTime());
        $published->setPublishedTime(null);
    }

    /**
     * @param App_Domain_Service_VersionableInterface $rollback
     * @return void
     */
    public function restoreRollback(App_Domain_Service_VersionableInterface $rollback)
    {

    }

    /**
     * @param App_Domain_Service_VersionableInterface $deleted
     * @return void
     */
    public function restoreDeleted(App_Domain_Service_VersionableInterface $deleted)
    {

    }

}
