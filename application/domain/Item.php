<?php

class App_Domain_Item extends \Boxspaced\EntityManager\Entity\AbstractEntity
{

    /**
     * @var array
     */
    protected $_versioningTransferFields = array(
        'navText',
        'metaKeywords',
        'metaDescription',
        'title',
        'fields',
        'parts',
    );

    /**
     * @return int
     */
    public function getId()
    {
        return $this->get('id');
    }

    /**
     * @param int $id
     * @return App_Domain_Item
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return App_Domain_Item
     */
    public function getVersionOf()
    {
        return $this->get('versionOf');
    }

    /**
     * @param App_Domain_Item $versionOf
     * @return App_Domain_Item
     */
    public function setVersionOf(App_Domain_Item $versionOf)
    {
        $this->set('versionOf', $versionOf);
		return $this;
    }

    /**
     * @return App_Domain_ItemType
     */
    public function getType()
    {
        return $this->get('type');
    }

    /**
     * @param App_Domain_ItemType $type
     * @return App_Domain_Item
     */
    public function setType(App_Domain_ItemType $type)
    {
        $this->set('type', $type);
		return $this;
    }

    /**
     * @return App_Domain_Route
     */
    public function getRoute()
    {
        return $this->get('route');
    }

    /**
     * @param App_Domain_Route $route
     * @return App_Domain_Item
     */
    public function setRoute(App_Domain_Route $route = null)
    {
        $this->set('route', $route);
		return $this;
    }

    /**
     * @return App_Domain_ItemTemplate
     */
    public function getTemplate()
    {
        return $this->get('template');
    }

    /**
     * @param App_Domain_ItemTemplate $template
     * @return App_Domain_Item
     */
    public function setTemplate(App_Domain_ItemTemplate $template = null)
    {
        $this->set('template', $template);
		return $this;
    }

    /**
     * @return App_Domain_ItemTeaserTemplate
     */
    public function getTeaserTemplate()
    {
        return $this->get('teaserTemplate');
    }

    /**
     * @param App_Domain_ItemTeaserTemplate $teaserTemplate
     * @return App_Domain_Item
     */
    public function setTeaserTemplate(App_Domain_ItemTeaserTemplate $teaserTemplate = null)
    {
        $this->set('teaserTemplate', $teaserTemplate);
		return $this;
    }

    /**
     * @return string
     */
    public function getColourScheme()
    {
        return $this->get('colourScheme');
    }

    /**
     * @param string $colourScheme
     * @return App_Domain_Item
     */
    public function setColourScheme($colourScheme)
    {
        $this->set('colourScheme', $colourScheme);
		return $this;
    }

    /**
     * @return string
     */
    public function getNavText()
    {
        return $this->get('navText');
    }

    /**
     * @param string $navText
     * @return App_Domain_Item
     */
    public function setNavText($navText)
    {
        $this->set('navText', $navText);
		return $this;
    }

    /**
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->get('metaKeywords');
    }

    /**
     * @param string $metaKeywords
     * @return App_Domain_Item
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->set('metaKeywords', $metaKeywords);
		return $this;
    }

    /**
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->get('metaDescription');
    }

    /**
     * @param string $metaDescription
     * @return App_Domain_Item
     */
    public function setMetaDescription($metaDescription)
    {
        $this->set('metaDescription', $metaDescription);
		return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->get('title');
    }

    /**
     * @param string $title
     * @return App_Domain_Item
     */
    public function setTitle($title)
    {
        $this->set('title', $title);
		return $this;
    }

    /**
     * @return App_Domain_ProvisionalLocation
     */
    public function getProvisionalLocation()
    {
        return $this->get('provisionalLocation');
    }

    /**
     * @param App_Domain_ProvisionalLocation $provisionalLocation
     * @return App_Domain_Item
     */
    public function setProvisionalLocation(App_Domain_ProvisionalLocation $provisionalLocation = null)
    {
        $this->set('provisionalLocation', $provisionalLocation);
		return $this;
    }

    /**
     * @return string
     */
    public function getPublishedTo()
    {
        return $this->get('publishedTo');
    }

