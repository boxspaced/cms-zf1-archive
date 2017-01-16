<?php

class App_Domain_Adapter_VersionableBlock implements App_Domain_Service_VersionableInterface
{

    /**
     * @var App_Domain_Block
     */
    protected $_block;

    /**
     * @param App_Domain_Block $block
     */
    public function __construct(App_Domain_Block $block)
    {
        $this->_block = $block;
    }

    /**
     * @return App_Domain_Block
     */
    public function getAdaptee()
    {
        return $this->_block;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->_block->getStatus();
    }

    /**
     * @param string $status
     * @return App_Domain_Adapter_VersionableBlock
     */
    public function setStatus($status)
    {
        $this->_block->setStatus($status);
        return $this;
    }

    /**
     * @return App_Domain_Adapter_VersionableBlock
     */
    public function getVersionOf()
    {
        if (null === $this->_block->getVersionOf()) {
            return null;
        }

        return new static($this->_block->getVersionOf());
    }

    /**
     * @param App_Domain_Adapter_VersionableBlock $versionOf
     * @return App_Domain_Adapter_VersionableBlock
     */
    public function setVersionOf(App_Domain_Service_VersionableInterface $versionOf = null)
    {
        if ($versionOf instanceof $this) {
            $versionOf = $versionOf->getAdaptee();
        }

        $this->_block->setVersionOf($versionOf);
        return $this;
    }

    /**
     * @return array
     */
    public function getVersioningTransferValues()
    {
        return $this->_block->getVersioningTransferValues();
    }

    /**
     * @param array $values
     * @return App_Domain_Adapter_VersionableBlock
     */
    public function setVersioningTransferValues(array $values)
    {
        $this->_block->setVersioningTransferValues($values);
        return $this;
    }

    /**
     * @param App_Domain_User $author
     * @return App_Domain_Adapter_VersionableBlock
     */
    public function setAuthor(App_Domain_User $author = null)
    {
        $this->_block->setAuthor($author);
        return $this;
    }

    /**
     * @param DateTime $authoredTime
     * @return App_Domain_Adapter_VersionableBlock
     */
    public function setAuthoredTime(DateTime $authoredTime = null)
    {
        $this->_block->setAuthoredTime($authoredTime);
        return $this;
    }

    /**
     * @param DateTime $publishedTime
     * @return App_Domain_Adapter_VersionableBlock
     */
    public function setPublishedTime(DateTime $publishedTime = null)
    {
        $this->_block->setPublishedTime($publishedTime);
        return $this;
    }

    /**
     * @param DateTime $lastModifiedTime
     * @return App_Domain_Adapter_VersionableBlock
     */
    public function setLastModifiedTime(DateTime $lastModifiedTime = null)
    {
        $this->_block->setLastModifiedTime($lastModifiedTime);
        return $this;
    }

    /**
     * @param DateTime $rollbackStopPoint
     * @return App_Domain_Adapter_VersionableBlock
     */
    public function setRollbackStopPoint(DateTime $rollbackStopPoint = null)
    {
        $this->_block->setRollbackStopPoint($rollbackStopPoint);
        return $this;
    }

}
