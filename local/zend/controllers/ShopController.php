<?php

class ShopController extends Sibirix_Controller {
	
    /**
     * @var $user Sibirix_Model_User_Row
     */
	protected $_model;
	 
    protected $user;
		
    public function init() {
        /**
         * @var $ajaxContext Sibirix_Controller_Action_Helper_SibirixAjaxContext
         */
		 
		CModule::IncludeModule("sale");
		CModule::IncludeModule("catalog");
		
        $ajaxContext = $this->_helper->getHelper('SibirixAjaxContext');
        $ajaxContext->addActionContext('shop-detail', 'html')
            ->initContext();
			
		$this->_model = new Sibirix_Model_Goods();

    }

    public function indexAction() {
		//ini_set('error_reporting', E_ALL);
		//ini_set('display_errors', 1);
		//ini_set('display_startup_errors', 1);
			
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'shop-detail');
        Zend_Registry::get('BX_APPLICATION')->SetTitle('Маркетплейс');
		$APP = Zend_Registry::get('BX_APPLICATION');

		if ($this->getParam("viewCounter") > 0) {
			Sibirix_Model_ViewCounter::setViewCounter($this->getParam("viewCounter"));
			$this->_model->reinitViewCounter();
		}
		else{
			Sibirix_Model_ViewCounter::setViewCounter(20);
			$this->_model->reinitViewCounter();		
		}
		
		$sortData = $this->getParam("sort");
		
		if (!empty($sortData)) {
			if (!empty($sortData["popular"])) {
				$catalogSort["PROPERTY_LIKE_CNT"] = $sortData["popular"];
			}
			if (!empty($sortData["date"])) {
				$catalogSort["DATE_CREATE"] = $sortData["date"];
			}
			if (!empty($sortData["price"])) {
				$catalogSort["catalog_PRICE_" . BASE_PRICE] = $sortData["price"];
			}
			if (!empty($sortData["budget"])) {
				$catalogSort["PROPERTY_BUDGET"] = $sortData["budget"];
			}
		}
			
		$catalogSort["SHOW_COUNTER"] = "DESC";
		$limit = 10;
		$filter = new Sibirix_Form_FilterDesign();
		$filterParams = $this->getAllParams();
		$categoryId = $this->getParam("categoryId");
		$filter->populate($filterParams);
		$validFilterValues = $filter->getValues();
		$result = $this->_model->getProductList($catalogFilter, $catalogSort);
		//$result->items = $this->_model->getImgItems($result->items);
	
		$discount = 0;
		for($i = 0; $i < count($result->items); $i++){
			if(CCatalogSKU::IsExistOffers($result->items[$i]->ID, IB_GOODS)){
				$result->items[$i]->OFFERS = 1;
			}
			else{$result->items[$i]->OFFERS = 0;};
			if(isset($result->items[$i]->DISCOUNT)){
				$discount+=1;
			}
		}
		
		//получим просмотренные товары
		$VISITOR_ITEMS = $APP->get_cookie("VISITOR_ITEMS");
		$view = 0;
		//если куку
		if($VISITOR_ITEMS){
			//распакуем массив
			$cookieitems = unserialize($VISITOR_ITEMS);
			//пройдемся по массиву, чтобы проверить есть там товар или нет
			for($i = 0; $i < count($cookieitems); $i++){
				for($n = 0; $n < count($result->items); $n++){
					if($result->items[$n]->ID == $cookieitems[$i]){
						$result->items[$n]->VIEW = 1;
						$view+=1;
					};
				};
			};
		}

		$categories = $this->_model->getCat();
		
		$links = array();
		$tree = array();
		$childs = array();

		foreach($categories as $item){
			if($item->IBLOCK_SECTION_ID){
				$childs[$item->IBLOCK_SECTION_ID][] = $item;
				foreach($categories as $item){ 
					if(isset($childs[$item->ID]))
					$item->CHILD = $childs[$item->ID];
					$tree = $childs[$item->ID];
				}
			}
		}

