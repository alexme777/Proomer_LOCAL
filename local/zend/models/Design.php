<?
/**
 * Class Sibirix_Model_Design
 * @method Sibirix_Model_Design_Row getElement($id=false)
 */
class Sibirix_Model_Design extends Sibirix_Model_Bitrix {

    protected $_iblockId = IB_DESIGN;

    protected $_instanceClass = 'Sibirix_Model_Design_Row';

    protected $_selectFields = array(
        'ID',
        'NAME',
        'CODE',
        'DETAIL_PICTURE',
        'PREVIEW_TEXT',
        'DETAIL_TEXT',
        'CREATED_BY',
        'PROPERTY_STYLE',
        'PROPERTY_PRIMARY_COLOR',
        'PROPERTY_BUDGET',
        'PROPERTY_AREA',
        'PROPERTY_PLAN_FLAT',
        'PROPERTY_ESTIMATE',
        'PROPERTY_PLAN',
        'PROPERTY_PLAN.NAME',
        'PROPERTY_STATUS',
        'PROPERTY_DOCUMENTS',
        'PROPERTY_LIKE_CNT',
		'PROPERTY_TIME'

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
        'PROPERTY_LIKE_CNT',
		'PROPERTY_TIME'
    ];

    public function init($initParams = NULL) {
        $this->_pageSize = Sibirix_Model_ViewCounter::getViewCounter();
        CModule::IncludeModule("catalog");
    }

    /**
     * Перезаписывание актуального количества элементов на странице
     */
    public function reinitViewCounter() {
        $this->_pageSize = Sibirix_Model_ViewCounter::getViewCounter();
    }

    /**
     * Перезаписывание актуального количества элементов на странице
     */
    public function reinitProfileViewCounter() {
        $this->_pageSize = Sibirix_Model_ViewCounter::getViewCounter(true);
    }

    /**
     * Перезаписывание количества элементов на странице для страницы дизайнера
     */
    public function reinitPageSize($size) {
        $this->_pageSize = $size;
    }

    /**
     * Получает комплексы по дизайну
     * @param $designId
     * @return array|bool
     */
    public function getComplexListByDesign($designId) {
        $dbPropPlan = CIBlockElement::GetProperty($this->_iblockId, $designId, array(), Array("CODE"=>"PLAN"));
        if($propPlan = $dbPropPlan->Fetch()) {
            $planId = $propPlan["VALUE"];
        } else {
            return false;
        }

        $complexModel  = new Sibirix_Model_Complex();
        $houseModel    = new Sibirix_Model_House();
        $entranceModel = new Sibirix_Model_Entrance();
        $floorModel    = new Sibirix_Model_Floor();
        $flatModel     = new Sibirix_Model_Flat();

        $floorId    = $flatModel->orderBy([], true)->select(['PROPERTY_FLOOR'], true)->where(['PROPERTY_PLAN' => $planId])->asSubQuery();
        $entranceId = $floorModel->orderBy([], true)->select(['PROPERTY_ENTRANCE'], true)->where(['ID' => $floorId])->asSubQuery();
        $houseId    = $entranceModel->orderBy([], true)->select(['PROPERTY_HOUSE'], true)->where(['ID' => $entranceId])->asSubQuery();
        $complexId  = $houseModel->orderBy([], true)->select(['PROPERTY_COMPLEX'], true)->where(['ID' => $houseId])->asSubQuery();

        $complex = $complexModel->where(["ID" => $complexId])->getElements();

        return $complex;
    }

    /**
     * Получает прайс-лист дизайнов
     * @param $design
     * @return array
     */
    public function getPrice($design) {
        if (empty($design)) return array();
        $priceList = array();

        if (!is_array($design)) {
            $design = [$design];
        }

        $designIds = array();
        foreach ($design as $designItem) {
            if (!is_object($designItem)) continue;
            $designIds[] = $designItem->ID;
        }

        if (empty($designIds)) {
            $designIds = $design;
        }

        $bxPrice = new CPrice();
        $getList = $bxPrice->GetList(array(),array("PRODUCT_ID" => $designIds));

        while ($price = $getList->Fetch()) {
            $priceList[$price["PRODUCT_ID"]] = $price;
        }

        return $priceList;
    }

