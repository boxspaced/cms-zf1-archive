<?php

class App_Domain_DigitalGalleryImage extends \Boxspaced\EntityManager\Entity\AbstractEntity
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
     * @return App_Domain_DigitalGalleryImage
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return string
     */
    public function getKeywords()
    {
        return $this->get('keywords');
    }

    /**
     * @param string $keywords
     * @return App_Domain_DigitalGalleryImage
     */
    public function setKeywords($keywords)
    {
        $this->set('keywords', $keywords);
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
     * @return App_Domain_DigitalGalleryImage
     */
    public function setTitle($title)
    {
        $this->set('title', $title);
		return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->get('description');
    }

    /**
     * @param string $description
     * @return App_Domain_DigitalGalleryImage
     */
    public function setDescription($description)
    {
        $this->set('description', $description);
		return $this;
    }

    /**
     * @return string
     */
    public function getImageNo()
    {
        return $this->get('imageNo');
    }

    /**
     * @param string $imageNo
     * @return App_Domain_DigitalGalleryImage
     */
    public function setImageNo($imageNo)
    {
        $this->set('imageNo', $imageNo);
		return $this;
    }

    /**
     * @return string
     */
    public function getCredit()
    {
        return $this->get('credit');
    }

    /**
     * @param string $credit
     * @return App_Domain_DigitalGalleryImage
     */
    public function setCredit($credit)
    {
        $this->set('credit', $credit);
		return $this;
    }

    /**
     * @return string
     */
    public function getCopyright()
    {
        return $this->get('copyright');
    }

    /**
     * @param string $copyright
     * @return App_Domain_DigitalGalleryImage
     */
    public function setCopyright($copyright)
    {
        $this->set('copyright', $copyright);
		return $this;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->get('price');
    }

    /**
     * @param float $price
     * @return App_Domain_DigitalGalleryImage
     */
    public function setPrice($price)
    {
        $this->set('price', $price);
		return $this;
    }

    /**
     * @return string
     */
    public function getImageName()
    {
        return $this->get('imageName');
    }

    /**
     * @param string $imageName
     * @return App_Domain_DigitalGalleryImage
     */
    public function setImageName($imageName)
    {
        $this->set('imageName', $imageName);
		return $this;
    }

    /**
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getCategories()
    {
        return $this->get('categories');
    }

    /**
     * @param App_Domain_DigitalGalleryImageCategory $category
     * @return App_Domain_DigitalGalleryImage
     */
    public function addCategory(App_Domain_DigitalGalleryImageCategory $category)
    {
        $category->setImage($this);
        $this->getCategories()->add($category);
		return $this;
    }

    /**
     * @param App_Domain_DigitalGalleryImageCategory $category
     * @return App_Domain_DigitalGalleryImage
     */
    public function deleteCategory(App_Domain_DigitalGalleryImageCategory $category)
    {
        $this->getCategories()->delete($category);
		return $this;
    }

    /**
     * @return App_Domain_DigitalGalleryImage
     */
    public function deleteAllCategories()
    {
        foreach ($this->getCategories() as $category) {
            $this->deleteCategory($category);
        }
		return $this;
    }

}
