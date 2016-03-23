<?
/**
 * Class Sibirix_Validate_Bitrix_Webform
 */
abstract class Sibirix_Validate_Bitrix_Webform {
    /**
     * @param $className
     * @param $name
     * @param $description
     * @param $types
     * @return array
     */
    public static function getMetadata($className, $name, $description, $types) {
        return array(
            "NAME"            => $name,
            "DESCRIPTION"     => $description,
            "TYPES"           => explode(",", $types),
            "SETTINGS"        => array(
                $className,
                "getSettings"
            ),
            "CONVERT_TO_DB"   => array(
                $className,
                "toDB"
            ),
            "CONVERT_FROM_DB" => array(
                $className,
                "fromDB"
            ),
            "HANDLER"         => array(
                $className,
                "isValid"
            )
        );
    }

    /**
     * @param $arParams
     * @param $arQuestion
     * @param $arAnswers
     * @param $arValues
     * @param Zend_Validate_Interface $validator
     * @return bool
     */
    public static function isValid($arParams, $arQuestion, $arAnswers, $arValues, Zend_Validate_Interface $validator) {
        foreach ($arValues as $value) {
            if (!empty($value) && !$validator->isValid($value)) {
                if (isset($arParams['ERROR_MESSAGE']) && !empty($arParams['ERROR_MESSAGE'])) {
                    self::setErrorMessage(self::t($arParams['ERROR_MESSAGE']));
                } else {
                    foreach ($validator->getMessages() as $message) {
                        self::setErrorMessage($message);
                    }
                }
                return false;
            }
        }

        return true;
    }

    /**
     * @param $arParams
     * @return string
     */
    public static function toDB($arParams) {
        return serialize($arParams);
    }

    /**
     * @param $strParams
     * @return mixed
     */
    public static function fromDB($strParams) {
        return unserialize($strParams);
    }

    /**
     * @return array
     */
    public static function getSettings() {
        return array(
            "ERROR_MESSAGE" => array(
                "TITLE"   => self::t("errorMessage"),
                "TYPE"    => "TEXT",
                "DEFAULT" => "",
            )
        );
    }

    /**
     * @throws Zend_Exception
     */
    public static function getDescription() {
        throw new Zend_Exception('in the child must inherit');
    }

    /**
     * @param $message
     */
    public static function setErrorMessage($message) {
        Zend_Registry::get('BX_APPLICATION')->ThrowException(/*"#FIELD_NAME#: " .*/ $message);
    }

    /**
     * @param $key
     * @return string
     */
    public function t($key) {
        return Zend_Validate_Abstract::getDefaultTranslator()->translate($key);
    }
}