<?php

class App_Domain_WhatsOn extends \Boxspaced\EntityManager\Entity\AbstractEntity
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
     * @return App_Domain_WhatsOn
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->get('category');
    }

    /**
     * @param string $category
     * @return App_Domain_WhatsOn
     */
    public function setCategory($category)
    {
        $this->set('category', $category);
		return $this;
    }

    /**
     * @return string
     */
    public function getActivity()
    {
        return $this->get('activity');
    }

    /**
     * @param string $activity
     * @return App_Domain_WhatsOn
     */
    public function setActivity($activity)
    {
        $this->set('activity', $activity);
		return $this;
    }

    /**
     * @return string
     */
    public function getDayTime()
    {
        return $this->get('dayTime');
    }

    /**
     * @param string $dayTime
     * @return App_Domain_WhatsOn
     */
    public function setDayTime($dayTime)
    {
        $this->set('dayTime', $dayTime);
		return $this;
    }

    /**
     * @return string
     */
    public function getVenue()
    {
        return $this->get('venue');
    }

    /**
     * @param string $venue
     * @return App_Domain_WhatsOn
     */
    public function setVenue($venue)
    {
        $this->set('venue', $venue);
		return $this;
    }

    /**
     * @return string
     */
    public function getAge()
    {
        return $this->get('age');
    }

    /**
     * @param string $age
     * @return App_Domain_WhatsOn
     */
    public function setAge($age)
    {
        $this->set('age', $age);
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
     * @return App_Domain_WhatsOn
     */
    public function setDescription($description)
    {
        $this->set('description', $description);
		return $this;
    }

    /**
     * @return DateTime
     */
    public function getSpecificDate()
    {
        return $this->get('specificDate');
    }

    /**
     * @param DateTime $specificDate
     * @return App_Domain_WhatsOn
     */
    public function setSpecificDate(DateTime $specificDate = null)
    {
        $this->set('specificDate', $specificDate);
		return $this;
    }

}
