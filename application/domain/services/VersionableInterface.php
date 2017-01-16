<?php

interface App_Domain_Service_VersionableInterface
{

    const STATUS_DRAFT = 'DRAFT';
    const STATUS_PUBLISHED = 'PUBLISHED';
    const STATUS_REVISION = 'REVISION';
    const STATUS_ROLLBACK = 'ROLLBACK';
    const STATUS_DELETED = 'DELETED';

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param string $status
     * @return App_Domain_Service_VersionableInterface
     */
    public function setStatus($status);

    /**
     * @return App_Domain_Service_VersionableInterface
     */
    public function getVersionOf();

    /**
     * @param App_Domain_Service_VersionableInterface $versionOf
     * @return App_Domain_Service_VersionableInterface
     */
    public function setVersionOf(App_Domain_Service_VersionableInterface $versionOf = null);

    /**
     * @return array
     */
    public function getVersioningTransferValues();

    /**
     * @param array $values
     * @return App_Domain_Service_VersionableInterface
     */
    public function setVersioningTransferValues(array $values);

    /**
     * @param App_Domain_User $author
     * @return App_Domain_Service_VersionableInterface
     */
    public function setAuthor(App_Domain_User $author = null);

    /**
     * @param DateTime $authoredTime
     * @return App_Domain_Service_VersionableInterface
     */
    public function setAuthoredTime(DateTime $authoredTime = null);

    /**
     * @param DateTime $publishedTime
     * @return App_Domain_Service_VersionableInterface
     */
    public function setPublishedTime(DateTime $publishedTime = null);

    /**
     * @param DateTime $lastModifiedTime
     * @return App_Domain_Service_VersionableInterface
     */
    public function setLastModifiedTime(DateTime $lastModifiedTime = null);

    /**
     * @param DateTime $rollbackStopPoint
     * @return App_Domain_Service_VersionableInterface
     */
    public function setRollbackStopPoint(DateTime $rollbackStopPoint = null);

}
