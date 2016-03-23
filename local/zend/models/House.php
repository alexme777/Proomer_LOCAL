<?

/**
 * Class Sibirix_Model_House
 *
 */
class Sibirix_Model_House extends Sibirix_Model_Bitrix {

    protected $_iblockId = IB_HOUSE;

    protected $_instanceClass = 'Sibirix_Model_House_Row';

    protected $_pageSize;

    protected $_selectListFields = [
        'ID',
        'CODE',
        'NAME',
    ];

    protected $_selectFields = [
        "ID",
        "PROPERTY_STREET",
        "PROPERTY_HOUSE_NUMBER",
        "PROPERTY_MAP_POSITION",
        "PROPERTY_STATUS",
        "PROPERTY_PLAN",
        "PROPERTY_ENTRANCE_CNT",
        "PROPERTY_FLOOR_CNT",
        "PROPERTY_COMPLEX"
    ];

    /**
     * Получает количество квартир с разным количеством комнат для дома
     * @param $houseId
     * @return array
     */
    public function getFlatList($houseId) {
        $entranceModel = new Sibirix_Model_Entrance();
        $floorModel    = new Sibirix_Model_Floor();
        $flatModel     = new Sibirix_Model_Flat();
        $flats = [];

        $entranceList = $entranceModel->select(["ID"], true)->where(["PROPERTY_HOUSE" => $houseId])->getElements();
		
        $entranceIds = array_map(function($obj){return $obj->ID;}, $entranceList);

        if (!empty($entranceIds)) {
            $floorList = $floorModel->select(["ID"], true)->where(["PROPERTY_ENTRANCE" => $entranceIds])->getElements();
            $floorIds = array_map(function ($obj) {
                return $obj->ID;
            }, $floorList);
			
            if (!empty($floorIds)) {
                $flats = $flatModel
                    ->select(["ID", "PROPERTY_ROOM_CNT"], true)
                    ->where(["PROPERTY_FLOOR" => $floorIds])
                    ->orderBy(["PROPERTY_ROOM_CNT" => "ASC", "CNT" => "DESC"], true)
                    ->uniqueBy("PROPERTY_ROOM_CNT")
                    ->getElements();
            }
			
        }

        return $flats;
    }
	
	 /**
     * Получает количество планировок
     * @param $houseId
     * @return array
     */
    public function getFlatListb($houseId) {
        $entranceModel = new Sibirix_Model_Entrance();
        $floorModel    = new Sibirix_Model_Floor();
        $flatModel     = new Sibirix_Model_Flat();
		$planModel     = new Sibirix_Model_Plan();
        $flats = [];

        $entranceList = $entranceModel->select(["ID"], true)->where(["PROPERTY_HOUSE" => $houseId])->getElements();
		
        $entranceIds = array_map(function($obj){return $obj->ID;}, $entranceList);

        if (!empty($entranceIds)) {
            $floorList = $floorModel->select(["ID"], true)->where(["PROPERTY_ENTRANCE" => $entranceIds])->getElements();
            $floorIds = array_map(function ($obj) {
                return $obj->ID;
            }, $floorList);
			
            if (!empty($floorIds)) {
                $flats = $flatModel
                    ->select(["ID", "PROPERTY_ROOM_CNT", "PROPERTY_PLAN"], true)
                    ->where(["PROPERTY_FLOOR" => $floorIds])
                    ->orderBy(["PROPERTY_ROOM_CNT" => "ASC", "CNT" => "DESC"], true)
                    ->getElements();
            }
			
        }

        return $flats;
    }
	
	
	

    /**
     * Копирование дома
     * @param $house
     * @return bool
     */
    public function copyElement($house){
        $filesModel = new Sibirix_Model_Files();

        $newHouseFields = $house->getSaveArray();
        $newHouseFields['PROPERTY_VALUES']['STATUS'] = $house->PROPERTY_STATUS_ENUM_ID;

        if (!empty($house->PROPERTY_PLAN_VALUE)) {
            $fileId = $filesModel->copyFile($house->PROPERTY_PLAN_VALUE);
            $newHouseFields['PROPERTY_VALUES']['PLAN'] = $fileId;
        }
        $newHouseId = $this->add($newHouseFields);

        $this->copyEntrances($house->ID, $newHouseId);

        return $newHouseId;
    }

    /**
     * Копирование подъездов дома, привязка к новому дому
     * @param $id
     * @param $newId
     */
    public function copyEntrances($id, $newId){
        $entranceModel = new Sibirix_Model_Entrance();

        $entrances = $entranceModel->where(['PROPERTY_HOUSE' => $id])->getElements();
        foreach ($entrances as $entrance) {
            $entranceModel->copyElement($entrance, $newId);
        }
    }
}
