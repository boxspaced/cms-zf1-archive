<?php

class App_Domain_Adapter_TransactableOrder implements App_Lib_PaymentGateway_Transaction
{

    /**
     * @var App_Domain_Order
     */
    protected $_order;

    /**
     * @param App_Domain_Order $order
     */
    public function __construct(App_Domain_Order $order)
    {
        $this->_order = $order;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->_order->getUser()->getFirstName();
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->_order->getUser()->getLastName();
    }

    /**
     * @return string
     */
    public function getCardNumber()
    {
        return $this->_order->getUser()->getCreditCardNumber();
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->_order->getTotal();
    }

}
