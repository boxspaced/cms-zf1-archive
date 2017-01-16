<?php

class App_Domain_ItemNote extends \Boxspaced\EntityManager\Entity\AbstractEntity
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
     * @return App_Domain_ItemNote
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return App_Domain_Item
     */
    public function getParentItem()
    {
        return $this->get('parentItem');
    }

    /**
     * @param App_Domain_Item $parentItem
     * @return App_Domain_ItemNote
     */
    public function setParentItem(App_Domain_Item $parentItem)
    {
        $this->set('parentItem', $parentItem);
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
     * @return App_Domain_ItemNote
     */
    public function setText($text)
    {
        $this->set('text', $text);
		return $this;
    }

    /**
     * @return App_Domain_User
     */
    public function getUser()
    {
        return $this->get('user');
    }

    /**
     * @param App_Domain_User $user
     * @return App_Domain_ItemNote
     */
    public function setUser(App_Domain_User $user)
    {
        $this->set('user', $user);
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
     * @return App_Domain_ItemNote
     */
    public function setCreatedTime(DateTime $createdTime = null)
    {
        $this->set('createdTime', $createdTime);
		return $this;
    }

}
