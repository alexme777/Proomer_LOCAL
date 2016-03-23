<?
/**
 * Class Sibirix_Validate_Bitrix_Webform_Regex
 */
class Sibirix_Validate_Bitrix_Webform_Regex extends Sibirix_Validate_Bitrix_Webform {
    /**
     * @return array
     */
    public static function getDescription() {
        return self::getMetadata(
            "Sibirix_Validate_Bitrix_Webform_Regex",
            "zv_regex",
            self::t("Regex"),
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
        return parent::isValid($arParams, $arQuestion, $arAnswers, $arValues, new Zend_Validate_Regex(array('pattern' => '/'.$arParams['PATTERN'].'/')));
    }

    /**
     * @return array
     */
    public static function getSettings() {
        $settings = array(
            "PATTERN" => array(
                "TITLE"   => self::t("Pattern"),
                "TYPE"    => "TEXT",
                "DEFAULT" => ".*",
            )
        );

        return $settings + parent::getSettings();
    }
}
