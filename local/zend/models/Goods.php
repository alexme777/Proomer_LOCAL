<?

/**
 * Class Sibirix_Model_Complex
 *
 */
class Sibirix_Model_Goods extends Sibirix_Model_Bitrix {

    protected $_iblockId = IB_GOODS;
    protected $_selectFields = array(
        'ID',
        'NAME',
		'PROPERTY_LENGTH',
		'PROPERTY_WIDTH',
		'PROPERTY_HEIGHT',
		'PROPERTY_MATERIAL',
		'PROPERTY_MADEIN',
		'PROPERTY_PRICE',
		'PROPERTY_ARTICLE',
		'PROPERTY_STATUS',
		'PROPERTY_ID_USER',
		'PROPERTY_IMG',
		'PROPERTY_COLOR',
		'PROPERTY_STYLE',
		'PROPERTY_PREVIEW',
		'PROPERTY_USED_DESIGN',
		'PROPERTY_PLAN_FLAT',
		'IBLOCK_SECTION_ID',
		'PREVIEW_PICTURE',
		'DETAIL_PICTURE',
		'PREVIEW_TEXT',
		'DETAIL_TEXT',
		'DISCOUNT_VALUE',
		'PRINT_DISCOUNT_VALUE',
		'PRINT_VALUE',
		'SHOW_COUNTER'
    );
	
	protected $_selectListFields = [
        'ID',
        'CODE',
        'CREATED_BY',
        'NAME',
        'DETAIL_PICTURE',
        'PREVIEW_TEXT',
        'PROPERTY_BUDGET',
        'PROPERTY_STATUS',
        'PROPERTY_LIKE_CNT'
    ];
	
	    /**
     * Перезаписывание актуального количества элементов на странице
     */
    public function reinitViewCounter() {
        $this->_pageSize = Sibirix_Model_ViewCounter::getViewCounter();
    }
	
/*=================================================================================*/
//	test
/*=================================================================================*/	
	public function getTest() {
		return $this->test();
    }
	
/*=================================================================================*/
//	Берет картинки у категорий
/*=================================================================================*/	
	public function getDataImg($categories) {
	
		return $this->getImageData($categories);
     
    }
/*=================================================================================*/
//	Категории
/*=================================================================================*/
	public function getCat($params = []) {
		$categories = $this->getSections($params);
		$categories = $this->getImgItems($categories);
		return $categories;
    }
/*=================================================================================*/
//	Категории
/*=================================================================================*/
	public function getCatChild($where) {
		$params['where'] = ["=SECTION_ID" => $where];
		return $this->getSections($params);
     
    }
/*=================================================================================*/
//	Добавляет один товар
/*=================================================================================*/
	public function addGoods($data) {
	
		return $this->add($data);
        //echo $element->SetPropertyValuesEx(100, IB_GOODS, $data);
    }
	
/*=================================================================================*/
//	Возвращает список товаров
/*=================================================================================*/
	public function getGoods($where) {
		$goods['ITEMS'] = $this->select($this->_selectFields, true)->where(["=PROPERTY_ID_USER" => $where])->orderBy([], true)->getElements();
		return $goods;
    }
    public function getGoodsDiscount($where = 1) {
			$goods = $this->select($this->_selectFields, true)->where([$where])->orderBy([], true)->getElements();
			return $goods;
    }
	public function getItems($where, $offset = '', $limit = '', $sort = ''){
		$goods = $this->select($this->_selectFields, true)->where($where)->page($offset, $limit)->orderBy($sort, true)->getElements();
		return $goods;
    }

    public function getImgItems($arr_row) {
    	foreach($arr_row as $row){
    		if(isset($row->PREVIEW_PICTURE)){$row->PREVIEW_PICTURE = CFile::GetPath($row->PREVIEW_PICTURE);};
    		if(isset($row->DETAIL_PICTURE)){$row->DETAIL_PICTURE = CFile::GetPath($row->DETAIL_PICTURE);};
    		if(isset($row->PROPERTY_IMG_VALUE)){$row->PROPERTY_IMG_VALUE = CFile::GetPath($row->PROPERTY_IMG_VALUE);};
    	};
		
		return $arr_row;
    }
    public function getImgItemsPrev($item) {
		
    	static $arr_img = array();
    //	foreach($arr_row as $row){
    		//if(!empty($item->PROPERTY_PREVIEW_PROPERTY_VALUE_ID)){
				foreach($item->PROPERTY_PREVIEW_VALUE as $k => $v){
					//$path = CFile::GetPath($row->PROPERTY_PREVIEW_VALUE);
					array_push($arr_img, CFile::GetPath($v));
				}
    	//	}
    //	};
    	$item->PROPERTY_PREVIEW_VALUE = $arr_img;
		return $item;
    }
	public function getGoodsItem($where){
		$goods = $this->select($this->_selectFields, true)->where(["=ID" => $where])->orderBy([], true)->getElements();
		return $goods;
    }
    public function getGoodsAItem($where){
		$goods = $this->select($this->_selectFields, true)->where(["=ID" => $where])->getElements();
		$this->_getGoodsInfo($goods);
		return $goods[0];
    }
    public function getItemList($where){
		$goods = $this->select($this->_selectFields, true)->where($where)->orderBy([], true)->getElements();
		return $goods;
    }