    /**
     * Список дизайнов
     * @param $filter
     * @param $sort
     * @param $page
     * @return object
     */
    public function getDesignList($filter, $sort, $page, $profile=false) {
        if (!$profile) {
            $filter['PROPERTY_STATUS'] = DESIGN_STATUS_PUBLISHED;
        }
        $designList = $this->select($this->_selectListFields, true)->orderBy($sort, true)->where($filter)->getPage($page);
        $this->_getDesignInfo($designList->items);

        return $designList;
    }

    /**
     * Получает максимальную и минимальную цену дизайна
     * @return array
     */
    public function getExtremePrice($created_by = false) {
        $filter = ($created_by) ? ['CREATED_BY' => $created_by, '!PROPERTY_STATUS' => DESIGN_STATUS_DELETED] : ['PROPERTY_STATUS' => DESIGN_STATUS_PUBLISHED];

        $minAvgPrice = $this->select(["ID"], true)->where($filter)->orderBy(["catalog_PRICE_" . BASE_PRICE => "ASC"], true)->getElement();
        $maxAvgPrice = $this->select(["ID"], true)->where($filter)->orderBy(["catalog_PRICE_" . BASE_PRICE => "DESC"], true)->getElement();

        $basePriceKey = "CATALOG_PRICE_" . BASE_PRICE;
		
        $result = array(
            "from" => round($minAvgPrice->$basePriceKey),
            "to"   => round($maxAvgPrice->$basePriceKey)
        );
	
        return $result;
    }

    /**
     * Получает максимальную и минимальную цену бюджета дизайна
     * @return array
     */
    public function getExtremeBudget() {
        $minAvgPrice = $this->select(["PROPERTY_BUDGET"], true)->orderBy(["PROPERTY_BUDGET" => "ASC"], true)->getElement();
        $maxAvgPrice = $this->select(["PROPERTY_BUDGET"], true)->orderBy(["PROPERTY_BUDGET" => "DESC"], true)->getElement();

        $result = array(
            "from" => (empty($minAvgPrice->PROPERTY_BUDGET_VALUE) ? 0 : $minAvgPrice->PROPERTY_BUDGET_VALUE),
            "to"   => $maxAvgPrice->PROPERTY_BUDGET_VALUE
        );

        return $result;
    }

