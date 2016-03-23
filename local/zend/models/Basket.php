<?
/**
* Class Sibirix_Model_Basket
*/
class Sibirix_Model_Basket extends Sibirix_Model_Bitrix {

    /**
     * @var CSaleBasket
     */
    protected $_bxBasket;

    protected $_selectFields = [
        "ID",
        "NAME",
        "PRICE",
        "QUANTITY",
        "PRODUCT_ID",
        "DETAIL_PAGE_URL",
        "ORDER_ID",
		"DELAY"
    ];

    public function init($initParams = NULL) {
        CModule::IncludeModule("sale");
        CModule::IncludeModule("catalog");
        $this->_bxBasket = new CSaleBasket();
    }

    /**
     * Добавляет в корзину дизайн
     * @param $id
     * @return bool|int
     */
    public function addDesign($id, $quantity=1, $fields=[], $props=[]) {
		
        $designModel = new Sibirix_Model_Design();

        $design = $designModel->select(['CODE'], true)->getElement($id);
		
        $fields["DETAIL_PAGE_URL"] = EZendManager::url(['elementCode' => $design->CODE], 'design-detail');
	
        return Add2BasketByProductID($id, $quantity, $fields, $props);
    }
	
	 /**
     * Добавляет в корзину заказ
     * @param $id
     * @return bool|int
     */
	 
    public function addProject($id, $quantity=1, $fields=[], $props=[]) {
		
        $designModel = new Sibirix_Model_Project();

        $design = $this->select(['CODE'], true)->getElement($id);
	
        $fields["DETAIL_PAGE_URL"] = EZendManager::url(['elementCode' => $design->CODE], 'design-detail');
		
        return Add2BasketByProductID($id, $quantity, $fields, $props);
    }
	
	/**
     * Добавляет в корзину товар
     * @param $id
     * @return bool|int
     */
    public function addGoods($id, $quantity=1, $fields=[], $props=[]) {
	
        $goodsModel = new Sibirix_Model_Goods();

        $goods = $goodsModel->select(['CODE', 'IBLOCK_SECTION_ID'], true)->getElement($id);
		
        //$fields["DETAIL_PAGE_URL"] = '/'.$goods->IBLOCK_SECTION_ID .'/'.EZendManager::url(['elementCode' => $goods->CODE], 'design-detail');
		$url = '';
		//if(!empty($goods->CODE)){
		//	$url = '/shop/'.$goods->IBLOCK_SECTION_ID .'/'.$goods->CODE;
		//}
		//else{
			$url = '/shop/'.$goods->IBLOCK_SECTION_ID .'/'.$goods->ID;
		//}
		
		$fields["DETAIL_PAGE_URL"] = $url;
		
        return Add2BasketByProductID($id, $quantity, $fields, $props);
    }


    /**
     * Удаляет дизайн из корзины
     * @param $itemId
     * @return array|bool
     */
    public function deleteDesign($id) {
        $basketItem = $this->_bxBasket->GetByID($id);
        $fUserId = $this->_bxBasket->GetBasketUserID();
        if($basketItem["FUSER_ID"] != $fUserId) {
            return false;
        }
		
        $delResult = $this->_bxBasket->Delete($id);
        if ($delResult) {
            return $this->getBasketTotal();
        } else {
            return false;
        }
    }
	
    /**
     * Удаляет товар из корзины
     * @param $itemId
     * @return array|bool
     */
    public function deleteGoods($id) {
        $basketItem = $this->_bxBasket->GetByID($id);
        $fUserId = $this->_bxBasket->GetBasketUserID();
        if($basketItem["FUSER_ID"] != $fUserId) {
            return false;
        }
		
        $delResult = $this->_bxBasket->Delete($id);
        if ($delResult) {
            return $this->getBasketTotal();
        } else {
            return false;
        }
    }

