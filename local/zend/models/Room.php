<?
/**
 * Class Sibirix_Model_Room
 */
class Sibirix_Model_Room extends Sibirix_Model_Bitrix {

    /**
     * @var CIBlockElement
     */
    protected $_bxElement;
    protected $_iblockId = IB_ROOM;
    protected $_selectFields = array(
        'ID',
        'NAME',
        'PROPERTY_IMAGES',
        'PROPERTY_AREA',
        'PROPERTY_DESIGN',
		'PROPERTY_ROOM_PLAN',
		'PROPERTY_PRICE_SQUARE'
    );

    public function init($initParams = NULL) {
        $this->_bxElement = new CIBlockElement();
    }

    /**
     * Добавляет комнату
     * @param $designId
     * @param $name
     * @param $area
     * @return bool
     */
    public function addRoom($designId, $name, $area) {
        if (!((int)$designId > 0)) return false;

        $fields = array(
            "IBLOCK_ID"       => $this->_iblockId,
            "NAME"            => $name,
            "PROPERTY_VALUES" => array(
                "DESIGN" => $designId,
                "AREA"   => $area
            )
        );
        $addResult = $this->_bxElement->Add($fields);

        return $addResult;
    }
	
	public function getRoomList($filter, $sort, $page, $profile=false, $limit = 11) {
		
        $roomList = $this->select($this->_selectFields, true)->where($filter)->orderBy($sort, true)->getPageItem($page, $limit);
        return $roomList;
    }
	
    /**
     * Удаляет комнату
     * @param $roomId
     * @return bool
     */
    public function deleteRoom($roomId) {
        if (!((int)$roomId > 0)) return false;
        $delResult = $this->_bxElement->Delete($roomId);

        return $delResult;
    }

    /**
     * Проверяет есть ли у пользователя доступ к комнате
     * @param $roomId
     * @param int $createdBy
     * @return bool
     */
    public function checkRoomAccess($roomId, $createdBy = 0) {
        $roomData = $this->getElement($roomId);
        $designId = $roomData->PROPERTY_DESIGN_VALUE;

        if (!($designId > 0)) {
            return false;
        }

        if (!Sibirix_Model_User::isAuthorized()) {
            return false;
        }

        if ($createdBy == 0) {
            $designModel = new Sibirix_Model_Design();
            $designData = $designModel->getElement($designId);
            $createdBy  = $designData->CREATED_BY;
        }

        if ($createdBy != Sibirix_Model_User::getId()) {
            return false;
        }

        return true;
    }

    /**
     * Добавляет файл к множественному свойству
     * возвращает список новых файлов
     * @param $id
     * @param $imageFile
     * @return array
     * @throws Exception
     */
    public function addImage($id, $imageFile) {

        $imageExist = $this->select(["PROPERTY_IMAGES"], true)->getElement($id);
        $alreadyImages = array();

        foreach ($imageExist->PROPERTY_IMAGES_VALUE as $key => $image) {
            $alreadyImages[$imageExist->PROPERTY_IMAGES_PROPERTY_VALUE_ID[$key]] = CIBlock::makeFilePropArray($image);
        }
        $fields["ID"] = $id;
        $fields["PROPERTY_VALUES"] = array(
            "IMAGES" => $alreadyImages + array("n0" => array("VALUE" => $imageFile))
        );

        $this->_bxElement->SetPropertyValuesEx($id, IB_ROOM, $fields["PROPERTY_VALUES"]);

        $newImage = $this->select(["PROPERTY_IMAGES"], true)->getElement($id);
        $resultImages = array();
        foreach ($newImage->PROPERTY_IMAGES_VALUE as $key => $imageId) {
            $resultImages[] = array(
                "valueId" => $newImage->PROPERTY_IMAGES_PROPERTY_VALUE_ID[$key],
                "imgSrc" => Resizer::resizeImage($imageId, "DROPZONE_ROOMS_PHOTO")
            );
        }

        return $resultImages;
    }

    /**
     * Удаляет файл из множетсвенного свойства
     * возвращает список новых файлов
     * @param $roomId
     * @param $imageItemId
     * @return array
     * @throws Exception
     */
    public function deleteImageItem($roomId, $imageItemId) {
        $arFile["MODULE_ID"] = "iblock";
        $arFile["del"] = "Y";

        $this->_bxElement->SetPropertyValueCode($roomId, "IMAGES", Array($imageItemId => Array("VALUE" => $arFile)));

        $newImage = $this->select(["PROPERTY_IMAGES"], true)->getElement($roomId);
        $resultImages = array();
        foreach ($newImage->PROPERTY_IMAGES_VALUE as $key => $imageId) {
            $resultImages[] = array(
                "valueId" => $newImage->PROPERTY_IMAGES_PROPERTY_VALUE_ID[$key],
                "imgSrc" => Resizer::resizeImage($imageId, "DROPZONE_ROOMS_PHOTO")
            );
        }

        return $resultImages;
    }
}
