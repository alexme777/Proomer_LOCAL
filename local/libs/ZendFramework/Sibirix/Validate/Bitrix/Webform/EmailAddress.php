<?
/**
 * Class Sibirix_Validate_Bitrix_Webform_EmailAddress
 */
class Sibirix_Validate_Bitrix_Webform_EmailAddress extends Sibirix_Validate_Bitrix_Webform {
    /**
     * @return array
     */
    public static function getDescription() {
        return self::getMetadata(
            "Sibirix_Validate_Bitrix_Webform_EmailAddress",
            "zv_email_address",
            self::t("EMail"),
            "text"
        );
    }

    /**
     * @param $arParams
     * @param $arQuestion
     * @param $arAnswers
     * @param $arValues
     * @return bool
     */
    public static function isValid($arParams, $arQuestion, $arAnswers, $arValues) {
        return parent::isValid($arParams, $arQuestion, $arAnswers, $arValues, new Zend_Validate_EmailAddress());
    }
}
