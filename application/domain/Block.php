<?php

class App_Domain_Block extends \Boxspaced\EntityManager\Entity\AbstractEntity
{

    /**
     * @var string[]
     */
    protected $_versioningTransferFields = array(
        'fields',
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
     * @return App_Domain_Block
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return App_Domain_Block
     */
    public function getVersionOf()
    {
        return $this->get('versionOf');
    }

    /**
     * @param App_Domain_Block
     * @return App_Domain_Block
     */
    public function setVersionOf($versionOf)
    {
        $this->set('versionOf', $versionOf);
        return $this;
    }

    /**
     * @return App_Domain_BlockType
     */
    public function getType()
    {
        return $this->get('type');
    }

    /**
     * @param App_Domain_BlockType $type
     * @return App_Domain_Block
     */
    public function setType($type)
    {
        $this->set('type', $type);
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->get('name');
    }

    /**
     * @param string $name
     * @return App_Domain_Block
     */
    public function setName($name)
    {
        $this->set('name', $name);
        return $this;
    }

    /**
     * @return App_Domain_BlockTemplate
     */
    public function getTemplate()
    {
        return $this->get('template');
    }

    /**
     * @param App_Domain_BlockTemplate $template
     * @return App_Domain_Block
     */
    public function setTemplate($template)
    {
        $this->set('template', $template);
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
     * @return App_Domain_Block
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
     * @return App_Domain_Block
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
     * @return App_Domain_Block
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
     * @return App_Domain_Block
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
     *
     * @param App_Domain_User $author
     * @return App_Domain_Block
     */
    public function setAuthor($author)
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
     * @return App_Domain_Block
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
     * @return App_Domain_Block
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
     * @return App_Domain_Block
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
     * @return App_Domain_Block
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
     * @param App_Domain_BlockField $field
     * @return App_Domain_Block
     */
    public function addField(App_Domain_BlockField $field)
    {
        $field->setParentBlock($this);
        $this->getFields()->add($field);
        return $this;
    }

    /**
     * @param App_Domain_BlockField $field
     * @return App_Domain_Block
     */
    public function deleteField(App_Domain_BlockField $field)
    {
        $this->getFields()->delete($field);
        return $this;
    }

    /**
     * @return App_Domain_Block
     */
    public function deleteAllFields()
    {
        foreach ($this->getFields() as $field) {
            $this->deleteField($field);
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
     * @param App_Domain_BlockNote $note
     * @return App_Domain_Block
     */
    public function addNote(App_Domain_BlockNote $note)
    {
        $note->setParentBlock($this);
        $this->getNotes()->add($note);
        return $this;
    }

    /**
     * @param App_Domain_BlockNote $note
     * @return App_Domain_Block
     */
    public function deleteNote(App_Domain_BlockNote $note)
    {
        $this->getNotes()->delete($note);
        return $this;
    }

    /**
     * @return App_Domain_Block
     */
    public function deleteAllNotes()
    {
        foreach ($this->getNotes() as $note) {
            $this->deleteNote($note);
        }
        return $this;
    }

    /**
     * @return array
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
     * @return App_Domain_Block
     */
    public function setVersioningTransferValues(array $values)
    {
        foreach ($values as $field => $value) {

            if (!in_array($field, $this->_versioningTransferFields)) {
                continue;
            }

            $this->set($field, $value);

            if ('fields' !== $field) {
                continue;
            }

            foreach ($value as $child) {
                $child->setParentBlock($this);
            }
        }

        return $this;
    }

}
