<?php

class App_View_Helper_TeaserTruncate extends Zend_View_Helper_Abstract
{

    /**
     * @param string $string
     * @param int $chars
     * @return string
     */
    public function teaserTruncate($string, $chars = 80)
    {
        $string = trim(strip_tags($string));
        $string = wordwrap($string, $chars, PHP_EOL);

        $firstBreakPosition = strpos($string, PHP_EOL);

        if (false !== $firstBreakPosition) {
            return substr($string, 0, $firstBreakPosition);
        }

        return $string;
    }

}
