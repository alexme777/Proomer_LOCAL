<?

/**
 * Class Sibirix_Model_Floor
 *
 */
class Sibirix_Model_Floor extends Sibirix_Model_Bitrix {

    protected $_iblockId = IB_FLOOR;

    protected $_pageSize;

    protected $_selectListFields = [
        'ID',
        'CODE',
        'NAME',
    ];

    protected $_selectFields = [
        'ID',
        'CODE',
        'NAME',
        'PROPERTY_FLOOR_PLAN',
        'PROPERTY_ENTRANCE'
    ];

    /**
     * Копирование этажа
     * @param $floor
     * @param $newEntranceId
     */
    public function copyElement($floor, $newEntranceId=false){
        $filesModel = new Sibirix_Model_Files();

        $newFloorFields = $floor->getSaveArray();
        if (!empty($floor->PROPERTY_FLOOR_PLAN_VALUE)) {
            $fileId = $filesModel->copyFile($floor->PROPERTY_FLOOR_PLAN_VALUE);
            $newFloorFields['PROPERTY_VALUES']['FLOOR_PLAN'] = $fileId;
        }
        if ($newEntranceId) {
            $newFloorFields['PROPERTY_VALUES']['ENTRANCE'] = $newEntranceId;
        }
        $newFloorId = $this->add($newFloorFields);

        $this->copyFlats($floor->ID, $newFloorId);

        return $newFloorId;
    }

    /**
     * Копирование квартир этажа, привязка к новому этажу
     * @param $id
     * @param $newId
     */
    public function copyFlats($id, $newId){
        $flatModel = new Sibirix_Model_Flat();

        $flats = $flatModel->where(["PROPERTY_FLOOR" => $id])->getElements();
        foreach ($flats as $flat) {
            $flatModel->copyElement($flat, $newId);
        }
    }
}
