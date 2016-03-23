<?
/**
 * Class Sibirix_Form_Element_Bitrix_InputPassword
 */
class Sibirix_Form_Element_Bitrix_InputPassword extends Zend_Form_Element_Password {

    /**
     *
     */
    public function init() {
        $this->addValidator(new Sibirix_Validate_Bitrix_Password());
        $this->addFilter('StringTrim');
    }
}
