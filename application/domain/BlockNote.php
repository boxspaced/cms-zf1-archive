<?php

class App_Domain_BlockNote extends \Boxspaced\EntityManager\Entity\AbstractEntity
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
     * @return App_Domain_BlockNote
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return App_Domain_Block
     */
    public function getParentBlock()
    {
        return $this->get('parentBlock');
    }

    /**
     * @param App_Domain_Block $parentBlock
     * @return App_Domain_BlockNote
     */
    public function setParentBlock(App_Domain_Block $parentBlock)
    {
        $this->set('parentBlock', $parentBlock);
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
     * @return App_Domain_BlockNote
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
     * @return App_Domain_BlockNote
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
     * @return App_Domain_BlockNote
     */
    public function setCreatedTime(DateTime $createdTime = null)
    {
        $this->set('createdTime', $createdTime);
		return $this;
    }

}
