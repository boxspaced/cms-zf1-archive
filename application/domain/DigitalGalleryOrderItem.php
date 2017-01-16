<?php

class App_Domain_DigitalGalleryOrderItem extends \Boxspaced\EntityManager\Entity\AbstractEntity
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
     * @return App_Domain_DigitalGalleryOrderItem
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return App_Domain_DigitalGalleryOrder
     */
    public function getOrder()
    {
        return $this->get('order');
    }

    /**
     * @param App_Domain_DigitalGalleryOrder $order
     * @return App_Domain_DigitalGalleryOrderItem
     */
    public function setOrder(App_Domain_DigitalGalleryOrder $order)
    {
        $this->set('order', $order);
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
     * @return App_Domain_DigitalGalleryOrderItem
     */
    public function setImage(App_Domain_DigitalGalleryImage $image)
    {
        $this->set('image', $image);
		return $this;
    }

}