		//$this->view->paginator = EHelper::getPaginator($result->pageData->totalItemsCount, $result->pageData->current, $result->pageData->size);
		$this->view->assign([
			"pageTitle" => 'Маркетплейс',
			"filter"    => $filter,
			"view" => $view,
			"discount" => $discount,
			"itemList"  => $result->items,
			//'paginator' => EHelper::getPaginator($result->pageData->totalItemsCount, $result->pageData->current, $result->pageData->size),
			"categoryId" => $categoryId,
			"categories" => $tree
		]);	
    }
	
	public function shopDetailAction() {
		
		//ini_set('error_reporting', E_ALL);
		//ini_set('display_errors', 1);
		//ini_set('display_startup_errors', 1);
		
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'shop-detail');
        Zend_Registry::get('BX_APPLICATION')->SetTitle('Магазин');
		
		if ($this->getParam("viewCounter") > 0) {
			Sibirix_Model_ViewCounter::setViewCounter($this->getParam("viewCounter"));
			$this->_model->reinitViewCounter();
		}
		else{
			Sibirix_Model_ViewCounter::setViewCounter(20);
			$this->_model->reinitViewCounter();
		}
			
		$sortData = $this->getParam("sort");
		
		if (!empty($sortData)) {
			if (!empty($sortData["popular"])) {
				$catalogSort["PROPERTY_LIKE_CNT"] = $sortData["popular"];
			}
			if (!empty($sortData["date"])) {
				$catalogSort["DATE_CREATE"] = $sortData["date"];
			}
			if (!empty($sortData["price"])) {
				$catalogSort["PROPERTY_PRICE"] = $sortData["price"];
			}	
		}
			
		$catalogSort["SORT"] = "ASC";
		$filter = new Sibirix_Form_FilterDesign();
		$filterParams = $this->getAllParams();
		$categoryId = $this->getParam("categoryId");
		$validFilterValues['categoryId'] = $categoryId;
		if ($filterParams["priceMin"] === null || $filterParams["priceMax"] === null) {
		  //  $filterParams["price"] = $this->_model->getExtremePrice();
		} else {
			$filterParams["price"] = array(
				$filterParams["priceMin"],
				$filterParams["priceMax"]
			);
		}

		$filter->populate($filterParams);
		$validFilterValues = $filter->getValues();
		$catalogFilter = $this->_model->prepareFilter($validFilterValues);
		
		//$pageTitle     = $this->_model->getPageTitle($validFilterValues);
		$result = $this->_model->getShopList($catalogFilter, $catalogSort, $this->getParam("page", 1));
		//$result->items = $this->_model->getImgItems($result->items);
				
		$this->view->assign([
			"pageTitle" => 'Магазин',
			"filter"    => $filter,
			"itemList"  => $result->items,
			'paginator' => EHelper::getPaginator($result->pageData->totalItemsCount, $result->pageData->current, $result->pageData->size),
		]);
	
    }
	
	public function categoryAction() {
		//ini_set('error_reporting', E_ALL);
		//ini_set('display_errors', 1);
		//ini_set('display_startup_errors', 1);
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'shop-detail');
		$APP = Zend_Registry::get('BX_APPLICATION');
		if ($this->getParam("viewCounter") > 0) {
			Sibirix_Model_ViewCounter::setViewCounter($this->getParam("viewCounter"));
			$this->_model->reinitViewCounter();
		}
		else{
			Sibirix_Model_ViewCounter::setViewCounter(20);
			$this->_model->reinitViewCounter();
		}
			
		$sortData = $this->getParam("sort");
		
		if (!empty($sortData)) {
			if (!empty($sortData["popular"])) {
				$catalogSort["PROPERTY_LIKE_CNT"] = $sortData["popular"];
			}
			if (!empty($sortData["date"])) {
				$catalogSort["DATE_CREATE"] = $sortData["date"];
			}
			if (!empty($sortData["price"])) {
				$catalogSort["catalog_PRICE_" . BASE_PRICE] = $sortData["price"];
			}
			if (!empty($sortData["budget"])) {
				$catalogSort["PROPERTY_BUDGET"] = $sortData["budget"];
			}
			
		}
			
		$catalogSort["SORT"] = "ASC";
		$filter = new Sibirix_Form_FilterDesign();
		$filterParams = $this->getAllParams();
		$categoryId = $this->getParam("categoryId");

		$filter->populate($filterParams);
		$validFilterValues = $filter->getValues();
		$validFilterValues['categoryId'] = $categoryId;
		$catalogFilter = $this->_model->prepareFilter($validFilterValues);
		$result = $this->_model->getShopList($catalogFilter, $catalogSort, $this->getParam("page", 1));
		//$result->items = $this->_model->getImgItems($result->items);
			
		$categories = $this->_model->getCat();

		$links = array();
		$tree = array();
		$childs = array();
		$current_cat = '';
	
		foreach($categories as $item){
			if($item->ID == $categoryId){
				$current_cat = $item->NAME;
			}
			if($item->IBLOCK_SECTION_ID){
				$childs[$item->IBLOCK_SECTION_ID][] = $item;
				foreach($categories as $item){ 
					if(isset($childs[$item->ID]))
					$item->CHILD = $childs[$item->ID];
					$tree = $childs[$item->ID];
				}
			}
			else{
				
			};
		}
		Zend_Registry::get('BX_APPLICATION')->SetTitle($current_cat);
		$this->view->pictures = $pictures;
		$this->view->paginator = EHelper::getPaginator($result->pageData->totalItemsCount, $result->pageData->current, $result->pageData->size);
		
		 /*=============================================================================
		/		Получим просмотренные товары					  					  /
	   *============================================================================*/
		$VISITOR_ITEMS = $APP->get_cookie("VISITOR_ITEMS");
		$view = 0;
		//если куку
		if($VISITOR_ITEMS){
			//распакуем массив
			$cookieitems = unserialize($VISITOR_ITEMS);
			//пройдемся по массиву, чтобы проверить есть там товар или нет
			for($i = 0; $i < count($cookieitems); $i++){
				for($n = 0; $n < count($result->items); $n++){
					if($result->items[$n]->ID == $cookieitems[$i]){
						$result->items[$n]->VIEW = 1;
						$view+=1;
					};
				};
			};
		}
		
		/*=============================================================================
	   /		Получает цепочку категорий от текущей до корневой					  /
	   *============================================================================*/
	  
		function getCategoryUp($where){
			static $chain_category = array();
			static $i = 0;
			$model = new Sibirix_Model_Goods;
			$categories = $model->getCat($where);

			array_push($chain_category, $categories[$i]);
			if(!empty($categories[$i]->IBLOCK_SECTION_ID)){
				$filter['where'] = ["=ID" => $categories[$i]->IBLOCK_SECTION_ID];
				getCategoryUp($filter);
				$i+=1;
			}
			return array_reverse($chain_category);
		};
		
		$filter = array();
		$filter['where'] = ["=ID" => $categoryId];
		$ch_categories = getCategoryUp($filter);
	
		$this->view->assign([
			"pageTitle"	 	=> $current_cat,
			"filter"   	 	=> $filter,
			"itemList"  	=> $result->items,
			'paginator' 	=> EHelper::getPaginator($result->pageData->totalItemsCount, $result->pageData->current, $result->pageData->size),
			"categoryId" 	=> $categoryId,
			"categories" 	=> $tree,
			"view"			=> $view,
			"ch_categories" => $ch_categories,
		]);
    }
	
	public function itemAction(){
		$APP = Zend_Registry::get('BX_APPLICATION');
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'shop-detail');
        //id категории
		$categoryId = $this->getParam("categoryId");
		//id товара
		$itemId = $this->getParam("itemId");
		 /*=============================================================================
		/		Проверим есть ли у товара торговое предложение						   /
		*============================================================================*/
		$properties = array();
		$trade_offer = array();
		$color_offer = array();
		$size_offer = array();
		$material_offer = array(); 
		if(CCatalogSKU::IsExistOffers($itemId, IB_GOODS)){
			$trade_offer = CCatalogSKU::getOffersList(
			array($itemId),
			IB_GOODS,
			array(),
			['PROPERTY_COLOR', 'PROPERTY_LINK', 'PROPERTY_MATHERIAL', 'PROPERTY_PRICE', 'PROPERTY_WIDTH', 'PROPERTY_HEIGHT', 'PROPERTY_LENGTH'],
			array()
			);

			foreach($trade_offer[$itemId] as $property){
				if(!empty($property['PROPERTY_COLOR_VALUE'])){
					//$color_offer[$property['ID']]['ID'] = $property['ID'];
					$color_offer[$property['ID']] = $property['PROPERTY_COLOR_VALUE'];
				};

				if(!empty($property['PROPERTY_MATHERIAL_VALUE'])){
					//$material_offer[$property[$property['ID']]['ID'] = $property['ID'];
					$material_offer[$property['ID']] = $property['PROPERTY_MATHERIAL_VALUE'];
				};
				if(!empty($property['PROPERTY_WIDTH_VALUE']) &&
				   !empty($property['PROPERTY_HEIGHT_VALUE']) &&
				   !empty($property['PROPERTY_LENGTH_VALUE']))
				{
					
					//$size_offer[$property[$property['ID']]['ID'] = $property['ID'];
					//$size_offer[$property[$property['ID']]['PROPERTY_WIDTH_VALUE'] = $property['PROPERTY_WIDTH_VALUE'];
					//$size_offer[$property[$property['ID']]['PROPERTY_HEIGHT_VALUE'] = $property['PROPERTY_HEIGHT_VALUE'];
					$size_offer[$property['ID']] = $property['PROPERTY_WIDTH_VALUE'].'x'.$property['PROPERTY_HEIGHT_VALUE'].'x'.$property['PROPERTY_LENGTH_VALUE'];
										
				};
				$property['PROPERTY_PRICE_VALUE'] = CPrice::GetBasePrice($property['ID'], 0, 0);
				$trade_offer[$itemId][$property['ID']] = $property;
			}
		
			//оставим только уникальные значения
			$color_offer = array_unique($color_offer);
			$material_offer = array_unique($material_offer);
			$size_offer = array_unique($size_offer);
			$properties = array();
			$properties['color'] = $color_offer;
			$properties['material'] = $material_offer;
			$properties['size'] = $size_offer;
		}
		 /*=============================================================================
		/	Берем товар							  									  /
	   *============================================================================*/
		if(CModule::IncludeModule("iblock"))
		CIBlockElement::CounterInc($itemId);
		$model = new Sibirix_Model_Goods;
		$model_design = new Sibirix_Model_Design;
		$item = $model -> getGoodsAItem($itemId);
		Zend_Registry::get('BX_APPLICATION')->SetTitle($item->NAME);
		if(!$item || $item->PROPERTY_STATUS_ENUM_ID != GOODS_STATUS_PUBLISHED){
			throw new Zend_Exception('Not found', 404);
		}
		
		 /*=============================================================================
		/	Получим превью изображения							  					  /
	   *============================================================================*/
		$item = $model -> getImgItemsPrev($item);
	
		$design_list = array();
		if($item->PROPERTY_USED_DESIGN_VALUE && count($item->PROPERTY_USED_DESIGN_VALUE) > 0){
			for($i = 0; $i < count($item->PROPERTY_USED_DESIGN_VALUE); $i++){
				$design_list[] = $model_design->getDesignList(['=ID' => $item->PROPERTY_USED_DESIGN_VALUE[$i]],[],false);
			}
		}
		/*=============================================================================
	   /		Кука, будет содержать id просмотренных товары						  /
	   *============================================================================*/
		$VISITOR_ITEMS = $APP->get_cookie("VISITOR_ITEMS");
		//если ранее мы получали куки
		if($VISITOR_ITEMS){
			//распакуем массив
			$cookieitems = unserialize($VISITOR_ITEMS);
			//флаг, показывает есть ли у нас уже товар в куках или нет
			$flag = 0;
			//пройдемся по массиву, чтобы проверить есть там товар или нет
			for($i = 0; $i < count($cookieitems); $i++){
				if($cookieitems[$i] == $itemId){
					$flag = 1;
					break;
				};
			};

			if($flag == 0){
				/*if(count($cookieitems) > 4){
					$start = count($cookieitems) - 4;
					$old_cookieitems = $cookieitems;
					$cookieitems = array();
					for($i = $start, $n = 0; $i < count($old_cookieitems); $i++, $n++){
						array_push($cookieitems, $old_cookieitems[$n]);
					}

					***

					unset($cookieitems[0]);
				};*/
		
				if(count($cookieitems) < 8){
					array_unshift($cookieitems, $itemId);
				}
				else{
					$excese = count($cookieitems) - 8;
					array_splice($cookieitems, -$excese);
					//array_shift($cookieitems);
					//unset($cookieitems[0]);
					array_unshift($cookieitems, $itemId);
				};
				$APP->set_cookie("VISITOR_ITEMS", serialize($cookieitems), time()+60*60*24*30);
			};
		}
		else{
			//куку на 1 месяц
			$APP->set_cookie("VISITOR_ITEMS", serialize(array($itemId)), time()+60*60*24*30);
		}
		
		$result = $this->_model->getProductList($catalogFilter, $catalogSort);
		
		$discount = 0;
		for($i = 0; $i < count($result->items); $i++){
			if(CCatalogSKU::IsExistOffers($result->items[$i]->ID, IB_GOODS)){
				$result->items[$i]->OFFERS = 1;
			}
			else{$result->items[$i]->OFFERS = 0;};
			if(isset($result->items[$i]->DISCOUNT)){
				$discount+=1;
			}
		}
		
		/*=============================================================================
	   /		Получим просмотренные товары					  					  /
	   *============================================================================*/
		$VISITOR_ITEMS = $APP->get_cookie("VISITOR_ITEMS");
		$view = 0;
		//если куку
		if($VISITOR_ITEMS){
			//распакуем массив
			$cookieitems = unserialize($VISITOR_ITEMS);
			//пройдемся по массиву, чтобы проверить есть там товар или нет
			for($i = 0; $i < count($cookieitems); $i++){
				for($n = 0; $n < count($result->items); $n++){
					if($result->items[$n]->ID == $cookieitems[$i]){
						$result->items[$n]->VIEW = 1;
						$view+=1;
					};
				};
			};
		}
		/*=============================================================================
	   /		Получает цепочку категорий от текущей до корневой					  /
	   *============================================================================*/
	   	$filter['where'] = ["=ID" => $categoryId];

		function getCategoryUp($where){
			static $chain_category = array();
			static $i = 0;
			$model = new Sibirix_Model_Goods;
			$categories = $model->getCat($where);
			array_push($chain_category, $categories[$i]);
			if(!empty($categories[$i]->IBLOCK_SECTION_ID)){
				$filter['where'] = ["=ID" => $categories[$i]->IBLOCK_SECTION_ID];
				getCategoryUp($filter);
				$i+=1;
			}
			return array_reverse($chain_category);
		};
		
		$filter = array();
		$filter['where'] = ["=ID" => $categoryId];
		$ch_categories = getCategoryUp($filter);

		/*Получаем категории
			if(Если мы в дочерней категории){нужно получить соседние категории для текущей}
			else if(Если в родительской){получаем дочерние}
		*/
		//смотрим, что у нас за категория
		$category = $model->getCat($filter);
		//если есть родитель
		if(!empty($category[0]->IBLOCK_SECTION_ID)){
			//то берем все его дочерние категории(соседи текущей)
			$filter['where'] = ["=SECTION_ID" => $category[0]->IBLOCK_SECTION_ID];
			$categories = $model->getCat($filter);
		}
		else{
			//берем дочерние категории
			$filter['where'] = ["=SECTION_ID" => $category[0]->ID];
			$categories = $model->getCat($filter);
		};

		$this->view->assign([
			"pageTitle" 	=> 'Маркетплейс',
			"itemId"		=> $itemId,
			"itemList"  	=> $result->items,
			"design_list" 	=> $design_list,
			"view" 			=> $view,
			"ch_categories"	=> $ch_categories,
			"item" 			=> $item,
			"trade_offer"	=> $trade_offer,
			"properties"	=> $properties
		]);
    }
	
	public function likeAddAction() {
        if (!check_bitrix_sessid() || !Sibirix_Model_User::isAuthorized()) {
            Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'not-found-page');
            throw new Zend_Exception('Not found', 404);
        }
	
        $likeModel = new Sibirix_Model_Like();
        $itemId = (int) $this->getParam("itemId");
		
        $likeModel->add($itemId);
		
       // $count = $this->_model->cacheLikes($itemId);

	    $hh = Highload::instance(HL_LIKES)->cache(0);
	
        $list = $hh->where(['UF_DESIGN' => $id])->fetch();
        $count = count($list);

        CIBlockElement::SetPropertyValuesEx($id, IB_DESIGN, array(
            "LIKE_CNT" => $count
        ));
	   
        $this->_helper->json(['success' => true, 'likeCnt' => $count]);
    }

    public function likeRemoveAction() {
        if (!check_bitrix_sessid() || !Sibirix_Model_User::isAuthorized()) {
            Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'not-found-page');
            throw new Zend_Exception('Not found', 404);
        }

        $likeModel = new Sibirix_Model_Like();
        $itemId = (int) $this->getParam("itemId");
        $likeModel->remove($itemId);
        //$count = $this->_model->cacheLikes($itemId);
		   $hh = Highload::instance(HL_LIKES)->cache(0);
	
        $list = $hh->where(['UF_DESIGN' => $id])->fetch();
        $count = count($list);

        CIBlockElement::SetPropertyValuesEx($id, IB_DESIGN, array(
            "LIKE_CNT" => $count
        ));
        $this->_helper->json(['success' => true, 'likeCnt' => $count]);
    }
	
	public function cacheLikes($id) {
        $hh = Highload::instance(HL_LIKES)->cache(0);
	
        $list = $hh->where(['UF_DESIGN' => $id])->fetch();
        $count = count($list);

        CIBlockElement::SetPropertyValuesEx($id, IB_DESIGN, array(
            "LIKE_CNT" => $count
        ));

        return $count;
    }
	
	
	
	
	
	
	
	
	
	
	//добавляет товары поштучно
	public function addoneAction() {

		$model = new Sibirix_Model_Goods;
	
		
    }
	//удаляет товары
	public function delgoodsAction() {
		$elems = json_decode($this->getParam('elems'));
		$model = new Sibirix_Model_Goods;
		$i = 0;
		for($i; $i < count($elems); $i++){
			$result = $model -> delGoods($elems[$i], $this->user->ID);
		}

		exit;
	}
	//меняет статус
	public function changestatusgoodsAction() {
		$elems = json_decode($this->getParam('elems'));
		$model = new Sibirix_Model_Goods;
	
		$i = 0;
		
		$data = array("STATUS" => 16);
		
		for($i; $i < count($elems); $i++){
			$result = $model -> changestatusGoods($elems[$i], $data);	
		}
	}
	//добавляет товары из YML
	public function addgoodsAction() {
		//ini_set('error_reporting', E_ALL);
		//ini_set('display_errors', 1);
		//ini_set('display_startup_errors', 1);
		$uploaddir = $_SERVER['DOCUMENT_ROOT'].'/upload/files/';
		include($_SERVER['DOCUMENT_ROOT'].'/local/include/XMLValid.php');
		//yml
		$realfile = $_FILES['files']['name'];
		
		//если есть файл
		if($realfile){
			/*$validate = new XmlValidator();
			$xml = file_get_contents($_FILES['files']['tmp_name']);
			echo $validate->isXMLContentValid($xml);
			exit;*/
			//$xmlContent = file_get_contents($xmlFilename);

			//isXMLContentValid
			//isXMLFileValid
			//что-то делаем с файлом
			$infoimg = getimagesize($_FILES['files']['tmp_name']);
			$content = file_get_contents($_FILES['files']['tmp_name']);
			$validate = new XMLValid();
			
			$ext =  substr(strrchr($realfile, '.'), 1);
			if(strtolower($ext)!='xml')
			$error['PROPERTY_VALUES']['FILE'] = 'Возможна загрузка файлов только с расширением xml<br>';
			if( $_FILES['files']['size'] >= 8*1024*1024*10)  $error .= 'Загружаемый файл \"'.$_FILES['files']['tmp_name'].'\" больше 10Мб<br>';
		}
		else{
			//что-то случилось
			$error['PROPERTY_VALUES']['FILE'] = 'Не выбран файл';
		};
		if(!$error){
			//если ничего не случилось
			if($realfile) { // Загружаем файл
				$filename               = md5($realfile . time()).'.'.$ext;
				$uploadfile             = $uploaddir . $filename;
				$data['files'] = $filename;
				chmod($_FILES['files']['tmp_name'][0], 0666);
				//сохраняем файл
				if(@move_uploaded_file($_FILES['files']['tmp_name'], $uploadfile)){
					echo json_encode(['success'=>true, 'url'=>$uploaddir . $filename]);
					exit;
				};
			};
		}
		else{

		};
	}
	
	public function parseAction() {
		$realfile = $this->getParam('file');
		//подключим xml парсер
		include $_SERVER['DOCUMENT_ROOT'].'/local/include/SimpleXMLReader.php';
		//экстенд xml
		include ($_SERVER['DOCUMENT_ROOT'].'/local/include/XMLReader.php');
		$file = $realfile;
		$model = new Sibirix_Model_Goods();
		$reader = new XML();
		$reader->open($file);
		$reader->parse();
		$reader->close();
		$items = $reader->arr_items;
		
		//генерируем название папки
		$dirname = substr(md5(microtime() . rand(0, 9999)), 0, 3);
		$dir = $_SERVER['DOCUMENT_ROOT'].'/upload/'.$dirname;
		//создадим папку
		mkdir($dir, 0666);
		//массив содержит св-ва элемента
		$data = array();
		$error = array();
		$data['NAME'] = $this->getParam('name');
		foreach($items as $item){
			$data = array();
			$data['NAME'] = $item->name .' '.$item->model;
			$data['PROPERTY_VALUES']['LENGTH'] = 0;
			$data['PROPERTY_VALUES']['WIDTH'] = 0;
			$data['PROPERTY_VALUES']['HEIGHT'] = 0;
			$data['PROPERTY_VALUES']['STATUS'] = 16;
			$data['PROPERTY_VALUES']['MATERIAL'] = 'Пластик';
			$data['PROPERTY_VALUES']['PRICE'] = $item->price;
			$data['PROPERTY_VALUES']['ARTICLE'] = $item->price;
			$data['PROPERTY_VALUES']['ID_USER'] = $this->user->ID;
			$data['IBLOCK_SECTION_ID'] = $item->categoryId;
			$filename = substr(md5(microtime() . rand(0, 9999)), 0, 32);
			//определим расширение
			$ext =  substr(strrchr($item->picture, '.'), 1);
			$path = $dir.'/'.$filename.'.'.$ext;
			//сохраняем картинку
			copy($item->picture, $path);

			$uploaddir_img = $_SERVER['DOCUMENT_ROOT'].'/upload/goods/';
			$uploaddir_img_small = $_SERVER['DOCUMENT_ROOT'].'/upload/goods/small/';
			//картинка
			$realfile_img = $path;
			
			//если есть картинка
			if($realfile_img){
				//что-то делаем с картинкой
				$infoimg = getimagesize($path);
				$ext =  substr(strrchr($realfile_img, '.'), 1);
				if(strtolower($ext)!='png' and strtolower($ext)!='jpg' and  strtolower($ext)!='jpeg' and  strtolower($ext)!='gif' )
				$error['PROPERTY_VALUES']['IMG'] = 'Возможна загрузка файлов только с расширением png, jpg, jpeg, gif<br>';
				//if( $infoimg >= 8*1024*1024*3)  $error .= 'Загружаемый файл \"'.$_FILES['image']['tmp_name'].'\" больше 3Мб<br>';
			}
			else{
				//что-то случилось
				$error['PROPERTY_VALUES']['IMG'] = 'Не выбрано изображение';
			};
			if(!$error){
				//если ничего не случилось
				if($realfile_img) { // Загружаем картинку
					$filename_img               = md5($realfile_img . time()).'.'.$ext;
					$uploadfile             = $uploaddir_img . $filename_img;
					chmod($path, 0666);

						require_once($_SERVER['DOCUMENT_ROOT'].'/local/include/SimpleImage.php');
						$image = new SimpleImage();
						$image->load($path);
						$image->save($uploaddir_img . $filename_img);
						$image->load($path);
						$image->resize(450, 300);
						$image->save($uploaddir_img_small . $filename_img);
	
					$data['PROPERTY_VALUES']['IMG'] = '/upload/goods/' . $filename_img;
				};
			}
			else{

			};
	
			$model -> addGoods($data);
			echo json_encode(['success'=>true]);
					exit;
		}
		exit;
		//подключим xml парсер
		//include $_SERVER['DOCUMENT_ROOT'].'/local/include/SimpleXMLReader.php';
		//дочерний
		//include $_SERVER['DOCUMENT_ROOT'].'/local/include/ExtendXMLReader.php';
		/*
		$file = "YML.xml";
		$reader = new ExtendXMLReader;
		$reader->open($file);
		$reader->parse();
		$reader->close();*/	
	}

    
}