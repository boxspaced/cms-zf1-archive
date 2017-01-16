<?php

class App_Domain_DigitalGalleryCategory extends \Boxspaced\EntityManager\Entity\AbstractEntity
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
     * @return App_Domain_DigitalGalleryCategory
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->get('type');
    }

    /**
     * @param string $type
     * @return App_Domain_DigitalGalleryCategory
     */
    public function setType($type)
    {
        $this->set('type', $type);
		return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->get('text');
    }

    /**
     * @param string $text
     * @return App_Domain_DigitalGalleryCategory
     */
    public function setText($text)
    {
        $this->set('text', $text);
		return $this;
    }

}
