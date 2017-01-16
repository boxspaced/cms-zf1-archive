<?php

class App_Domain_User extends \Boxspaced\EntityManager\Entity\AbstractEntity
{

    const TYPE_PUBLIC = 'public';
    const TYPE_ADMIN = 'admin';

    /**
     * @return int
     */
    public function getId()
    {
        return $this->get('id');
    }

    /**
     * @param int $id
     * @return App_Domain_User
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
     * @return App_Domain_User
     */
    public function setType($type)
    {
        $this->set('type', $type);
		return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->get('username');
    }

    /**
     * @param string $username
     * @return App_Domain_User
     */
    public function setUsername($username)
    {
        $this->set('username', $username);
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
     * @return App_Domain_User
     */
    public function setEmail($email)
    {
        $this->set('email', $email);
		return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->get('password');
    }

    /**
     * @param string $password
     * @return App_Domain_User
     */
    public function setPassword($password)
    {
        $this->set('password', $password);
		return $this;
    }

    /**
     * @return DateTime
     */
    public function getLastLogin()
    {
        return $this->get('lastLogin');
    }

    /**
     * @param DateTime $lastLogin
     * @return App_Domain_User
     */
    public function setLastLogin(DateTime $lastLogin = null)
    {
        $this->set('lastLogin', $lastLogin);
		return $this;
    }

    /**
     * @return DateTime
     */
    public function getThisLogin()
    {
        return $this->get('thisLogin');
    }

    /**
     * @param DateTime $thisLogin
     * @return App_Domain_User
     */
    public function setThisLogin(DateTime $thisLogin = null)
    {
        $this->set('thisLogin', $thisLogin);
		return $this;
    }

    /**
     * @return bool
     */
    public function getActivated()
    {
        return $this->get('activated');
    }

    /**
     * @param bool $activated
     * @return App_Domain_User
     */
    public function setActivated($activated)
    {
        $this->set('activated', $activated);
		return $this;
    }

    /**
     * @return bool
     */
    public function getEverBeenActivated()
    {
        return $this->get('everBeenActivated');
    }

    /**
     * @param bool $everBeenActivated
     * @return App_Domain_User
     */
    public function setEverBeenActivated($everBeenActivated)
    {
        $this->set('everBeenActivated', $everBeenActivated);
		return $this;
    }

    /**
     * @return DateTime
     */
    public function getRegisteredTime()
    {
        return $this->get('registeredTime');
    }

    /**
     * @param DateTime $registeredTime
     * @return App_Domain_User
     */
    public function setRegisteredTime(DateTime $registeredTime = null)
    {
        $this->set('registeredTime', $registeredTime);
		return $this;
    }

}
