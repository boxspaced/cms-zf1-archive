<?php

class App_View_Helper_Date extends Zend_View_Helper_Abstract
{

    /**
     * @param mixed $input
     * @param string $format
     * @return string
     */
    public function date($input, $format)
    {
        if ($input instanceof DateTime) {
            return $input->format($format);
        }

        try {

            $date = new DateTime($input);
            return $date->format($format);

        } catch (Exception $e) {
            return 'invalid date';
        }
    }

}
