<?php

class ZendExt_Paginator_Adapter_Callback implements Zend_Paginator_Adapter_Interface
{

    /**
     * @var callable
     */
    protected $_itemsCallback;

    /**
     * @var callable
     */
    protected $_countCallback;

    /**
     * @param callable $itemsCallback
     * @param callable $countCallback
     */
    public function __construct(callable $itemsCallback, callable $countCallback)
    {
        $this->_itemsCallback = $itemsCallback;
        $this->_countCallback = $countCallback;
    }

    /**
     * @return int
     */
    public function count()
    {
        return call_user_func($this->_countCallback);
    }

    /**
     * @param int $offset
     * @param int $itemCountPerPage
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        return call_user_func($this->_itemsCallback, $offset, $itemCountPerPage);
    }

}
