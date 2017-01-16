<?php

class App_Domain_ProvisionalLocation extends \Boxspaced\EntityManager\Entity\AbstractEntity
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
     * @return App_Domain_ProvisionalLocation
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return $this->get('to');
    }

    /**
     * @param string $to
     * @return App_Domain_ProvisionalLocation
     */
    public function setTo($to)
    {
        $this->set('to', $to);
		return $this;
    }

    /**
     * @return int
     */
    public function getBeneathMenuItemId()
    {
        return $this->get('beneathMenuItemId');
    }

    /**
     * @param int $beneathMenuItemId
     * @return App_Domain_ProvisionalLocation
     */
    public function setBeneathMenuItemId($beneathMenuItemId)
    {
        $this->set('beneathMenuItemId', $beneathMenuItemId);
		return $this;
    }

}
