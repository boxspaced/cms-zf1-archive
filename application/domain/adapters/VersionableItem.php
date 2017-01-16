<?php

class App_Domain_Adapter_VersionableItem implements App_Domain_Service_VersionableInterface
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
     * @return App_Domain_Item
     */
    public function getAdaptee()
    {
        return $this->_item;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->_item->getStatus();
    }

    /**
     * @param string $status
     * @return App_Domain_Adapter_VersionableItem
     */
    public function setStatus($status)
    {
        $this->_item->setStatus($status);
        return $this;
    }

    /**
     * @return App_Domain_Adapter_VersionableItem
     */
    public function getVersionOf()
    {
        if (null === $this->_item->getVersionOf()) {
            return null;
        }

        return new static($this->_item->getVersionOf());
    }

    /**
     * @param App_Domain_Adapter_VersionableItem $versionOf
     * @return App_Domain_Adapter_VersionableItem
     */
    public function setVersionOf(App_Domain_Service_VersionableInterface $versionOf = null)
    {
        if ($versionOf instanceof $this) {
            $versionOf = $versionOf->getAdaptee();
        }

        $this->_item->setVersionOf($versionOf);
        return $this;
    }

    /**
     * @return array
     */
    public function getVersioningTransferValues()
    {
        return $this->_item->getVersioningTransferValues();
    }

    /**
     * @param array $values
     * @return App_Domain_Adapter_VersionableItem
     */
    public function setVersioningTransferValues(array $values)
    {
        $this->_item->setVersioningTransferValues($values);
        return $this;
    }

    /**
     * @param App_Domain_User $author
     * @return App_Domain_Adapter_VersionableItem
     */
    public function setAuthor(App_Domain_User $author = null)
    {
        $this->_item->setAuthor($author);
        return $this;
    }

    /**
     * @param DateTime $authoredTime
     * @return App_Domain_Adapter_VersionableItem
     */
    public function setAuthoredTime(DateTime $authoredTime = null)
    {
        $this->_item->setAuthoredTime($authoredTime);
        return $this;
    }

    /**
     * @param DateTime $publishedTime
     * @return App_Domain_Adapter_VersionableItem
     */
    public function setPublishedTime(DateTime $publishedTime = null)
    {
        $this->_item->setPublishedTime($publishedTime);
        return $this;
    }

    /**
     * @param DateTime $lastModifiedTime
     * @return App_Domain_Adapter_VersionableItem
     */
    public function setLastModifiedTime(DateTime $lastModifiedTime = null)
    {
        $this->_item->setLastModifiedTime($lastModifiedTime);
        return $this;
    }

    /**
     * @param DateTime $rollbackStopPoint
     * @return App_Domain_Adapter_VersionableItem
     */
    public function setRollbackStopPoint(DateTime $rollbackStopPoint = null)
    {
        $this->_item->setRollbackStopPoint($rollbackStopPoint);
        return $this;
    }

}
