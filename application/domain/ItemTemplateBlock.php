<?php

class App_Domain_ItemTemplateBlock extends \Boxspaced\EntityManager\Entity\AbstractEntity
{

    /**
     * @return int
     */
    public function getId()
    {
        return $this->get('id');
    }

    /**
     * @param int $id
     * @return App_Domain_ItemTemplateBlock
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return App_Domain_ItemTemplate
     */
    public function getParentTemplate()
    {
        return $this->get('parentTemplate');
    }

    /**
     * @param App_Domain_ItemTemplate $parentTemplate
     * @return App_Domain_ItemTemplateBlock
     */
    public function setParentTemplate(App_Domain_ItemTemplate $parentTemplate)
    {
        $this->set('parentTemplate', $parentTemplate);
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
     * @return App_Domain_ItemTemplateBlock
     */
    public function setName($name)
    {
        $this->set('name', $name);
		return $this;
    }

    /**
     * @return string
     */
    public function getAdminLabel()
    {
        return $this->get('adminLabel');
    }

    /**
     * @param string $adminLabel
     * @return App_Domain_ItemTemplateBlock
     */
    public function setAdminLabel($adminLabel)
    {
        $this->set('adminLabel', $adminLabel);
		return $this;
    }

    /**
     * @return bool
     */
    public function getSequence()
    {
        return $this->get('sequence');
    }

    /**
     * @param bool $sequence
     * @return App_Domain_ItemTemplateBlock
     */
    public function setSequence($sequence)
    {
        $this->set('sequence', $sequence);
		return $this;
    }

}
