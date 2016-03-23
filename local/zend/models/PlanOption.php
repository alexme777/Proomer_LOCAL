<?

/**
 * Class Sibirix_Model_Plan
 *
 */
class Sibirix_Model_PlanOption extends Sibirix_Model_Bitrix {

    protected $_iblockId = IB_PLAN_OPTION;

    protected $_pageSize;

    protected $_selectListFields = [
        'ID',
        'CODE',
        'NAME',
		'PROPERTY_AREA',
		'PROPERTY_ROOM',
		'PROPERTY_FREE_PLAN',
		'PROPERTY_IMAGES',
		'PROPERTY_STATUS',
		'PROPERTY_COORDS',
		'PROPERTY_ROOM_PLAN',
		'PREVIEW_PICTURE',
		'DETAIL_PICTURE'
    ];
	
	protected $_selectListFields2 = [
        'ID',
        'CODE',
        'NAME',
		'PROPERTY_AREA',
		'PROPERTY_ROOM',
		'PROPERTY_FREE_PLAN',
		'PROPERTY_IMAGES',
		'PROPERTY_STATUS',
		'PROPERTY_OPTIONS',
		'PROPERTY_COORDS',
		'PREVIEW_PICTURE',
		'DETAIL_PICTURE'
    ];
	
	public function getPlanList($filter, $sort, $page, $profile=false, $limit = 11) {
        $planList = $this->select($this->_selectListFields, true)->where($filter)->orderBy($sort, true)->getPageItem($page, $limit);
      //  $this->_getDesignInfo($planList->items);

        return $planList;
    }
	
	public function getPlanOptionList($filter, $sort, $page, $profile=false, $limit = 5) {
        $planList = $this->select($this->_selectListFields, true)->orderBy($sort, true)->where($filter)->getPageItem($page, $limit);
		//  $this->_getDesignInfo($planList->items);

        return $planList;
    }
	
	public function getPageTitle($filterParams) {
        $pageTitle = "Подберите дизайн мечты для вашей квартиры";
        /** @var $houseData Sibirix_Model_House_Row **/

        if (!empty($filterParams["house"])) {
            $complexModel  = new Sibirix_Model_Complex();
            $houseModel    = new Sibirix_Model_House();

            $houseData    = $houseModel->select(["PROPERTY_COMPLEX"])->getElement($filterParams["house"]);
            $complexData  = $complexModel->getElement($houseData->PROPERTY_COMPLEX_VALUE);

            $pageTitle = $complexData->NAME.", ".$houseData->getAddress();
        } elseif(!empty($filterParams["complexId"])) {
            $complexModel  = new Sibirix_Model_Complex();
            $complexData  = $complexModel->getElement($filterParams["complexId"]);

            $pageTitle = $complexData->NAME;
        }

        return $pageTitle;
    }
	/**
     * Формирование фильтрующего массива
     * @param $getParams
     * @return array
     */
    public function prepareFilter($getParams) {
        if (empty($getParams)) return array();

        $filterKeys = array(
            "area"
        );

        /*foreach ($getParams as $paramKey => $param) {
            if (!in_array($paramKey, $filterKeys)) {
                unset($getParams[$paramKey]);
            }
        }

        if (empty($getParams)) return array();*/
		$filterArray = array();
        //По площади
        if (!empty($getParams["areaMin"])) {  
            $filterArray[">=PROPERTY_AREA"] = $getParams["areaMin"];
		}
		if (!empty($getParams["areaMax"])) {  
            $filterArray["<=PROPERTY_AREA"] = $getParams["areaMax"];
		}
		
		if(!empty($getParams["planFree"]) && $getParams["planFree"] == true){
			$filterArray["PROPERTY_FREE_PLAN"] = array(53);
		}
		
		if(!empty($getParams['countRoom'])){
			$countRoom = json_decode($getParams['countRoom']);
			if(gettype($countRoom) == 'array'){
				$filterArray["PROPERTY_ROOM"] = $countRoom;
			};
		}
		if(!empty($getParams['address'])){
			$address = $getParams['address'];
			$filterArray["PROPERTY_ADDRESS"] = $address;
		}


        return $filterArray;
    }
	
	protected function _getPlanInfo($designs) {
        $this->getImageData($designs);
        $designPriceList = $this->getPrice($designs);

        //дизайнеры
        $designerList = [];
        $designerIds = array_map(function($obj) {return $obj->CREATED_BY;}, $designs);
        $designerGetList = (new Sibirix_Model_User())
            ->where(['ID' => $designerIds])
            ->getElements();
        $this->getImageData($designerGetList, 'PERSONAL_PHOTO');

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
        foreach ($designerGetList as $ind => $designer) {
            $designerList[$designer->ID] = $designer;
        }

        foreach ($designs as $key => $designItem) {
            $designItem->PRICES = $designPriceList[$designItem->ID];
            $designItem->DESIGNER = $designerList[$designItem->CREATED_BY];
            $designItem->IS_LIKED = (!empty($list)) ? in_array($designItem->ID, $list) : false;
            $designItem->IS_IN_BASKET = (!empty($basketItems))? in_array($designItem->ID, $basketItems) : false;
			$designItem->IS_IN_FAVOURITE = (!empty($favouriteItems))? in_array($designItem->ID, $favouriteItems) : false;
        }
		
    }
	
	    /**
     * Добавляет файл к множественному свойству
     * возвращает список новых файлов
     * @param $id
     * @param $imageFile
     * @return array
     * @throws Exception
     */
    public function addPlan($fields) {
        $bxElement           = new CIBlockElement();
        $fields["IBLOCK_ID"] = IB_PLAN;

       // if ($fields["ID"] > 0) {
            $designId    = $fields["ID"];
            $designProps = $fields["PROPERTY_VALUES"];
           // unset($fields["ID"], $fields["PROPERTY_VALUES"]);

            if (!empty($fields["NAME"])) {
                $fields["CODE"] = CUtil::translit($fields["NAME"], "ru");
            }
//echo print_r($fields);
//exit;
            $result = $bxElement->Add($fields);
			return $result;
           /* if ($updateResult && !empty($designProps)) {
                $bxElement->SetPropertyValuesEx($designId, IB_PLAN, $designProps);

                //оповещение администратору
               /* if (array_key_exists('STATUS', $designProps) && $designProps['STATUS'] == DESIGN_STATUS_MODERATION) {
                    $notificationModel = new Sibirix_Model_Notification();

                    $design = $this->getElement($designId);
                    $designer = Sibirix_Model_User::getCurrent();
                    $notificationModel->statusModeraion($design, $designer);
                }*/
           // }

       /*     $result = $updateResult;
        } else {
            $patternEnumId = EnumUtils::getListIdByXmlId($this->_iblockId, "STATUS", "draft");
            if (empty($fields["NAME"])) {
                $fields["NAME"] = PATTERN_DESIGN_NAME . time();
                $fields["CODE"] = CUtil::translit($fields["NAME"], "ru");
                $fields["PROPERTY_VALUES"]["STATUS"] = $patternEnumId;
            }
            $newId = $bxElement->Add($fields);

            $result = $newId;
        }

        if (empty($fields["PRICE_VALUE"])) {
            return $result;
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
*/
       // return $result;
    }
}
