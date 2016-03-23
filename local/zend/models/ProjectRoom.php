<?

/**
 * Class Sibirix_Model_Plan
 *
 */
class Sibirix_Model_ProjectRoom extends Sibirix_Model_Bitrix {

    protected $_iblockId = IB_ORDER_PROJECT_ROOM;

    protected $_pageSize;

    protected $_selectListFields = [
		'ID',
        'CODE',
        'NAME',
		'PROPERTY_TYPE_ROOM',
		'PROPERTY_PEOPLE',
		'PROPERTY_PEOPLE_CHILDREN',
		'PROPERTY_DESIGN_LIKED',
		'PROPERTY_SUGGEST',
		'PROPERTY_FILE',
		'PROPERTY_TIME',
		'PROPERTY_AREA',
		'PROPERTY_PRICE_SQUARE'
    ];
	
		    /**
     * Добавляет файл к множественному свойству
     * возвращает список новых файлов
     * @param $id
     * @param $imageFile
     * @return array
     * @throws Exception
     */
    public function addRoom($fields){
       
		if (!empty($fields["NAME"])) {
			$fields["CODE"] = CUtil::translit($fields["NAME"], "ru");
		}
		$result = $this->Add($fields);
		return $result;
    }
	
	public function getProjectRoomList($filter, $sort, $page, $profile=false, $limit = 11) {
        $roomList = $this->select($this->_selectListFields, true)->orderBy($sort, true)->where($filter)->getPageItem($page, $limit);
        return $roomList;
    }
	
	public function editRoomProject($fields) {

        $bxElement           = new CIBlockElement();
        $fields["IBLOCK_ID"] = IB_ORDER_PROJECT_ROOM;
		
        if ($fields["ID"] > 0) {
            $designId    = $fields["ID"];
            $designProps = $fields["PROPERTY_VALUES"];
            unset($fields["ID"], $fields["PROPERTY_VALUES"]);

            if (!empty($fields["NAME"])) {
                $fields["CODE"] = CUtil::translit($fields["NAME"], "ru");
            }

            $updateResult = $bxElement->Update($designId, $fields);

            if ($updateResult && !empty($designProps)) {
                $bxElement->SetPropertyValuesEx($designId, IB_ORDER_PROJECT_ROOM, $designProps);
            }

            $result = $updateResult;
        } else {
			
            if (empty($fields["NAME"])) {
                $fields["NAME"] = 'Project Order Name'. time();
                $fields["CODE"] = CUtil::translit($fields["NAME"], "ru");
            }
            $newId = $bxElement->Add($fields);

            $result = $newId;
        }
        if (empty($fields["PRICE_VALUE"])) {
           // return $result;
        }

        $bxPrice = new CPrice();
        $res = $bxPrice->GetList(array(), array(
                "PRODUCT_ID"       => $fields["PRICE_VALUE"]["PRODUCT_ID"],
                "CATALOG_GROUP_ID" => $fields["PRICE_VALUE"]["CATALOG_GROUP_ID"],
                "CURRENCY"         => $fields["PRICE_VALUE"]["CURRENCY"]
            ));

        if ($arr = $res->Fetch()) {
            $bxPrice->Update($arr["ID"], $fields["PRICE_VALUE"]);
        } else {
            $bxCatalogProduct = new CCatalogProduct();
            $bxCatalogProduct->Add(["ID" => $fields["PRICE_VALUE"]["PRODUCT_ID"]]);
            $bxPrice->Add($fields["PRICE_VALUE"]);
        }

        return $result;
    }
}
