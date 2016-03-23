<?
/**
 * Class Sibirix_Validate_Uri
 */
class Sibirix_Validate_Uri extends Zend_Validate_Abstract {

    /**
     *
     */
    const MSG_INVALID_URI = 'uriIsInvalid';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::MSG_INVALID_URI => "Invalid URI",
    );

    /**
     * @param mixed $value
     * @return bool
     */
    public function isValid($value) {
        $this->_setValue($value);

        //Validate the URI
        $valid = Zend_Uri::check($value);

        //Return validation result TRUE|FALSE  
        if ($valid) {
            return true;
        } else {
            $this->_error(self::MSG_INVALID_URI);
            return false;

        }
    }
}
