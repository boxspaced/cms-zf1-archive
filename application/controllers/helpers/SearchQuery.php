<?php

class Controller_Helper_SearchQuery extends Zend_Controller_Action_Helper_Abstract
{

    /**
     * @param string $paramName
     * @param string $indexKey
     * @param bool $strict
     * @return string
     */
    public function buildFilterSubQuery($paramName, $indexKey, $strict = true)
    {
        $filters = (array) $this->getRequest()->getParam($paramName);

        if (!$this->_filtersHaveAtLeastOneValue($filters)) {
            return '';
        }

        $subQuery = $strict ? ' +(' : ' (';

        foreach ($filters as $filter) {
            $subQuery .= $indexKey . ':"' . $filter . '" OR ';
        }

        return $subQuery . $indexKey . ':"All")';
    }

    /**
     * @param array $filters
     * @return bool
     */
    protected function _filtersHaveAtLeastOneValue(array $filters)
    {
        foreach ($filters as $filter) {

            if ($filter) {
                return true;
            }
        }

        return false;
    }

}
