<?

/**
 * Class Sibirix_Model_Complex_Row
 */
class Sibirix_Model_Complex_Row extends Sibirix_Model_Bitrix_Row {

    /**
     * Получает url детальной страницы
     * @return mixed
     */
    public function getUrl() {
        return EZendManager::url(['elementCode' => $this->CODE], 'design-detail');
    }

    /**
     * Получает список дизайнов
     * @param array $filter
     * @return array
     */
    public function getDesignList($filter = array(), $select = array("ID")) {
        $houseModel    = new Sibirix_Model_House();
        $entranceModel = new Sibirix_Model_Entrance();
        $floorModel    = new Sibirix_Model_Floor();
        $flatModel     = new Sibirix_Model_Flat();
        $designModel   = new Sibirix_Model_Design();
        $designList = [];
        $filter['PROPERTY_STATUS'] = DESIGN_STATUS_PUBLISHED;

        $houseList = $houseModel->select(["ID"], true)->where(["PROPERTY_COMPLEX" => $this->ID])->getElements();
        $houseIds = array_map(function($obj){return $obj->ID;}, $houseList);

        if (!empty($houseIds)) {
            $entranceList = $entranceModel->select(["ID"], true)->where(["PROPERTY_HOUSE" => $houseIds])->getElements();
            $entranceIds = array_map(function ($obj) {
                return $obj->ID;
            }, $entranceList);

            if (!empty($entranceIds)) {
                $floorList = $floorModel->select(["ID"], true)->where(["PROPERTY_ENTRANCE" => $entranceIds])->getElements();
                $floorIds = array_map(function ($obj) {
                    return $obj->ID;
                }, $floorList);

                if (!empty($floorIds)) {
                    $flatList = $flatModel->select(["ID", "PROPERTY_PLAN"], true)->where(["PROPERTY_FLOOR" => $floorIds])->getElements();
                    $planIds = array_map(function ($obj) {
                        return $obj->PROPERTY_PLAN_VALUE;
                    }, $flatList);

                    if (!empty($planIds)) {
                        $designList = $designModel->select($select, true)->where(array_merge($filter, ["PROPERTY_PLAN" => $planIds]))->getElements();
                    }
                }
            }
        }

        return $designList;
    }

}