	public function getGoodsId($id) {
		$goods = $this->select($this->_selectFields, true)->where(["=ID" => $id])->getElement();
		return $goods;
    }
/*=================================================================================*/
//	Обновляет товары
/*=================================================================================*/
	public function changestatusGoods($id, $fields) {
		$result = $this->updateValue($id, $fields);
		return $result;
    }
	public function updateGoods($id, $fields, $prop_fields) {
		$result = $this->update($id, $fields);
		$result = $this->updateValue($id, $prop_fields);
		return $result;
    }

/*=================================================================================*/
//	Удаляет товары
/*=================================================================================*/	
	public function delGoods($id, $where) {
		$this->remove($id);
    }
/*=================================================================================*/
/*=================================================================================*/		
	 public function addPlan($id, $imageFile) {
        $imageExist = $this->select(["PROPERTY_PREVIEW"], true)->getElement($id);
        $alreadyImages = array();
		
        foreach ($imageExist->PROPERTY_PREVIEW_VALUE as $key => $image) {
            $alreadyImages[$imageExist->PROPERTY_PREVIEW_PROPERTY_VALUE_ID[$key]] = CIBlock::makeFilePropArray($image);
        }
        $fields["ID"] = $id;
        $fields["PROPERTY_VALUES"] = array(
            "PREVIEW" => $alreadyImages + array("n0" => array("VALUE" => $imageFile))
        );
	
        $element = new CIBlockElement();
		
        $element->SetPropertyValuesEx($id, IB_GOODS, $fields["PROPERTY_VALUES"]);

        $newImage = $this->select(["PROPERTY_PREVIEW"], true)->getElement($id);
		//echo print_r($newImage);
		//exit;
        $resultImages = array();
        foreach ($newImage->PROPERTY_PREVIEW_VALUE as $key => $imageId) {
            $resultImages[] = array(
                "valueId" => $newImage->PROPERTY_PREVIEW_PROPERTY_VALUE_ID[$key],
                "imgSrc" => Resizer::resizeImage($imageId, "DROPZONE_PREVIEW_PHOTO")
            );
        }

        return $resultImages;
    }

    /**
     * Удаляет файл из множетсвенного свойства
     * возвращает список новых файлов
     * @param $imageItemId
     * @return array
     * @throws Exception
     */
    public function deletePlan($designId, $imageItemId) {
        $arFile["MODULE_ID"] = "iblock";
        $arFile["del"] = "Y";

        $element = new CIBlockElement();
        $element->SetPropertyValueCode($designId, "PREVIEW", Array($imageItemId => Array("VALUE" => $arFile)));

        $newImage = $this->select(["PROPERTY_PREVIEW"], true)->getElement($designId);
        $resultImages = array();
        foreach ($newImage->PROPERTY_PREVIEW_VALUE as $key => $imageId) {
            $resultImages[] = array(
                "valueId" => $newImage->PROPERTY_PREVIEW_PROPERTY_VALUE_ID[$key],
                "imgSrc" => Resizer::resizeImage($imageId, "DROPZONE_PREVIEW_PHOTO")
            );
        }

        return $resultImages;
    }
	
	public function getShopList($filter = array(), $sort, $page, $profile=false) {
        if (!$profile) {
            $filter['PROPERTY_STATUS'] = GOODS_STATUS_PUBLISHED;
        }
        $itemList = $this->select($this->_selectFields, true)->where($filter)->orderBy($sort, true)->getPage($page);
		$this->_getGoodsInfo($itemList->items);
        return $itemList;
    }
	
