<?
/**
 * Class Sibirix_Model_City
 */
class Sibirix_Model_City extends Sibirix_Model_Bitrix {

    protected $_iblockId = IB_CITY;


    /**
     * Хук, который не позволяет сделать больше одного города по умолчанию
     * @param $fields
     * @return mixed
     */
    public static function setDefaultCity($fields) {
        if ($fields["IBLOCK_ID"] != IB_CITY) return $fields;

        $cityId = $fields["ID"];
        $dbProps = CIBlockElement::GetProperty($fields["IBLOCK_ID"], $cityId, array(), array("CODE"=>"DEFAULT_CITY"));

        if ($propValue = $dbProps->Fetch()) {
            $defaultCityPropId = EnumUtils::getPropertyCheckboxId($fields["IBLOCK_ID"], "DEFAULT_CITY", "Y");
            if ($defaultCityPropId == $propValue["VALUE"]) {
                $self = new self();
                $cityList = $self->where(["!ID" => $cityId, "IBLOCK_ID" => $fields["IBLOCK_ID"]], true)->getElements();

                if (!empty($cityList)) {
                    foreach ($cityList as $city) {
                        CIBlockElement::SetPropertyValuesEx($city->ID, false, array("DEFAULT_CITY" => false));
                    }
                }
            }
        }

        return $fields;
    }

    /**
     * ID города по названию
     * @param $cityName
     * @return bool
     */
    public function getCityIdByName($cityName) {
        $city = $this->where(["NAME" => $cityName])->getElement();
        if (!empty($city)) {
            return $city->ID;
        } else {
            return false;
        }
    }

    /**
     * Возвращает ID города по умолчанию
     * @return mixed
     */
    public function getDefaultCity() {
        $city = $this->where(["!PROPERTY_DEFAULT_CITY_VALUE" => false])->getElement();

        if (!empty($city)) {
            return $city->ID;
        } else {
            $city = $this->getElement();
            return $city->ID;
        }
    }

}
