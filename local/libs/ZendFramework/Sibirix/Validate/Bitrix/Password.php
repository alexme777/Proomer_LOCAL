<?
/**
 * Class Sibirix_Validate_Bitrix_Password
 * Валидатор для поля пароля, учитывающий правила битрикса
 */
class Sibirix_Validate_Bitrix_Password extends Zend_Validate_Abstract {

    const LENGTH      = 'bitrixPasswordLength';
    const UPPER       = 'bitrixPasswordUpper';
    const LOWER       = 'bitrixPasswordLower';
    const DIGIT       = 'bitrixPasswordDigit';
    const PUNCTUATION = 'bitrixPasswordPunct';

    protected $_messageTemplates = array(
        self::LENGTH      => "'%value%' must be at least %min% characters in length",
        self::UPPER       => "'%value%' must contain at least one uppercase letter",
        self::LOWER       => "'%value%' must contain at least one lowercase letter",
        self::DIGIT       => "'%value%' must contain at least one digit character",
        self::PUNCTUATION => "'%value%' must contain at least one punctuation character"
    );

    /**
     * @var array
     */
    protected $_messageVariables = array(
        'min' => '_min'
    );

    protected $_min = 0;

    protected $_settings;

    /**
     *
     */
    public function __construct() {
        $settings = Zend_Session::namespaceGet('SESS_AUTH');

        if ($settings['POLICY']) {
            $this->_settings = $settings['POLICY'];
        }

        if ($this->_settings['PASSWORD_LENGTH'] > 0) {
            $this->_min = $this->_settings['PASSWORD_LENGTH'];
        } else {
            unset($this->_messageTemplates[self::LENGTH]);
        }

        if ($this->_settings['PASSWORD_UPPERCASE'] !== 'Y') {
            unset($this->_messageTemplates[self::UPPER]);
        }

        if ($this->_settings['PASSWORD_LOWERCASE'] !== 'Y') {
            unset($this->_messageTemplates[self::LOWER]);
        }

        if ($this->_settings['PASSWORD_PUNCTUATION'] !== 'Y') {
            unset($this->_messageTemplates[self::PUNCTUATION]);
        }

        if ($this->_settings['PASSWORD_DIGITS'] !== 'Y') {
            unset($this->_messageTemplates[self::DIGIT]);
        }
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function isValid($value) {
        $this->_setValue($value);

        $isValid = true;

        if ($this->_min > strlen($value)) {
            $this->_error(self::LENGTH);
            $isValid = false;
        }

        if ($this->_settings['PASSWORD_UPPERCASE'] == 'Y') {
            if (!preg_match('/[A-Z]/', $value)) {
                $this->_error(self::UPPER);
                $isValid = false;
            }
        }

        if ($this->_settings['PASSWORD_LOWERCASE'] == 'Y') {
            if (!preg_match('/[a-z]/', $value)) {
                $this->_error(self::LOWER);
                $isValid = false;
            }
        }

        if ($this->_settings['PASSWORD_PUNCTUATION'] == 'Y') {
            if (!preg_match('/[,.<>/?;:\'"\[\]{}\|`~!@#$%^&*()-_+=]/', $value)) {
                $this->_error(self::PUNCTUATION);
                $isValid = false;
            }
        }

        if ($this->_settings['PASSWORD_DIGITS'] == 'Y') {
            if (!preg_match('/\d/', $value)) {
                $this->_error(self::DIGIT);
                $isValid = false;
            }
        }

        return $isValid;
    }
}