	public function getProductList($filter = array(), $sort, $limit = 10000) {
        $filter['PROPERTY_STATUS'] = GOODS_STATUS_PUBLISHED;
		//$itemList = new Array();
        $itemList->items = $this->select($this->_selectFields, true)->where($filter)->orderBy($sort, true)->limit($limit)->getElements();
		$this->_getGoodsInfo($itemList->items);
        return $itemList;
    }
	
	protected function _getGoodsInfo($designs) {

		
        $this->getImageData($designs);
      
        $list = [];
        //активные лайки пользователя
        if (Sibirix_Model_User::isAuthorized()) {
            $hh = Highload::instance(HL_LIKES)->cache(0);
            $list = $hh->where(['UF_USER_ID' => Sibirix_Model_User::getId()])->fetch();

            $list = array_map(function ($obj) {
                return $obj['UF_DESIGN'];
            }, $list);
        }

        //корзина
        $basketModel = new Sibirix_Model_Basket();
        $basketItems = $basketModel->getBasketProductsId();
		$favouriteItems = $basketModel->getFavouriteProductsId();
		//
        foreach ($designerGetList as $ind => $designer) {
            $designerList[$designer->ID] = $designer;
        }

        foreach ($designs as $key => $designItem) {
            //$designItem->PRICES = $designPriceList[$designItem->ID];
            //$designItem->DESIGNER = $designerList[$designItem->CREATED_BY];
            //$designItem->IS_LIKED = (!empty($list)) ? in_array($designItem->ID, $list) : false;
            $designItem->IS_IN_BASKET = (!empty($basketItems))? in_array($designItem->ID, $basketItems) : false;
			$designItem->IS_IN_FAVOURITE = (!empty($favouriteItems))? in_array($designItem->ID, $favouriteItems) : false;
			if(CCatalogSKU::IsExistOffers($designItem->ID, IB_GOODS)){
				$designItem->OFFERS = 1;
			}
			else{$designItem->OFFERS = 0;};
			$discount = CCatalogDiscount::GetDiscountByProduct($designItem->ID);
			if(!empty($discount)){
				$designItem->DISCOUNT = $discount[0];
				$discount[0]['NEW_PRICE_VALUE'] = $designItem->PROPERTY_PRICE_VALUE - round($designItem->PROPERTY_PRICE_VALUE * $designItem->DISCOUNT['VALUE']/100);
				$designItem->DISCOUNT = $discount[0];
			}
        }
    }
	
