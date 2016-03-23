<?
class Sibirix_Model_Row extends Sibirix_Model_Bitrix_Row {
    public function __toString() {
        return $this->NAME;
    }
}
