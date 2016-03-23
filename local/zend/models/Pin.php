<?
/**
 * Class Sibirix_Model_Room
 */
class Sibirix_Model_Pin extends Sibirix_Model_Bitrix {

    /**
     * @var CIBlockElement
     */
    protected $_bxElement;
    protected $_iblockId = IB_PIN;
    protected $_selectFields = array(
        'ID',
		'NAME',
		'PREVIEW_PICTURE',
		'PROPERTY_COORDS',
		'PROPERTY_ROOMS',
		'PROPERTY_URL',
		'PROPERTY_PRICE'
    );

    public function init($initParams = NULL) {
        $this->_bxElement = new CIBlockElement();
    }

    /**
     * Возвращает пины
     * @param $roomId
     * @param $name
     * @param $area
     * @return bool
     */
    public function getPin($roomId) {
        $pins = $this->select($this->_selectFields, true)->where(["=PROPERTY_ROOM" => $roomId])->orderBy([], true)->getElements();
		return $pins;
    }
	
	/**
     * Добавляет пины
     */
    public function addPin($data, $indx) {
        $id = $this->add($data);
		$pin = $this->select($this->_selectFields, true)->where(["=ID" => $id])->orderBy([], true)->getElement();
		$element = new CIBlockElement();
		$element->SetPropertyValuesEx($id, $this->_iblockId, $data["PROPERTY_VALUES"]);
    }

	public function getPins($where) {
        $pins = $this->select($this->_selectFields, true)->where($where, true)->orderBy([], true)->getElements();
		return $pins;
    }
	
	public function updatePin($data) {
		$element = new CIBlockElement();
        $element->SetPropertyValuesEx($data['ID'], $this->_iblockId, $data['PROPERTY_VALUES']);
    }
	
	public function delPin($where) {
		$this->remove($where);
    }
}