    /**
     * @param string $publishedTo
     * @return App_Domain_Item
     */
    public function setPublishedTo($publishedTo)
    {
        $this->set('publishedTo', $publishedTo);
		return $this;
    }

    /**
     * @return DateTime
     */
    public function getLiveFrom()
    {
        return $this->get('liveFrom');
    }

    /**
     * @param DateTime $liveFrom
     * @return App_Domain_Item
     */
    public function setLiveFrom(DateTime $liveFrom = null)
    {
        $this->set('liveFrom', $liveFrom);
		return $this;
    }

    /**
     * @return DateTime
     */
    public function getExpiresEnd()
    {
        return $this->get('expiresEnd');
    }

    /**
     * @param DateTime $expiresEnd
     * @return App_Domain_Item
     */
    public function setExpiresEnd(DateTime $expiresEnd = null)
    {
        $this->set('expiresEnd', $expiresEnd);
		return $this;
    }

    /**
     * @return string
     */
    public function getWorkflowStage()
    {
        return $this->get('workflowStage');
    }

    /**
     * @param string $workflowStage
     * @return App_Domain_Item
     */
    public function setWorkflowStage($workflowStage)
    {
        $this->set('workflowStage', $workflowStage);
		return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->get('status');
    }

    /**
     * @param string $status
     * @return App_Domain_Item
     */
    public function setStatus($status)
    {
        $this->set('status', $status);
		return $this;
    }

    /**
     * @return App_Domain_User
     */
    public function getAuthor()
    {
        return $this->get('author');
    }

    /**
     * @param App_Domain_User $author
     * @return App_Domain_Item
     */
    public function setAuthor(App_Domain_User $author = null)
    {
        $this->set('author', $author);
		return $this;
    }

    /**
     * @return DateTime
     */
    public function getAuthoredTime()
    {
        return $this->get('authoredTime');
    }

    /**
     * @param DateTime $authoredTime
     * @return App_Domain_Item
     */
    public function setAuthoredTime(DateTime $authoredTime = null)
    {
        $this->set('authoredTime', $authoredTime);
		return $this;
    }

    /**
     * @return DateTime
     */
    public function getLastModifiedTime()
    {
        return $this->get('lastModifiedTime');
    }

    /**
     * @param DateTime $lastModifiedTime
     * @return App_Domain_Item
     */
    public function setLastModifiedTime(DateTime $lastModifiedTime = null)
    {
        $this->set('lastModifiedTime', $lastModifiedTime);
		return $this;
    }

    /**
     * @return DateTime
     */
    public function getPublishedTime()
    {
        return $this->get('publishedTime');
    }

    /**
     * @param DateTime $publishedTime
     * @return App_Domain_Item
     */
    public function setPublishedTime(DateTime $publishedTime = null)
    {
        $this->set('publishedTime', $publishedTime);
		return $this;
    }

    /**
     * @return DateTime
     */
    public function getRollbackStopPoint()
    {
        return $this->get('rollbackStopPoint');
    }

    /**
     * @param DateTime $rollbackStopPoint
     * @return App_Domain_Item
     */
    public function setRollbackStopPoint(DateTime $rollbackStopPoint = null)
    {
        $this->set('rollbackStopPoint', $rollbackStopPoint);
		return $this;
    }

    /**
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getFields()
    {
        return $this->get('fields');
    }

    /**
     * @param App_Domain_ItemField $field
     * @return App_Domain_Item
     */
    public function addField(App_Domain_ItemField $field)
    {
        $field->setParentItem($this);
        $this->getFields()->add($field);
		return $this;
    }

    /**
     * @param App_Domain_ItemField $field
     * @return App_Domain_Item
     */
    public function deleteField(App_Domain_ItemField $field)
    {
        $this->getFields()->delete($field);
		return $this;
    }

    /**
     * @return App_Domain_Item
     */
    public function deleteAllFields()
    {
        foreach ($this->getFields() as $field)
        {
            $this->deleteField($field);
        }
		return $this;
    }

