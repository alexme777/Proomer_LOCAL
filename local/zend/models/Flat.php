<?

/**
 * Class Sibirix_Model_Flat
 *
 */
class Sibirix_Model_Flat extends Sibirix_Model_Bitrix {

    protected $_iblockId = IB_FLAT;

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
        'PROPERTY_FLOOR',
        'PROPERTY_PLAN',
        'PROPERTY_ROOM_CNT'
    ];

    /**
     * Копирование квартиры
     * @param $flat
     * @param $newFloorId - id нового этажа
     */
    public function copyElement($flat, $newFloorId=false){
        $newFlatFields = $flat->getSaveArray();
        if ($newFloorId) {
            $newFlatFields['PROPERTY_VALUES']['FLOOR'] = $newFloorId;
        }
        return $this->add($newFlatFields);
    }
	
	public function getFlatList($filter, $sort, $page, $profile=false, $limit = 11) {
        $planList = $this->select($this->_selectFields, true)->orderBy($sort, true)->where($filter)->getPageItem($page, $limit);
      //  $this->_getDesignInfo($planList->items);

        return $planList;
    }
}