	    /**
     * Формирование фильтрующего массива
     * @param $getParams
     * @return array
     */
    public function prepareFilter($getParams) {
        if (empty($getParams)) return array();

        $filterKeys = array(
            "price",
            "primaryColor",
            "style",
			"madeIn",
            "status",
			"categoryId"
        );

        foreach ($getParams as $paramKey => $param) {
            if (!in_array($paramKey, $filterKeys)) {
                unset($getParams[$paramKey]);
            }
        }

        if (empty($getParams)) return array();

        $filterArray = array();

        //По цене дизайна
        if (!empty($getParams["price"]) && $getParams["price"] != '0:0') {
            $avgPriceArray = explode(":", $getParams["price"]);
            $filterArray[">=CATALOG_PRICE_" . BASE_PRICE] = $avgPriceArray[0];
            $filterArray["<=CATALOG_PRICE_" . BASE_PRICE] = $avgPriceArray[1];

        }

        //По средней бюджета
        /*if (!empty($getParams["budget"])) {
            $avgPriceArray = explode(":", $getParams["budget"]);
            if ($avgPriceArray[0] == 0) {
                $filterArray[] = array(
                    "LOGIC" => "OR",
                    [">=PROPERTY_BUDGET" => $avgPriceArray[0], "<=PROPERTY_BUDGET" => $avgPriceArray[1]],
                    ["=PROPERTY_BUDGET"  => false],
                );
            } else {
                $filterArray[">=PROPERTY_BUDGET"] = $avgPriceArray[0];
                $filterArray["<=PROPERTY_BUDGET"] = $avgPriceArray[1];
            }
        }*/
		
		 //По категории
        if (!empty($getParams["categoryId"])) {
            $filterArray["=SECTION_ID"] = $getParams["categoryId"];
        }
		
        //По цвету
        if (!empty($getParams["primaryColor"])) {
            $filterArray["=PROPERTY_COLOR"] = $getParams["primaryColor"];
        }
		
		//По производителю
        if (!empty($getParams["madeIn"])) {
            $filterArray["=PROPERTY_MADEIN"] = $getParams["madeIn"];
        }

        //По стилю
        if (!empty($getParams["style"])) {
            $filterArray["=PROPERTY_STYLE"] = $getParams["style"];
        }

        //статус
		$filterArray["=PROPERTY_STATUS"] = GOODS_STATUS_PUBLISHED;
        /*if (!empty($getParams["status"])) {
            $status = $getParams["status"];
            $key = array_search(0, $status);

            if ($key !== false) {
                $filterArray["CATALOG_PRICE_" . BASE_PRICE] = 0;
                unset($status[$key]);
            }

            if (!empty($status)) {
                $filterArray["=PROPERTY_STATUS"] = $getParams["status"];
            }
        }*/

        //По дополнительным параметрам из сервиса поиска дизайна
        //Дизайны привязаны к объектам через планировки квартир. Всё сводится к выбору нужных планировок квартир
        $houseModel    = new Sibirix_Model_House();
        $entranceModel = new Sibirix_Model_Entrance();
        $floorModel    = new Sibirix_Model_Floor();
        $flatModel     = new Sibirix_Model_Flat();

        $setSearchServiceFilter = false;
        if (!empty($getParams["flat"])) {
            //Если задана квартира
            $flatList = $flatModel->select(["PROPERTY_PLAN"], true)->where(["ID" => $getParams["flat"]])->getElements();
            $setSearchServiceFilter = true;
        } elseif(!empty($getParams["floor"])) {
            //Если задан этаж
            $flatList = $flatModel->select(["PROPERTY_PLAN"], true)->where(["PROPERTY_FLOOR" => $getParams["floor"]])->getElements();
            $setSearchServiceFilter = true;
        } elseif(!empty($getParams["entrance"])) {
            //Если задан подъезд
            $floorList = $floorModel->orderBy([], true)->select(["ID"], true)->where(["PROPERTY_ENTRANCE" => $getParams["entrance"]])->getElements();
            $idList    = array_map(function($obj){return $obj->ID;}, $floorList);

            if (!empty($idList)) {
                $flatList = $flatModel->select(["PROPERTY_PLAN"], true)->where(["PROPERTY_FLOOR" => $idList])->getElements();
            }
            $setSearchServiceFilter = true;
        } elseif(!empty($getParams["house"])) {
            //Если задан дом
            $entranceList = $entranceModel->orderBy([], true)->select(["ID"], true)->where(["PROPERTY_HOUSE" => $getParams["house"]])->getElements();
            $idList    = array_map(function($obj){return $obj->ID;}, $entranceList);

            if (!empty($idList)) {
                $floorList = $floorModel->orderBy([], true)->select(["ID"], true)->where(["PROPERTY_ENTRANCE" => $idList])->getElements();
                $idList    = array_map(function($obj){return $obj->ID;}, $floorList);

                if (!empty($idList)) {
                    $flatList = $flatModel->select(["PROPERTY_PLAN"], true)->where(["PROPERTY_FLOOR" => $idList])->getElements();
                }
            }
            $setSearchServiceFilter = true;
        } elseif(!empty($getParams["complexId"])) {
            //Если задан комплекс
            $houseList = $houseModel->orderBy([], true)->select(["ID"], true)->where(["PROPERTY_COMPLEX" => $getParams["complexId"]])->getElements();
            $idList    = array_map(function($obj){return $obj->ID;}, $houseList);

            if (!empty($idList)) {
                $entranceList = $entranceModel->orderBy([], true)->select(["ID"], true)->where(["PROPERTY_HOUSE" => $idList])->getElements();
                $idList    = array_map(function($obj){return $obj->ID;}, $entranceList);

                if (!empty($idList)) {
                    $floorList = $floorModel->orderBy([], true)->select(["ID"], true)->where(["PROPERTY_ENTRANCE" => $idList])->getElements();
                    $idList    = array_map(function($obj){return $obj->ID;}, $floorList);
                    if (!empty($idList)) {
                        $flatList = $flatModel->select(["PROPERTY_PLAN"], true)->where(["PROPERTY_FLOOR" => $idList])->getElements();
                    }
                }
            }
            $setSearchServiceFilter = true;
        }

        if (!empty($flatList)) {
            $filterArray["PROPERTY_PLAN"] = array_map(function ($obj) {return $obj->PROPERTY_PLAN_VALUE;}, $flatList);
        } elseif($setSearchServiceFilter) {
            $filterArray["PROPERTY_PLAN"] = 0;
        }

        return $filterArray;
    }
	
}