    /**
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getParts()
    {
        return $this->get('parts');
    }

    /**
     * @param App_Domain_ItemPart $part
     * @return App_Domain_Item
     */
    public function addPart(App_Domain_ItemPart $part)
    {
        $part->setParentItem($this);
        $this->getParts()->add($part);
		return $this;
    }

    /**
     * @param App_Domain_ItemPart $part
     * @return App_Domain_Item
     */
    public function deletePart(App_Domain_ItemPart $part)
    {
        $this->getParts()->delete($part);
		return $this;
    }

    /**
     * @return App_Domain_Item
     */
    public function deleteAllParts()
    {
        foreach ($this->getParts() as $part)
        {
            $this->deletePart($part);
        }
		return $this;
    }

    /**
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getNotes()
    {
        return $this->get('notes');
    }

    /**
     * @param App_Domain_ItemNote $note
     * @return App_Domain_Item
     */
    public function addNote(App_Domain_ItemNote $note)
    {
        $note->setParentItem($this);
        $this->getNotes()->add($note);
		return $this;
    }

    /**
     * @param App_Domain_ItemNote $note
     * @return App_Domain_Item
     */
    public function deleteNote(App_Domain_ItemNote $note)
    {
        $this->getNotes()->delete($note);
		return $this;
    }

    /**
     * @return App_Domain_Item
     */
    public function deleteAllNotes()
    {
        foreach ($this->getNotes() as $note)
        {
            $this->deleteNote($note);
        }
		return $this;
    }

    /**
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getFreeBlocks()
    {
        return $this->get('freeBlocks');
    }

    /**
     * @param App_Domain_ItemFreeBlock $freeBlock
     * @return App_Domain_Item
     */
    public function addFreeBlock(App_Domain_ItemFreeBlock $freeBlock)
    {
        $freeBlock->setParentItem($this);
        $this->getFreeBlocks()->add($freeBlock);
		return $this;
    }

    /**
     * @param App_Domain_ItemFreeBlock $freeBlock
     * @return App_Domain_Item
     */
    public function deleteFreeBlock(App_Domain_ItemFreeBlock $freeBlock)
    {
        $this->getFreeBlocks()->delete($freeBlock);
		return $this;
    }

    /**
     * @return App_Domain_Item
     */
    public function deleteAllFreeBlocks()
    {
        foreach ($this->getFreeBlocks() as $freeBlock)
        {
            $this->deleteFreeBlock($freeBlock);
        }
		return $this;
    }

    /**
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getBlockSequences()
    {
        return $this->get('blockSequences');
    }

    /**
     * @param App_Domain_ItemBlockSequence $blockSequence
     * @return App_Domain_Item
     */
    public function addBlockSequence(App_Domain_ItemBlockSequence $blockSequence)
    {
        $blockSequence->setParentItem($this);
        $this->getBlockSequences()->add($blockSequence);
		return $this;
    }

    /**
     * @param App_Domain_ItemBlockSequence $blockSequence
     * @return App_Domain_Item
     */
    public function deleteBlockSequence(App_Domain_ItemBlockSequence $blockSequence)
    {
        $this->getBlockSequences()->delete($blockSequence);
		return $this;
    }

    /**
     * @return App_Domain_Item
     */
    public function deleteAllBlockSequences()
    {
        foreach ($this->getBlockSequences() as $blockSequence) {
            $this->deleteBlockSequence($blockSequence);
        }
		return $this;
    }

    /**
     * @return type
     */
    public function getVersioningTransferValues()
    {
        $values = array();

        foreach ($this->_versioningTransferFields as $key) {
            $values[$key] = $this->get($key);
        }

        return $values;
    }

    /**
     * @param array $values
     * @return App_Domain_Item
     */
    public function setVersioningTransferValues(array $values)
    {
        foreach ($values as $field => $value) {

            if (!in_array($field, $this->_versioningTransferFields)) {
                continue;
            }

            $this->set($field, $value);

            if ('fields' !== $field && 'parts' !== $field) {
                continue;
            }

            foreach ($value as $child) {
                $child->setParentItem($this);
            }
        }

		return $this;
    }

}
