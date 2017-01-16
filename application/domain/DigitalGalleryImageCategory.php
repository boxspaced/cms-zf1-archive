<?php

class App_Domain_DigitalGalleryImageCategory extends \Boxspaced\EntityManager\Entity\AbstractEntity
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
     * @return App_Domain_DigitalGalleryImageCategory
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return App_Domain_DigitalGalleryImage
     */
    public function getImage()
    {
        return $this->get('image');
    }

    /**
     * @param App_Domain_DigitalGalleryImage $image
     * @return App_Domain_DigitalGalleryImageCategory
     */
    public function setImage(App_Domain_DigitalGalleryImage $image)
    {
        $this->set('image', $image);
		return $this;
    }

    /**
     * @return App_Domain_DigitalGalleryCategory
     */
    public function getCategory()
    {
        return $this->get('category');
    }

    /**
     * @param App_Domain_DigitalGalleryCategory $category
     * @return App_Domain_DigitalGalleryImageCategory
     */
    public function setCategory(App_Domain_DigitalGalleryCategory $category)
    {
        $this->set('category', $category);
		return $this;
    }

}