    protected function _getDesignInfo($designs) {
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

    public function getSliderItems($complexId, $elementCnt) {
        $complex = (new Sibirix_Model_Complex())->getElement($complexId);

        $cacheTime = 60 * 60 * 24 * 30;
        $obCache   = new CPHPCache();
        if ($obCache->InitCache($cacheTime, 'design-slider'.$complexId, '/design-slider'.$complexId.'-rand/')) {
            $vars = $obCache->GetVars();
            $designs = $vars['design-slider'];
        } else {
            $designs = $complex->getDesignList([], $this->_selectListFields);
            $this->_getDesignInfo($designs);

            if ($obCache->StartDataCache()) {
                $obCache->EndDataCache(array('design-slider' => $designs));
            }
        }

        if (empty($designs)) {
            return array();
        }

        shuffle($designs);

        if ($elementCnt > 0) {
            $resultData = array_slice($designs, 0, $elementCnt);
        } else {
            $resultData = $designs;
        }
        return $resultData;
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
            "budget",
            "primaryColor",
            "style",
            "complexId",
            "house",
            "entrance",
            "floor",
            "flat",
            "status"
        );

        foreach ($getParams as $paramKey => $param) {
            if (!in_array($paramKey, $filterKeys)) {
                unset($getParams[$paramKey]);
            }
        }

        if (empty($getParams)) return array();

        $filterArray = array();

        //По цене дизайна
        if (!empty($getParams["price"])) {
            $avgPriceArray = explode(":", $getParams["price"]);
            $filterArray[">=CATALOG_PRICE_" . BASE_PRICE] = $avgPriceArray[0];
            $filterArray["<=CATALOG_PRICE_" . BASE_PRICE] = $avgPriceArray[1];

        }

        //По средней бюджета
        if (!empty($getParams["budget"])) {
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
        }

        //По цвету
        if (!empty($getParams["primaryColor"])) {
            $filterArray["=PROPERTY_PRIMARY_COLOR"] = $getParams["primaryColor"];
        }

        //По стилю
        if (!empty($getParams["style"])) {
            $filterArray["=PROPERTY_STYLE"] = $getParams["style"];
        }

        //статус
        if (!empty($getParams["status"])) {
            $status = $getParams["status"];
            $key = array_search(0, $status);

            if ($key !== false) {
                $filterArray["CATALOG_PRICE_" . BASE_PRICE] = 0;
                unset($status[$key]);
            }

            if (!empty($status)) {
                $filterArray["=PROPERTY_STATUS"] = $getParams["status"];
            }
        }

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



    /**
     * Возвращает заголовок списка дизайнов
     * @param $filterParams
     * @return string
     */
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

    public function getSimilarDesigns($design, $elementCnt) {
        $filter = [
            "!ID" => $design->ID,
            "PROPERTY_PLAN" => $design->PROPERTY_PLAN,
            [
                "LOGIC" => "OR",
                ["PROPERTY_STYLE" => $design->PROPERTY_STYLE],
                ["PROPERTY_PRIMARY_COLOR" => $design->PROPERTY_PRIMARY_COLOR],
            ],
        ];

        $cacheTime = 60 * 60 * 24 * 30;
        $obCache   = new CPHPCache();
        if ($obCache->InitCache($cacheTime, 'similar-design-slider'.$design->ID, '/similar-design-slider'.$design->ID.'-rand/')) {
            $vars = $obCache->GetVars();
            $similarDesigns = $vars['similar-designs'];
        } else {
            $similarDesigns = $this
                ->select($this->_selectListFields, true)
                ->where($filter)
                ->getElements();

            $this->_getDesignInfo($similarDesigns);

            if ($obCache->StartDataCache()) {
                $obCache->EndDataCache(array('similar-designs' => $similarDesigns));
            }
        }

        if (empty($similarDesigns)) {
            return array();
        }

        shuffle($similarDesigns);

        if ($elementCnt > 0) {
            $resultData = array_slice($similarDesigns, 0, $elementCnt);
        } else {
            $resultData = $similarDesigns;
        }

        return $resultData;
    }

    /**
     * Добавление/изменение дизайна
     * @param $fields
     * @return bool|int
     */
    public function editDesign($fields) {
        $bxElement           = new CIBlockElement();
        $fields["IBLOCK_ID"] = IB_DESIGN;

        if ($fields["ID"] > 0) {
            $designId    = $fields["ID"];
            $designProps = $fields["PROPERTY_VALUES"];
            unset($fields["ID"], $fields["PROPERTY_VALUES"]);

            if (!empty($fields["NAME"])) {
                $fields["CODE"] = CUtil::translit($fields["NAME"], "ru");
            }

            $updateResult = $bxElement->Update($designId, $fields);

            if ($updateResult && !empty($designProps)) {
                $bxElement->SetPropertyValuesEx($designId, IB_DESIGN, $designProps);

                //оповещение администратору
                if (array_key_exists('STATUS', $designProps) && $designProps['STATUS'] == DESIGN_STATUS_MODERATION) {
                    $notificationModel = new Sibirix_Model_Notification();

                    $design = $this->getElement($designId);
                    $designer = Sibirix_Model_User::getCurrent();
                    $notificationModel->statusModeraion($design, $designer);
                }
            }

            $result = $updateResult;
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

        return $result;
    }

    /**
     * Проверяет есть ли у пользователя доступ к дизайну
     * @param $designId
     * @param int $createdBy
     * @return bool
     */
    public function checkDesignAccess($designId, $createdBy = 0) {
        if (!($designId > 0)) {
            return false;
        }
        if (!Sibirix_Model_User::isAuthorized()) {
            return false;
        }

        if ($createdBy == 0) {
            $designData = $this->getElement($designId);
            $createdBy  = $designData->CREATED_BY;
        }

        if ($createdBy != Sibirix_Model_User::getId()) {
            return false;
        }

        return true;
    }

    public function changingStatus($fields){
        if ($fields["IBLOCK_ID"] != IB_DESIGN) {
            return $fields;
        }

        if (is_array($fields['PROPERTY_VALUES'][PROPERTY_STATUS_ID][0])) {
            $statusValue = $fields['PROPERTY_VALUES'][PROPERTY_STATUS_ID][0]['VALUE'];
        } else {
            $statusValue = $fields['PROPERTY_VALUES'][PROPERTY_STATUS_ID][0];
        }
        $designModel = new Sibirix_Model_Design();
        $design = $designModel->select(['PROPERTY_COMMENT'])->where(["ID" => $fields["ID"]])->getElement();
        if (!$design) {
            return $fields;
        } else {
            $statusValueOld = $design->PROPERTY_STATUS_ENUM_ID;
        }

        if ($statusValue == $statusValueOld) {
            return $fields;
        } else {
            $notificationModel = new Sibirix_Model_Notification();
            $userModel = new Sibirix_Model_User();
            $designer = $userModel->where(['ID' => $design->CREATED_BY])->getElement();

            if ($statusValue == DESIGN_STATUS_MODERATION) { //отправлен на модерацию
                $notificationModel->statusModeraion($design, $designer);
            } elseif ($statusValue == DESIGN_STATUS_ERROR) { //на доработку
                if (isset($fields['PROPERTY_VALUES'][PROPERTY_COMMENT]['n0'])) {
                    $comment = $fields['PROPERTY_VALUES'][PROPERTY_COMMENT]['n0']['VALUE']['TEXT'];
                } else {
                    $comment = $fields['PROPERTY_VALUES'][PROPERTY_COMMENT][$design->ID . ':' . PROPERTY_COMMENT]['VALUE']['TEXT'];
                }
                $notificationModel->statusError($design, $designer, $comment);
            } elseif ($statusValue == DESIGN_STATUS_PUBLISHED) { //опубликован
                $notificationModel->statusPublished($design, $designer);
            }
        }

        return $fields;
    }

    /**
     * Возвращает план квартиры
     * @param $designId
     * @return array|bool
     */
    public function getPlanImage($designId) {
        if (!($designId) > 0) return false;
        $bxElement = new CIBlockElement();
        $dbProps = $bxElement->GetProperty($this->_iblockId, $designId, array(), array("CODE" => "PLAN_FLAT"));
        $values = [];

        while ($image = $dbProps->Fetch()) {
            $values[] = $image["VALUE"];
        }

        return $values;
    }

    /**
     * Кешируем лайки дизайна, для сортировки по популярности
     * @param $id
     */
    public function cacheLikes($id) {
        $hh = Highload::instance(HL_LIKES)->cache(0);
        $list = $hh->where(['UF_DESIGN' => $id])->fetch();
        $count = count($list);

        CIBlockElement::SetPropertyValuesEx($id, IB_DESIGN, array(
            "LIKE_CNT" => $count
        ));

        return $count;
    }

    /**
     * Добавляет файл к множественному свойству
     * возвращает список новых файлов
     * @param $id
     * @param $imageFile
     * @return array
     * @throws Exception
     */
    public function addPlan($id, $imageFile) {
        $imageExist = $this->select(["PROPERTY_PLAN_FLAT"], true)->getElement($id);
        $alreadyImages = array();
				
        foreach ($imageExist->PROPERTY_PLAN_FLAT_VALUE as $key => $image) {
            $alreadyImages[$imageExist->PROPERTY_PLAN_FLAT_PROPERTY_VALUE_ID[$key]] = CIBlock::makeFilePropArray($image);
        }
        $fields["ID"] = $id;
        $fields["PROPERTY_VALUES"] = array(
            "PLAN_FLAT" => $alreadyImages + array("n0" => array("VALUE" => $imageFile))
        );
		
        $element = new CIBlockElement();

        $element->SetPropertyValuesEx($id, IB_DESIGN, $fields["PROPERTY_VALUES"]);

        $newImage = $this->select(["PROPERTY_PLAN_FLAT"], true)->getElement($id);

        $resultImages = array();
        foreach ($newImage->PROPERTY_PLAN_FLAT_VALUE as $key => $imageId) {
            $resultImages[] = array(
                "valueId" => $newImage->PROPERTY_PLAN_FLAT_PROPERTY_VALUE_ID[$key],
                "imgSrc" => Resizer::resizeImage($imageId, "DROPZONE_ROOMS_PHOTO")
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
        $element->SetPropertyValueCode($designId, "PLAN_FLAT", Array($imageItemId => Array("VALUE" => $arFile)));

        $newImage = $this->select(["PROPERTY_PLAN_FLAT"], true)->getElement($designId);
        $resultImages = array();
        foreach ($newImage->PROPERTY_PLAN_FLAT_VALUE as $key => $imageId) {
            $resultImages[] = array(
                "valueId" => $newImage->PROPERTY_PLAN_FLAT_PROPERTY_VALUE_ID[$key],
                "imgSrc" => Resizer::resizeImage($imageId, "DROPZONE_ROOMS_PHOTO")
            );
        }

        return $resultImages;
    }
}