    /**
     *
     */
    public function getBasketTotal() {
        //Получаем актуальную корзину
        $filter = ["DELAY" => "N", "FUSER_ID" => $this->_bxBasket->GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL"];
        $basketGetList  = $this->_bxBasket->GetList([], $filter, false, false, ['ID', 'PRICE', 'QUANTITY']);

        $basketItemList = array();
        while ($item = $basketGetList->Fetch()) {
            $basketItemList[] = $item;
        }
        $totalPrice    = 0;

        foreach ($basketItemList as $basketKey => $basketItem) {
            $totalPrice += ($basketItem["PRICE"] * $basketItem["QUANTITY"]);
        }

        $result = [
            "basketTotal" => [
                "totalPrice"    => $totalPrice,
                "totalCount"    => count($basketItemList)
            ]
        ];

        return $result;
    }

    /**
     * Возвращает количество позиций в корзине
     * @return bool
     */
    public static function getBasketCount() {
        CModule::IncludeModule("sale");
        $bxSaleBasket = new CSaleBasket();
        $cntBasketItems = $bxSaleBasket->GetList(array(), array(
			"DELAY" => "N",
            "FUSER_ID" => $bxSaleBasket->GetBasketUserID(),
            "LID" => SITE_ID,
            "ORDER_ID" => "NULL"
        ), array());
		
        return $cntBasketItems;
    }

    public function getBasket($delay="N", $orderId=NULL) {
		//Получаем актуальную корзину
        $filter = ["DELAY" => $delay, "FUSER_ID" => $this->_bxBasket->GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => $orderId];
        $basketGetList  = $this->_bxBasket->GetList([], $filter, false, false, $this->_selectFields);

        $basketItemList = array();
        while ($item = $basketGetList->Fetch()) {
            $basketItemList[] = $item;
        }

        if (empty($basketItemList)) {
            return false;
        }

        //Получаем картинки
        $productIds = array_map(function ($el) {return $el["PRODUCT_ID"];}, $basketItemList);
        $designModel = new Sibirix_Model_Design();
        $designList = $designModel->select(["ID", "DETAIL_PICTURE", "CREATED_BY", "PROPERTY_BUDGET"], true)->where(["ID" => $productIds])->getElements();
        $designs = [];
        foreach ($designList as $design) {
            $designs[$design->ID] = $design;
        }
       // $this->getImageData($designs, ['DETAIL_PICTURE']);
		
		$designModel = new Sibirix_Model_Goods();
		$designList = $designModel->select(["ID", "DETAIL_PICTURE", "CREATED_BY", "PROPERTY_PRICE"], true)->where(["ID" => $productIds])->getElements();
      
        foreach ($designList as $design) {
            $designs[$design->ID] = $design;
        }
        $this->getImageData($designs, ['DETAIL_PICTURE']);

        //дизайнеры
        $userModel = new Sibirix_Model_User();
        $designerIds = array_map(function($obj) {return $obj->CREATED_BY;}, $designs);
        $designerList = $userModel->select(["ID","CODE", "NAME", "LAST_NAME", "EMAIL"], true)->where(["ID" => $designerIds])->getElements();

        $designers = [];
        foreach ($designerList as $designer) {
            $designers[$designer->ID] = $designer;
        }

        //Добавляем в массив корзины новые поля
        $totalPrice = 0;

        foreach ($basketItemList as $basketKey => $basketItem) {
            $basketItemList[$basketKey]["IMAGE"] = $designs[$basketItem["PRODUCT_ID"]]->DETAIL_PICTURE;
			if(isset($designs[$basketItem["PRODUCT_ID"]]->PROPERTY_PRICE_VALUE)){
				$basketItemList[$basketKey]["PRICE"] = $basketItem['PRICE'];
			}
            else{
				$basketItemList[$basketKey]["PRICE"] = $designs[$basketItem["PRODUCT_ID"]]->PROPERTY_BUDGET_VALUE;
			}
            $basketItemList[$basketKey]["DESIGNER"] = $designers[$designs[$basketItem["PRODUCT_ID"]]->CREATED_BY];

            //Суммарные цифры
            $totalPrice += ($basketItem["PRICE"] * $basketItem["QUANTITY"]);
        }
		
        $result = [
            "basketItems" => $basketItemList,
            "basketTotal" => [
                "totalPrice" => $totalPrice,
                "totalCount" => count($basketItemList)
            ]
        ];

        return $result;
    }
	
	public function getBasketGoods($delay="N", $orderId=NULL) {
    //Получаем актуальную корзину
        $filter = ["DELAY" => $delay, "FUSER_ID" => $this->_bxBasket->GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => $orderId];
        $basketGetList  = $this->_bxBasket->GetList([], $filter, false, false, $this->_selectFields);

        $basketItemList = array();
        while ($item = $basketGetList->Fetch()) {
            $basketItemList[] = $item;
        }

        if (empty($basketItemList)) {
            return false;
        }
		
        //Получаем картинки
        $productIds = array_map(function ($el) {return $el["PRODUCT_ID"];}, $basketItemList);
        $designModel = new Sibirix_Model_Design();
        $designList = $designModel->select(["ID", "DETAIL_PICTURE", "CREATED_BY", "PROPERTY_BUDGET"], true)->where(["ID" => $productIds])->getElements();
        $designs = [];
        foreach ($designList as $design) {
            $designs[$design->ID] = $design;
        }
       // $this->getImageData($designs, ['DETAIL_PICTURE']);
		
		$designModel = new Sibirix_Model_Goods();
		$designList = $designModel->select(["ID", "IBLOCK_SECTION_ID", "CODE", "NAME", "PROPERTY_PRICE", "DETAIL_PAGE_URL", "IBLOCK_ID", "DETAIL_PICTURE", "CREATED_BY", "PROPERTY_PRICE"], true)->where(["ID" => $productIds])->getElements();
      
        foreach ($designList as $design) {
			$design->URL = '/shop/'.$design->IBLOCK_SECTION_ID .'/'.$design->ID;
            $designs[$design->ID] = $design;
        }
        $this->getImageData($designs, ['DETAIL_PICTURE']);
		
        //дизайнеры
        $userModel = new Sibirix_Model_User();
        $designerIds = array_map(function($obj) {return $obj->CREATED_BY;}, $designs);
        $designerList = $userModel->select(["ID", "NAME", "LAST_NAME", "EMAIL"], true)->where(["ID" => $designerIds])->getElements();

        $designers = [];
        foreach ($designerList as $designer) {
            $designers[$designer->ID] = $designer;
        }

        //Добавляем в массив корзины новые поля
        $totalPrice = 0;
        foreach ($basketItemList as $basketKey => $basketItem) {
            $basketItemList[$basketKey]["IMAGE"] = $designs[$basketItem["PRODUCT_ID"]]->DETAIL_PICTURE;
			if(isset($designs[$basketItem["PRODUCT_ID"]]->PROPERTY_PRICE_VALUE)){
				$basketItemList[$basketKey]["PRICE"] = $designs[$basketItem["PRODUCT_ID"]]->PROPERTY_PRICE_VALUE;
			}
            else{
				$basketItemList[$basketKey]["PRICE"] = $designs[$basketItem["PRODUCT_ID"]]->PROPERTY_BUDGET_VALUE;
			}
            $basketItemList[$basketKey]["DESIGNER"] = $designers[$designs[$basketItem["PRODUCT_ID"]]->CREATED_BY];

            //Суммарные цифры
            $totalPrice += ($basketItem["PRICE"] * $basketItem["QUANTITY"]);
        }
        $result = [
            "basketItems" => $designList,
            "basketTotal" => [
                "totalPrice" => $totalPrice,
                "totalCount" => count($basketItemList)
            ]
        ];

        return $result;
    }
	
	public function getBasketDesign($delay="N", $orderId=NULL) {
    //Получаем актуальную корзину
        $filter = ["DELAY" => $delay, "FUSER_ID" => $this->_bxBasket->GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => $orderId];
        $basketGetList  = $this->_bxBasket->GetList([], $filter, false, false, $this->_selectFields);

        $basketItemList = array();
        while ($item = $basketGetList->Fetch()) {
            $basketItemList[] = $item;
        }

        if (empty($basketItemList)) {
            return false;
        }
		
        //Получаем картинки
        $productIds = array_map(function ($el) {return $el["PRODUCT_ID"];}, $basketItemList);
        $designModel = new Sibirix_Model_Design();
        $designList = $designModel->select(["ID", "CODE", "NAME", "PROPERTY_PRICE", "DETAIL_PAGE_URL", "IBLOCK_ID", "DETAIL_PICTURE", "CREATED_BY", "PROPERTY_BUDGET"], true)->where(["ID" => $productIds])->getElements();
        $designs = [];
        foreach ($designList as $design) {
            $designs[$design->ID] = $design;
        }
        $this->getImageData($designs, ['DETAIL_PICTURE']);
				
        //дизайнеры
        $userModel = new Sibirix_Model_User();
        $designerIds = array_map(function($obj) {return $obj->CREATED_BY;}, $designs);
        $designerList = $userModel->select(["ID", "NAME", "LAST_NAME", "EMAIL"], true)->where(["ID" => $designerIds])->getElements();

        $designers = [];
        foreach ($designerList as $designer) {
            $designers[$designer->ID] = $designer;
        }

        //Добавляем в массив корзины новые поля
        $totalPrice = 0;
        foreach ($basketItemList as $basketKey => $basketItem) {
            $basketItemList[$basketKey]["IMAGE"] = $designs[$basketItem["PRODUCT_ID"]]->DETAIL_PICTURE;
			if(isset($designs[$basketItem["PRODUCT_ID"]]->PROPERTY_PRICE_VALUE)){
				$basketItemList[$basketKey]["PRICE"] = $designs[$basketItem["PRODUCT_ID"]]->PROPERTY_PRICE_VALUE;
			}
            else{
				$basketItemList[$basketKey]["PRICE"] = $designs[$basketItem["PRODUCT_ID"]]->PROPERTY_BUDGET_VALUE;
			}
            $basketItemList[$basketKey]["DESIGNER"] = $designers[$designs[$basketItem["PRODUCT_ID"]]->CREATED_BY];

            //Суммарные цифры
            $totalPrice += ($basketItem["PRICE"] * $basketItem["QUANTITY"]);
        }
        $result = [
            "basketItems" => $designList,
            "basketTotal" => [
                "totalPrice" => $totalPrice,
                "totalCount" => count($basketItemList)
            ]
        ];

        return $result;
    }

    public function getBasketProductsId() {
        $filter = ["DELAY" => "N", "FUSER_ID" => $this->_bxBasket->GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL"];
        $basketGetList  = $this->_bxBasket->GetList([], $filter, false, false, $this->_selectFields);

        $basketItemList = array();
        while ($item = $basketGetList->Fetch()) {
            $basketItemList[] = $item;
        }

        if (empty($basketItemList)) {
            return [];
        }

        $productIds = array_map(function ($el) {return $el["PRODUCT_ID"];}, $basketItemList);
        return $productIds;
    }
	
	public function getFavouriteProductsId() {
        $filter = ["DELAY" => "Y", "FUSER_ID" => $this->_bxBasket->GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL"];
        $basketGetList  = $this->_bxBasket->GetList([], $filter, false, false, $this->_selectFields);

        $basketItemList = array();
        while ($item = $basketGetList->Fetch()) {
            $basketItemList[] = $item;
        }

        if (empty($basketItemList)) {
            return [];
        }

        $productIds = array_map(function ($el) {return $el["PRODUCT_ID"];}, $basketItemList);
        return $productIds;
    }

    public function getItem($id) {
		
        $basketElement = $this->_bxBasket->GetByID($id);
        $item['ID'] = $basketElement['ID'];
        $item['NAME'] = $basketElement['NAME'];
        $item['DETAIL_PAGE_URL'] = $basketElement['DETAIL_PAGE_URL'];
        $item['PRICE'] = EHelper::price($basketElement['PRICE']);

        $modelDesign = new Sibirix_Model_Design();
        $design = $modelDesign->select(['DETAIL_PICTURE'], true)->getElement($basketElement['PRODUCT_ID']);
        $item['IMAGE_SRC'] = Resizer::resizeImage($design->DETAIL_PICTURE, 'BASKET_SIDEBAR');

        return $item;
    }
	
	public function getItemProject($id) {
		$projectModel = new Sibirix_Model_Project();
		
		$basketElement = $this->_bxBasket->GetByID($id);
		$PROJECT_ARR = $projectModel->select(['CODE','PROPERTY_ID_OPTION_PLAN'], true)->getElement($basketElement['PRODUCT_ID']);

        $basketElement = $this->_bxBasket->GetByID($id);
        $item['ID'] = $basketElement['ID'];
        $item['NAME'] = $basketElement['NAME'];
        $item['DETAIL_PAGE_URL'] = $basketElement['DETAIL_PAGE_URL'];
        $item['PRICE'] = EHelper::price($basketElement['PRICE']);

        $planoptionModel = new Sibirix_Model_PlanOption();
        $id_flat_option = $PROJECT_ARR->PROPERTY_ID_OPTION_PLAN_VALUE;
		$PLAN_OPTION = $planoptionModel->select(['CODE','PROPERTY_IMAGES'], true)->getElement($id_flat_option);
        $item['IMAGE_SRC'] = Resizer::resizeImage($PLAN_OPTION->PROPERTY_IMAGES, 'BASKET_SIDEBAR');

        return $item;
    }
	
	
	public function getItemShop($id) {
        $basketElement = $this->_bxBasket->GetByID($id);
        $item['ID'] = $basketElement['ID'];
        $item['NAME'] = $basketElement['NAME'];
        $item['DETAIL_PAGE_URL'] = $basketElement['DETAIL_PAGE_URL'];
        $item['PRICE'] = EHelper::price($basketElement['PRICE']);
        $modelDesign = new Sibirix_Model_Goods();
        $design = $modelDesign->select(['DETAIL_PICTURE'], true)->getElement($basketElement['PRODUCT_ID']);
        $item['IMAGE_SRC'] = Resizer::resizeImage($design->DETAIL_PICTURE, 'BASKET_SIDEBAR');

        return $item;
    }

}
