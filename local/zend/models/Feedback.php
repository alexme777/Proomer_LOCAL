<?

/**
 * Class Sibirix_Model_Feedback
 *
 */
class Sibirix_Model_Feedback extends Sibirix_Model_Bitrix {

    protected $_iblockId = IB_FEEDBACK;

    public function add($fields) {
        global $DB;
        $lang = new CLang();

        $fieldAdd["NAME"]                        = $fields["name"];
        $fieldAdd["ACTIVE_FROM"]                 = date($DB->DateFormatToPHP($lang->GetDateFormat()));
        $fieldAdd["PREVIEW_TEXT"]                = $fields["message"];
        $fieldAdd["PROPERTY_VALUES"]["CONTACTS"] = $fields["contacts"];

        return parent::Add($fieldAdd);
    }

}
