<?

/**
 * Class Sibirix_Model_Feedback
 *
 */
class Sibirix_Model_SuperMan extends Sibirix_Model_Bitrix {

    protected $_iblockId = IB_SUPER_MAN;

    public function add($fields) {
        global $DB;
        $lang = new CLang();

        $fieldAdd["NAME"]                        = $fields["name"];
        $fieldAdd["ACTIVE_FROM"]                 = date($DB->DateFormatToPHP($lang->GetDateFormat()));
        $fieldAdd["PREVIEW_TEXT"]                = $fields["message"];
        $fieldAdd["PROPERTY_VALUES"]["CONTACTS"] = $fields["contacts"];
		$fieldAdd["PROPERTY_VALUES"]["ADDRESS"] = $fields["address"];
		$fieldAdd["PROPERTY_VALUES"]["CITY"] = $fields["city"];

        return parent::Add($fieldAdd);
    }

}
