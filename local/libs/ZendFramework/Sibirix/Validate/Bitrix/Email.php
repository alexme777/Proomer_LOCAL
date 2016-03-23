<?
/**
 * Class Sibirix_Validate_Bitrix_Email
 */
class Sibirix_Validate_Bitrix_Email extends Zend_Validate_Abstract {

    /**
     *
     */
    const MSG_INVALID_EMAIL = 'bitrixEmailIsInvalid';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::MSG_INVALID_EMAIL => "Email address is incorrect",
    );

    /**
     * @param mixed $value
     * @return bool
     */
    public function isValid($value) {
        $this->_setValue($value);

        //Validate the Email
        $valid = check_email($value, true);

        //Return validation result TRUE|FALSE  
        if ($valid) {
            return true;
        } else {
            $this->_error(self::MSG_INVALID_EMAIL);
            return false;

        }
    }
}
