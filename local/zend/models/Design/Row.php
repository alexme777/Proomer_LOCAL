<?

/**
 * Class Sibirix_Model_Design_Row
 */
class Sibirix_Model_Design_Row extends Sibirix_Model_Bitrix_Row {

    /**
     * Получает url детальной страницы
     * @return mixed
     */
    public function getUrl() {
        return EZendManager::url(['elementCode' => $this->CODE], 'design-detail');
    }

    /**
     * Возвращает цену дизайна
     * @return float
     */
    public function getPrice() {
        CModule::IncludeModule("catalog");
        $bxPrice = new CPrice();
        $priceGetList = $bxPrice->GetList(
            array(),
            array(
                "PRODUCT_ID" => $this->ID,
            )
        )->Fetch();

        $price = $priceGetList['PRICE'];

        return $price;
    }

    /**
     * Возвращает название дизайна, проверяя является ли оно временным
     * @return string
     */
    public function getName() {
        if (preg_match("/" . PATTERN_DESIGN_NAME . "\d*/", $this->NAME)) {
            return "";
        } else {
            return $this->NAME;
        }
    }
}
