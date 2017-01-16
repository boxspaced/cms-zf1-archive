<?php

class App_Domain_Course extends \Boxspaced\EntityManager\Entity\AbstractEntity
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
     * @return App_Domain_Course
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
     * @return App_Domain_Course
     */
    public function setCategory($category)
    {
        $this->set('category', $category);
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
     * @return App_Domain_Course
     */
    public function setTitle($title)
    {
        $this->set('title', $title);
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
     * @return App_Domain_Course
     */
    public function setCode($code)
    {
        $this->set('code', $code);
		return $this;
    }

    /**
     * @return string
     */
    public function getDay()
    {
        return $this->get('day');
    }

    /**
     * @param string $day
     * @return App_Domain_Course
     */
    public function setDay($day)
    {
        $this->set('day', $day);
		return $this;
    }

    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->get('startDate');
    }

    /**
     * @param DateTime $startDate
     * @return App_Domain_Course
     */
    public function setStartDate(DateTime $startDate = null)
    {
        $this->set('startDate', $startDate);
		return $this;
    }

    /**
     * @return DateTime
     */
    public function getTime()
    {
        return $this->get('time');
    }

    /**
     * @param DateTime $time
     * @return App_Domain_Course
     */
    public function setTime(DateTime $time = null)
    {
        $this->set('time', $time);
		return $this;
    }

    /**
     * @return int
     */
    public function getNumWeeks()
    {
        return $this->get('numWeeks');
    }

    /**
     * @param int $numWeeks
     * @return App_Domain_Course
     */
    public function setNumWeeks($numWeeks)
    {
        $this->set('numWeeks', $numWeeks);
		return $this;
    }

    /**
     * @return float
     */
    public function getHoursPerWeek()
    {
        return $this->get('hoursPerWeek');
    }

    /**
     * @param float $hoursPerWeek
     * @return App_Domain_Course
     */
    public function setHoursPerWeek($hoursPerWeek)
    {
        $this->set('hoursPerWeek', $hoursPerWeek);
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
     * @return App_Domain_Course
     */
    public function setVenue($venue)
    {
        $this->set('venue', $venue);
		return $this;
    }

    /**
     * @return float
     */
    public function getFee()
    {
        return $this->get('fee');
    }

    /**
     * @param float $fee
     * @return App_Domain_Course
     */
    public function setFee($fee)
    {
        $this->set('fee', $fee);
		return $this;
    }

    /**
     * @return float
     */
    public function getConcession()
    {
        return $this->get('concession');
    }

    /**
     * @param float $concession
     * @return App_Domain_Course
     */
    public function setConcession($concession)
    {
        $this->set('concession', $concession);
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
     * @return App_Domain_Course
     */
    public function setDayTime($dayTime)
    {
        $this->set('dayTime', $dayTime);
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
     * @return App_Domain_Course
     */
    public function setDescription($description)
    {
        $this->set('description', $description);
		return $this;
    }

}
