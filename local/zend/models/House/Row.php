<?

/**
 * Class Sibirix_Model_House_Row
 */
class Sibirix_Model_House_Row extends Sibirix_Model_Bitrix_Row {

    public function getAddress() {
        return trim(sprintf('%s, %s', trim($this->PROPERTY_STREET_VALUE), trim($this->PROPERTY_HOUSE_NUMBER_VALUE)));
    }
}
