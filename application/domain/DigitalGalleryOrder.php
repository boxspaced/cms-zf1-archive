<?php

class App_Domain_DigitalGalleryOrder extends \Boxspaced\EntityManager\Entity\AbstractEntity
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
     * @return App_Domain_DigitalGalleryOrder
     */
    public function setId($id)
    {
        $this->set('id', $id);
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
     * @return App_Domain_DigitalGalleryOrder
     */
    public function setName($name)
    {
        $this->set('name', $name);
		return $this;
    }

    /**
     * @return string
     */
    public function getDayPhone()
    {
        return $this->get('dayPhone');
    }

    /**
     * @param string $dayPhone
     * @return App_Domain_DigitalGalleryOrder
     */
    public function setDayPhone($dayPhone)
    {
        $this->set('dayPhone', $dayPhone);
		return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->get('email');
    }

    /**
     * @param string $email
     * @return App_Domain_DigitalGalleryOrder
     */
    public function setEmail($email)
    {
        $this->set('email', $email);
		return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->get('message');
    }

    /**
     * @param string $message
     * @return App_Domain_DigitalGalleryOrder
     */
    public function setMessage($message)
    {
        $this->set('message', $message);
		return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedTime()
    {
        return $this->get('createdTime');
    }

    /**
     * @param DateTime $createdTime
     * @return App_Domain_DigitalGalleryOrder
     */
    public function setCreatedTime(DateTime $createdTime = null)
    {
        $this->set('createdTime', $createdTime);
		return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->get('code');
    }

    /**
     * @param string $code
     * @return App_Domain_DigitalGalleryOrder
     */
    public function setCode($code)
    {
        $this->set('code', $code);
		return $this;
    }

    /**
     * @return \Boxspaced\EntityManager\Collection\Collection
     */
    public function getItems()
    {
        return $this->get('items');
    }

    /**
     * @param App_Domain_DigitalGalleryOrderItem $item
     * @return App_Domain_DigitalGalleryOrder
     */
    public function addItem(App_Domain_DigitalGalleryOrderItem $item)
    {
        $item->setOrder($this);
        $this->getItems()->add($item);
		return $this;
    }

    /**
     * @param App_Domain_DigitalGalleryOrderItem $item
     * @return App_Domain_DigitalGalleryOrder
     */
    public function deleteItem(App_Domain_DigitalGalleryOrderItem $item)
    {
        $this->getItems()->delete($item);
		return $this;
    }

    /**
     * @return App_Domain_DigitalGalleryOrder
     */
    public function deleteAllItems()
    {
        foreach ($this->getItems() as $item) {
            $this->deleteItem($item);
        }
		return $this;
    }

}
