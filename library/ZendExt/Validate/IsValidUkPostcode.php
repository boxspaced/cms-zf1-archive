<?php

class ZendExt_Validate_IsValidUkPostcode extends Zend_Validate_Abstract
{

    const INVALID_UK_POSTCODE = 'invalidUkPostcode';

    protected $_messageTemplates = array(
        self::INVALID_UK_POSTCODE => 'Invalid UK postcode',
    );

    public function isValid($value)
    {
        $string = (string) $value;
        $this->_setValue($string);

        $postcode = strtolower($string);
        $postcode = str_replace(' ', '', $postcode);

        // Permitted letters depend upon their position in the postcode
        $alpha1 = "[abcdefghijklmnoprstuwyz]";                          // Character 1
        $alpha2 = "[abcdefghklmnopqrstuvwxy]";                          // Character 2
        $alpha3 = "[abcdefghjkstuw]";                                   // Character 3
        $alpha4 = "[abehmnprvwxy]";                                     // Character 4
        $alpha5 = "[abdefghjlnpqrstuwxyz]";                             // Character 5

        // Expression for postcodes AN NAA, ANN NAA, AAN NAA, and AANN NAA
        $patterns[0] = '/^(' . $alpha1 . '{1}' . $alpha2 . '{0,1}[0-9]{1,2})([0-9]{1}' . $alpha5 . '{2})$/';
        // Expression for postcodes ANA NAA
        $patterns[1] = '/^(' . $alpha1 . '{1}[0-9]{1}' . $alpha3 . '{1})([0-9]{1}' . $alpha5 . '{2})$/';
        // Expression for postcodes AANA NAA
        $patterns[2] = '/^(' . $alpha1 . '{1}' . $alpha2 . '[0-9]{1}' . $alpha4 . ')([0-9]{1}' . $alpha5 . '{2})$/';

        // Check the string against the three types of postcodes
        foreach ($patterns as $pattern) {

            $status = @preg_match($pattern, $postcode);

            if (false === $status) {
                require_once 'Zend/Validate/Exception.php';
                throw new Zend_Validate_Exception("Internal error matching pattern '{$pattern}' against value '{$postcode}'");
            }

            if ($status) {
                return true;
            }
        }

        $this->_error(self::INVALID_UK_POSTCODE);
        return false;
    }

}
