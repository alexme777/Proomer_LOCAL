<?

/**
 * Class Sibirix_Model_Entrance
 *
 */
class Sibirix_Model_Entrance extends Sibirix_Model_Bitrix {

    protected $_iblockId = IB_ENTRANCE;

    protected $_pageSize;

    protected $_selectListFields = [
        'ID',
        'CODE',
        'NAME',
        'PROPERTY_HOUSE'
    ];

    protected $_selectFields = [
        'ID',
        'CODE',
        'NAME',
        'PROPERTY_HOUSE'
    ];

    /**
     * Копирование подъезда
     * @param $entrance
     * @param $newHouseId
     */
    public function copyElement($entrance, $newHouseId=false){
        $newEntranceFields = $entrance->getSaveArray();
        if ($newHouseId) {
            $newEntranceFields['PROPERTY_VALUES']['HOUSE'] = $newHouseId;
        }
        $newEntranceId = $this->add($newEntranceFields);

        $this->copyFloors($entrance->ID, $newEntranceId);

        return $newEntranceId;
    }

    /**
     * Копирование этажей подъезда, привязка к нопому подъезду
     * @param $id
     * @param $newId
     */
    public function copyFloors($id, $newId){
        $floorModel = new Sibirix_Model_Floor();

        $floors = $floorModel->where(["PROPERTY_ENTRANCE" => $id])->getElements();
        foreach ($floors as $floor) {
            $floorModel->copyElement($floor, $newId);
        }

    }
}
