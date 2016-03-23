<?

/**
 * Class FeedbackController
 */
class ProjectController extends Sibirix_Controller {

    /**
     * @var Sibirix_Model_Feedback
     */
    protected $_model;

    public function init() {
		$ajaxContext = $this->_helper->getHelper('SibirixAjaxContext');
		$ajaxContext->addActionContext('index', 'html')
		->initContext();

        $this->_model  = new Sibirix_Model_Plan();
    }
	public function indexAction() {
		
		Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'project-page index');
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'project');
		Zend_Registry::get('BX_APPLICATION')->SetTitle(("Выбор планировки"));
		
		$planModel = new Sibirix_Model_Plan();
		$planoptionModel = new Sibirix_Model_PlanOption();
		$complexModel = new Sibirix_Model_Complex();
		$houseModel = new Sibirix_Model_House();
		$flatModel = new Sibirix_Model_Flat();
		$entranceModel = new Sibirix_Model_Entrance();
		
		$street = $this->getParam('street');
		
		if(!$street){
			$this->redirect('/');
		}
		
		$filter["PROPERTY_STREET"] = $street;
		$number = $this->getParam('number');
		if($number){
			$filter["PROPERTY_HOUSE_NUMBER"] = $number;
		}
		//1)Находим дом
		if(!empty($street)){
			$house = $houseModel->select(['ID', 'NAME', 'PROPERTY_COMPLEX'], true)->where($filter, true)->getElement();
		}
		//1)Находим комплекс
		if($house){
			$complex = $complexModel->select(['ID', 'NAME'], true)->where(["ID" => $house->PROPERTY_COMPLEX])->getElement();
		}
		//2)Находим подъезды
		
		//3)Находим этажи
		
		//4)Находим квартиры
		$ARR_FLAT = $houseModel->getFlatListb($house->ID);
		$planIds = array_map(function ($obj) {
					return $obj->PROPERTY_PLAN;
					}, $ARR_FLAT);
		$planIds = array_unique($planIds);
		/*plans = $planModel
		->select(["ID", "PROPERTY_ROOM", "PROPERTY_IMAGES", "PROPERTY_AREA"], true)
		->where(["ID" => $planIds])
		->getElements();*/
		
		$designModel = new Sibirix_Model_Design();
        $catalogSort["SORT"] = "ASC";	
        $filter = new Sibirix_Form_FilterDesign();	
        $filterParams = $this->getAllParams();
        $catalogFilter = $this->_model->prepareFilter($filterParams);
		$catalogFilter['=ID'] = $planIds;
		//$catalogFilter['=PROPERTY_SHOW_STEP_1_VALUE'] = 'Y';
        $pageTitle     = $this->_model->getPageTitle($validFilterValues);
		if($this->getParam('page')){
			$limit = $this->getParam('page') * 10;
		}
		else{
			$limit = 10;
		}
		if($house){
			$result = $planModel->getPlanList($catalogFilter, $catalogSort, 1, false, $limit);
		};
		$option_id = $this->getParam('options');
		$designs_id = $this->getParam('designs');
		$resultOptions = array();
		
		if($option_id){
			foreach($result->items as $res){
				if($res->ID == $option_id){
					$resultOptions = $planoptionModel->getPlanList(['=PROPERTY_PLAN_FLAT'=>$option_id], $catalogSort, 1, false, 999);
				};
			};
		};

		if($designs_id){
			foreach($resultOptions->items as $res){
				if($res->ID == $designs_id){
					$resultDesigns = $designModel->getDesignList(['=PROPERTY_PLAN'=>$designs_id], $catalogSort, 1, false, 999);
				};
			};
		};
			
        $this->view->assign([
            "pageTitle" => $pageTitle,
            "filter"    => $filter,
            "itemList"  => $result->items,
			"complex"	=> $complex,
			"itemListPlan"  => $resultOptions->items,
			"options"	=> $option_id,
			"itemListDesign"  => $resultDesigns->items,
            'paginator' => EHelper::getPaginator($result->pageData->totalItemsCount, $result->pageData->current, $result->pageData->size),
        ]);
    }
	
	public function listProjectAvtion(){
		
	}
	
	public function step1Action() {
		$APP = Zend_Registry::get('BX_APPLICATION');
		
		Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'create-project-page');
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'project');
		Zend_Registry::get('BX_APPLICATION')->SetTitle(("Выбор планировки"));
		
		$ajaxContext = $this->_helper->getHelper('SibirixAjaxContext');
		$ajaxContext->addActionContext('step1', 'html')
		->initContext();
		
		$planModel = new Sibirix_Model_Plan();
		$planoptionModel = new Sibirix_Model_PlanOption();
		$flatModel = new Sibirix_Model_Flat();
		$projectModel = new Sibirix_Model_Project();
		$userModel = new Sibirix_Model_User();
		$access = false;
		/*==========================================
			Авторизован?
		=========================================*/
		if($this->getParam('page')){
			$limit = $this->getParam('page') * 8;
		}
		else{
			$limit = 8;
		}
		if(Sibirix_Model_User::isAuthorized()){
			$auth = true;
			//Проверка есть ли в куках уже добавленный заказ	
			//.................??????????................
			
			$id_user = $userModel->getId();
			/*====================================
				Берем заказ(если есть)
			====================================*/
			$PROJECT_ARR = $projectModel->getProjectList(['=PROPERTY_USER' => $id_user, '=PROPERTY_STATUS' => PROJECT_STATUS_ADDED], [], 1, false, 1);
			if($PROJECT_ARR->items[0]->PROPERTY_STEP_VALUE > 1){
				$access = true;
			}
			$count = $PROJECT_ARR->pageData->totalItemsCount;
			if($count > 0){
				$item = $PROJECT_ARR->items[0];
				//id планировки
				$id_flat = $item->PROPERTY_ID_FLAT_VALUE;
				
				//id планировки(вариант)
				$id_plan = $item->PROPERTY_ID_OPTION_PLAN_VALUE;
				$PLAN_SAVE = $planModel->getPlanList(['=ID'=>$id_flat], [], 1, false, 1);
				if($id_plan){
					$PLAN_OPTION_SAVE = $planoptionModel->getPlanList(['=ID'=>$id_plan], [], 1, false, 1);
				};

				foreach($PLAN_SAVE->items as $plan){
					if (!empty($plan->PROPERTY_IMAGES_VALUE)) {
						$image = Resizer::resizeImage($plan->PROPERTY_IMAGES_VALUE, 'COMPLEX_LIST');
					} else {
						$image = '/local/images/proomer2.png';
					}
					$plan->PLAN_IMAGE = $image;
				}
				foreach($PLAN_OPTION_SAVE->items as $plan){
					if (!empty($plan->PROPERTY_IMAGES_VALUE)) {
						$image = Resizer::resizeImage($plan->PROPERTY_IMAGES_VALUE, 'COMPLEX_LIST');
					} else {
						$image = '/local/images/proomer2.png';
					}
					$plan->PLAN_IMAGE = $image;
				}
			}
		}
		/*======================================
			Куки
		======================================*/
		else{
			$auth = false;
		};
		//получим массив квартир
		$catalogSort["SORT"] = "ASC";	
		$filterParams = $this->getAllParams();	
		// $catalogFilter = $this->_model->prepareFilter($filterParams);
		$catalogFilter = [];
		$pageTitle = $this->_model->getPageTitle($validFilterValues);
		$id_item = $this->getParam('id_item');
		$FLAT_ARR = $flatModel->getFlatList($catalogFilter, $catalogSort, 1, false, 999);
		$planIds = array_map(function ($obj) {
						return $obj->PROPERTY_PLAN_VALUE;
					}, $FLAT_ARR->items);
		$planIds = array_unique($planIds);
		$PLAN_ARR = $planModel->getPlanList(['=ID'=>$planIds],[], 1, false, $limit);

		foreach($PLAN_ARR->items as $plan){			
			if (!empty($plan->PROPERTY_IMAGES_VALUE)) {
				$image = Resizer::resizeImage($plan->PROPERTY_IMAGES_VALUE, 'COMPLEX_LIST');
			} else {
				$image = '/local/images/proomer2.png';
			}
			
			$plan->PLAN_IMAGE = $image;
		
		}
	
		$upload_result = $projectModel->getProjectList(['=PROPERTY_USER' => $id_user, '=PROPERTY_STATUS' => PROJECT_STATUS_MODERATION], [], 1, false, 1);
		$upload_result -> items = $projectModel->getImageData($upload_result->items, "DETAIL_PICTURE");$upload_result -> items = $projectModel->getImageData($upload_result->items, "PREVIEW_PICTURE");
		$a_result = $planModel->getPlanList(['=ID' => $id_plan], $catalogSort, 1, false, 1);
		$starting_price = ($PROJECT_ARR->items[0]->PROPERTY_PRICE_SQUARE_VALUE * $a_result->items[0]->PROPERTY_AREA) + ($PROJECT_ARR->items[0]->PROPERTY_PRICE_SQUARE_VALUE * $a_result->items[0]->PROPERTY_AREA)/ 100 * 21;

		$this->view->assign([
            "pageTitle" 		=> $pageTitle,
            "itemList"  		=> $PLAN_ARR->items,
			"projectData" 		=> $PROJECT_ARR->items[0],
			"starting_price" 	=> $starting_price,
			"uploadItemList"  	=> $upload_result->items,
			"url"				=> '/project/step2/',
			"auth"				=> $auth,
			"access"			=> $access,
			"step"				=> 'step1',
			"nextstep"			=> 'savestep1',
			"nextstep_d"		=> 'К параметры <br/> дизайн-проекта',
			"PLAN_SAVE"			=> $PLAN_SAVE->items,
			"PLAN_OPTION_SAVE"	=> $PLAN_OPTION_SAVE->items,
            'paginator' 		=> EHelper::getPaginator($result->pageData->totalItemsCount, $result->pageData->current, $result->pageData->size),
        ]);

    }
	
	public function savestep1Action(){
		/*========================================
			Сохранение
			Принцип работы: Если пользователь не авторизован, то храним в куку первый и второй шаг,
			далее он должен будет авторизоваться и первый, второй шаг переносим в бд, следущие шаги также сохраняются в бд
			Если авторизован просто храним в бд
		=========================================*/
		$planModel = new Sibirix_Model_Plan();
		$projectModel = new Sibirix_Model_Project();
		$userModel = new Sibirix_Model_User();
		$id_flat = $this->getParam('id_flat');
		$id_plan = $this->getParam('id_plan');
		/*==========================================
			Авторизован?
		=========================================*/
		$catalogSort = ['ASC'];
		if(Sibirix_Model_User::isAuthorized()){
			//Проверка есть ли в куках уже добавленный заказ	
			//.................??????????................
			$id_user = $userModel->getId();
			//получаем планировку выбранную на первом шаге
			$result = $planModel->getPlanList(['=ID' => $id_flat], [], 1, false, 1);
			$item = $result->items[0];
	
				$fields = array();
				$fields["NAME"] = $result->items[0]->NAME;
				$fields["PROPERTY_VALUES"]["USER"] = $id_user;
				$fields["PROPERTY_VALUES"]["ID_OPTION_PLAN"] = false;
				$fields["PROPERTY_VALUES"]["STATUS"] = PROJECT_STATUS_ADDED;
				$fields["PROPERTY_VALUES"]["ID_FLAT"] = $id_flat;
				$fields["PROPERTY_VALUES"]["ID_OPTION_PLAN"] = $id_plan;
			
			//проверим есть ли уже добавленные заказы
			$PROJECT_ARR = $projectModel->getProjectList(['=PROPERTY_USER' => $id_user, '=PROPERTY_STATUS' => PROJECT_STATUS_ADDED], $catalogSort, 1, false, 1);
			$count = $PROJECT_ARR->pageData->totalItemsCount;
			if($count == 0){
				//адд
				$fields["PROPERTY_VALUES"]["STEP"] = 2;
				if($projectModel -> addProject($fields)){
				};
			}
			else{
				//упдате
				$fields['ID'] = $PROJECT_ARR->items[0]->ID;
				$step = $PROJECT_ARR->items[0]->PROPERTY_STEP_VALUE;
				if(empty($step) || $step < 2){
					$fields["PROPERTY_VALUES"]["STEP"] = 2;
				}
				else{
					$fields["PROPERTY_VALUES"]["STEP"] = $PROJECT_ARR->items[0]->PROPERTY_STEP_VALUE;
				};
				//CIBlockElement::SetPropertyValuesEx($result_project->items[0]->ID, IB_ORDER_PROJECT, $fields['PROPERTY_VALUES']);
				$projectModel -> editProject($fields);
			};
		}
		else{
			//$APP = Zend_Registry::get('BX_APPLICATION');
			//$APP->set_cookie("selectPlanItem", "", time()-3600);
			//$APP->set_cookie("selectPlanItem", $id, time()+3600 * 24 * 3);
		};
		$this->_helper->json(['result' => 1, "response" => true]);
	}
	
	public function step2Action() {
		
		$APP = Zend_Registry::get('BX_APPLICATION');
		
		Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'create-project-page');
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'project');
		Zend_Registry::get('BX_APPLICATION')->SetTitle(("Выбор планировки"));
		
		$planModel = new Sibirix_Model_Plan();
		$planoptionModel = new Sibirix_Model_PlanOption();
		$projectModel = new Sibirix_Model_Project();
		$projectroomModel = new Sibirix_Model_ProjectRoom();
		$roomModel = new Sibirix_Model_Room();
		$userModel = new Sibirix_Model_User();
		/*==========================================
			Узнаем есть ли у пользователя уже заказ
			Если есть берем
		=========================================*/
		
		$catalogSort["SORT"] = "ASC";
		if(Sibirix_Model_User::isAuthorized()){
			$auth = true;
			$id_user = $userModel->getId();
			//заказ
			$PROJECT_ARR = $projectModel->getProjectList(['=PROPERTY_USER' => $id_user, '=PROPERTY_STATUS' => PROJECT_STATUS_ADDED], [], 1, false, 1);
			//если не пройден 1 шаг
			if($PROJECT_ARR->items[0]->PROPERTY_STEP_VALUE < 2){
				$this->redirect('/project/step1/');
			}
			if($PROJECT_ARR->items[0]->PROPERTY_STEP_VALUE >= 2){
				$access = true;
			}
			$count = $PROJECT_ARR->pageData->totalItemsCount;
			if($count > 0){
				$item = $PROJECT_ARR->items[0];
				//id планировки
				$id_flat = $item->PROPERTY_ID_FLAT_VALUE;
				//id планировки(вариант)
				$id_plan = $item->PROPERTY_ID_OPTION_PLAN_VALUE;
			
				$PLAN_SAVE = $planModel->getPlanList(['=ID'=>$id_flat], [], 1, false, 1);
				if($id_plan){
					$PLAN_OPTION_SAVE = $planoptionModel->getPlanList(['=ID'=>$id_plan], [], 1, false, 1);
				};

				foreach($PLAN_SAVE->items as $plan){
					if (!empty($plan->PROPERTY_IMAGES_VALUE)) {
						$image = Resizer::resizeImage($plan->PROPERTY_IMAGES_VALUE, 'COMPLEX_LIST');
					} else {
						$image = '/local/images/proomer2.png';
					}
					$plan->PLAN_IMAGE = $image;
				}
				foreach($PLAN_OPTION_SAVE->items as $plan){
					if (!empty($plan->PROPERTY_IMAGES_VALUE)) {
						$image = Resizer::resizeImage($plan->PROPERTY_IMAGES_VALUE, 'COMPLEX_LIST');
					} else {
						$image = '/local/images/proomer2.png';
					}
					$plan->PLAN_IMAGE = $image;
				}
			};
		}
		else{
			$this->redirect('/project/step1/');
			$auth = false;
		};
		
		$filterParams = $this->getAllParams();	
		// $catalogFilter = $this->_model->prepareFilter($filterParams);
		$catalogFilter = ['=PROPERTY_SHOW_STEP' => false, '=PROPERTY_SHOW_STEP_1_VALUE' => 'Y'];
		$pageTitle = $this->_model->getPageTitle($validFilterValues);
		$id_item = $this->getParam('id_item');
	
		
		//$PLAN_ARR = $planModel->getPlanList(['=ID' => $id_plan], $catalogSort, $this->getParam("page", 1), false, 999);
		$starting_price = ($PROJECT_ARR->items[0]->PROPERTY_PRICE_SQUARE_VALUE * $PLAN_ARR->items[0]->PROPERTY_AREA) + ($PROJECT_ARR->items[0]->PROPERTY_PRICE_SQUARE_VALUE * $PLAN_ARR->items[0]->PROPERTY_AREA)/ 100 * 21;
		$roomIds = array_map(function ($obj) {
		return $obj->PROPERTY_ROOM_PLAN_VALUE;
		}, $PLAN_OPTION_SAVE->items);
		$roomIds = array_unique($roomIds);

		if($roomIds){
			$ARR_ROOM = $roomModel->getRoomList(['=ID' => $roomIds[0]], [], 1, false);
			
			foreach($ARR_ROOM->items as $room){
				$room->PRICE_ROOM = ($room->PROPERTY_PRICE_SQUARE_VALUE * $room->PROPERTY_AREA_VALUE) + ($room->PROPERTY_PRICE_SQUARE_VALUE * $room->PROPERTY_AREA_VALUE) / 100 * COMISSION_PERCENT;
			};
			$access = true;
			
		}
		else{
			$access = false;
		}

		$roomIds = array_map(function ($obj) {
		return $obj->PROPERTY_ROOM_ORDER_VALUE;
		}, $PROJECT_ARR->items);
		$roomIds = array_unique($roomIds);
		
		$ARR_PROJECT_ROOM = $projectroomModel->getProjectRoomList(['=ID' => $roomIds], [], 1, false);
						
		foreach($ARR_PROJECT_ROOM->items as $room){
			$room->PRICE_ROOM = (PRICE_SQUARE * $room->PROPERTY_AREA_VALUE) + (PRICE_SQUARE * $room->PROPERTY_AREA_VALUE) / 100 * COMISSION_PERCENT;
		};

		$this->view->assign([
            "pageTitle" 		=> $pageTitle,
			"projectData" 		=> $PROJECT_ARR->items[0],
			"starting_price" 	=> $starting_price,
			"url"				=> '/project/step3/',
			"step"				=> 'step2',
			"nextstep"			=> 'savestep2',
			'access' 			=> $access,
			'auth'				=> $auth,
			"nextstep_d"		=> 'К оформлению',
			"PLAN_SAVE"			=> $PLAN_SAVE->items,
			"PLAN_OPTION_SAVE"	=> $PLAN_OPTION_SAVE->items,
			"ARR_ROOM"			=> $ARR_ROOM->items,
			"ARR_PROJECT_ROOM"	=> $ARR_PROJECT_ROOM->items,
            'paginator' 		=> EHelper::getPaginator($PLAN_ARR->pageData->totalItemsCount, $PLAN_ARR->pageData->current, $PLAN_ARR->pageData->size),
        ]);

    }
	
	public function savestep2Action(){

		$planModel = new Sibirix_Model_Plan();
		$projectModel = new Sibirix_Model_Project();
		$projectroomModel = new Sibirix_Model_ProjectRoom();
		$userModel = new Sibirix_Model_User();
		$defaultValues = $this->getAllParams();
		
		//если авторизован
		$catalogSort["SORT"] = "ASC";	
		if(Sibirix_Model_User::isAuthorized()){
			
			$id_user = $userModel->getId();
			
			$fields = array();
			$fields["NAME"] = PATTERN_ROOM_NAME .time();
			$fields["ID"] = $defaultValues['id_room'];
		
			$fields["PROPERTY_VALUES"]["TYPE_ROOM"] = $defaultValues['type_room'];
			$fields["PROPERTY_VALUES"]["TIME"] = $defaultValues['slider_time'];
			$fields["PROPERTY_VALUES"]["PEOPLE"] = $defaultValues['age_man'];
			$fields["PROPERTY_VALUES"]["PEOPLE_CHILDREN"] = $defaultValues['age_children'];
			$fields["PROPERTY_VALUES"]["SUGGEST"] = $defaultValues['comments'];
			$fields["PROPERTY_VALUES"]["AREA"] = $defaultValues['area'];
			$fields["PROPERTY_VALUES"]["PRICE_SQUARE"] = $defaultValues['price_square'];
			$fields["PROPERTY_VALUES"]["DESIGN_LIKED"] = $defaultValues['liked_design'];
			
			//массив с заказом
			$PROJECT_ARR = $projectModel->getProjectList(['=PROPERTY_USER' => $id_user, '=PROPERTY_STATUS' => PROJECT_STATUS_ADDED], [], 1, false, 1);
			
			//проверим есть ли уже добавленные заказы
			$count = $PROJECT_ARR->pageData->totalItemsCount;
			if($count == 0){
				//послать отсюда
			}
			else{
				//посмотрим id 
				$roomIds = $PROJECT_ARR->items[0]->PROPERTY_ROOM_ORDER_VALUE;
				if($fields['ID'] > 0){
		
					if(in_array($fields['ID'], $roomIds)){
				
						$result = $projectroomModel->editRoomProject($fields);
					}
					else{
						return false;
					}
				}
				else{

					//добавим комнату
					$result = $projectroomModel->addRoom($fields);
				}
			
				array_push($roomIds, $result);
				
				$fields['PROPERTY_VALUES']["ROOM_ORDER"] = $roomIds;
				//упдате
				$step = $PROJECT_ARR->items[0]->PROPERTY_STEP_VALUE;
				if(empty($step) || $step < 3){
					$fields["PROPERTY_VALUES"]["STEP"] = 3;
				}
				else{
					$fields["PROPERTY_VALUES"]["STEP"] = $PROJECT_ARR->items[0]->PROPERTY_STEP_VALUE;
				}
				CIBlockElement::SetPropertyValuesEx($PROJECT_ARR->items[0]->ID, IB_ORDER_PROJECT, $fields['PROPERTY_VALUES']);
			};
		}
		else{
	
		};
		$this->_helper->json(['result' => $result, "response" => true]);
	}
	
	public function step3Action() {
		
		$APP = Zend_Registry::get('BX_APPLICATION');
		
		Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'create-project-page');
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'project');
		Zend_Registry::get('BX_APPLICATION')->SetTitle(("Выбор планировки"));
		
		$planModel = new Sibirix_Model_Plan();
		$planoptionModel = new Sibirix_Model_PlanOption();
		$projectModel = new Sibirix_Model_Project();
		$projectroomModel = new Sibirix_Model_ProjectRoom();
		$roomModel = new Sibirix_Model_Room();
		$userModel = new Sibirix_Model_User();
		/*==========================================
			Узнаем есть ли у пользователя уже заказ
			Если есть берем
		=========================================*/
		$catalogSort["SORT"] = "ASC";
		if(Sibirix_Model_User::isAuthorized()){
			$auth = true;
			$id_user = $userModel->getId();
			//заказ
			$PROJECT_ARR = $projectModel->getProjectList(['=PROPERTY_USER' => $id_user, '=PROPERTY_STATUS' => PROJECT_STATUS_ADDED], [], 1, false, 1);
			//если не пройден 1 шаг
			if($PROJECT_ARR->items[0]->PROPERTY_STEP_VALUE < 3){
				$this->redirect('/project/step2/');
			}
			if($PROJECT_ARR->items[0]->PROPERTY_STEP_VALUE >= 3){
				$access = true;
			}
			$count = $PROJECT_ARR->pageData->totalItemsCount;
			if($count > 0){
				$item = $PROJECT_ARR->items[0];
				//id планировки
				$id_flat = $item->PROPERTY_ID_FLAT_VALUE;
				//id планировки(вариант)
				$id_plan = $item->PROPERTY_ID_OPTION_PLAN_VALUE;
				$PLAN_SAVE = $planModel->getPlanList(['=ID'=>$id_flat], [], 1, false, 1);
				$PLAN_OPTION_SAVE = $planoptionModel->getPlanList(['=ID'=>$id_plan], [], 1, false, 1);

				foreach($PLAN_SAVE->items as $plan){
					if (!empty($plan->PROPERTY_IMAGES_VALUE)) {
						$image = Resizer::resizeImage($plan->PROPERTY_IMAGES_VALUE, 'COMPLEX_LIST');
					} else {
						$image = '/local/images/proomer2.png';
					}
					$plan->PLAN_IMAGE = $image;
				}
				foreach($PLAN_OPTION_SAVE->items as $plan){
					if (!empty($plan->PROPERTY_IMAGES_VALUE)) {
						$image = Resizer::resizeImage($plan->PROPERTY_IMAGES_VALUE, 'COMPLEX_LIST');
					} else {
						$image = '/local/images/proomer2.png';
					}
					$plan->PLAN_IMAGE = $image;
				}
			};
		}
		else{
			$this->redirect('/project/step1/');
			$auth = false;
		};
		
		$filterParams = $this->getAllParams();	
		// $catalogFilter = $this->_model->prepareFilter($filterParams);
		$catalogFilter = ['=PROPERTY_SHOW_STEP' => false, '=PROPERTY_SHOW_STEP_1_VALUE' => 'Y'];
		$pageTitle = $this->_model->getPageTitle($validFilterValues);
		$id_item = $this->getParam('id_item');
	
		
		//$PLAN_ARR = $planModel->getPlanList(['=ID' => $id_plan], $catalogSort, $this->getParam("page", 1), false, 999);
		$starting_price = ($PROJECT_ARR->items[0]->PROPERTY_PRICE_SQUARE_VALUE * $PLAN_ARR->items[0]->PROPERTY_AREA) + ($PROJECT_ARR->items[0]->PROPERTY_PRICE_SQUARE_VALUE * $PLAN_ARR->items[0]->PROPERTY_AREA)/ 100 * 21;
		$roomIds = array_map(function ($obj) {
		return $obj->PROPERTY_ROOM_PLAN_VALUE;
		}, $PLAN_OPTION_SAVE->items);
		$roomIds = array_unique($roomIds);
		
		$ARR_ROOM = $roomModel->getRoomList(['=ID' => $roomIds[0]], [], 1, false);

		/*foreach($ARR_ROOM->items as $room){
			$room->PRICE_ROOM = ($room->PROPERTY_PRICE_SQUARE_VALUE * $room->PROPERTY_AREA_VALUE) + ($room->PROPERTY_PRICE_SQUARE_VALUE * $room->PROPERTY_AREA_VALUE) / 100 * COMISSION_PERCENT;
		};*/
		$roomIds = array_map(function ($obj) {
		return $obj->PROPERTY_ROOM_ORDER_VALUE;
		}, $PROJECT_ARR->items);
		$roomIds = array_unique($roomIds);
		$ARR_PROJECT_ROOM = $projectroomModel->getProjectRoomList(['=ID' => $roomIds], [], 1, false);
	
		foreach($ARR_PROJECT_ROOM->items as $room){
			$room->PRICE_ROOM = (PRICE_SQUARE * $room->PROPERTY_AREA_VALUE) + (PRICE_SQUARE * $room->PROPERTY_AREA_VALUE) / 100 * COMISSION_PERCENT;
		};

		$this->view->assign([
            "pageTitle" 		=> $pageTitle,
			"projectData" 		=> $PROJECT_ARR->items[0],
			"starting_price" 	=> $starting_price,
			"url"				=> '/basket',
			"step"				=> 'step3',
			"nextstep"			=> 'savestep3',
			'access' 			=> $access,
			'auth'				=> $auth,
			"nextstep_d"		=> 'К оплате',
			"PLAN_SAVE"			=> $PLAN_SAVE->items,
			"PLAN_OPTION_SAVE"	=> $PLAN_OPTION_SAVE->items,
			"ARR_ROOM"			=> $ARR_ROOM->items,
			"ARR_PROJECT_ROOM"	=> $ARR_PROJECT_ROOM->items,
            'paginator' 		=> EHelper::getPaginator($PLAN_ARR->pageData->totalItemsCount, $PLAN_ARR->pageData->current, $PLAN_ARR->pageData->size),
        ]);
    }
	
	public function savestep3Action(){
		/*========================================
			Сохранение
			Принцип работы: Если пользователь не авторизован, то храним в куку первый и второй шаг,
			далее он должен будет авторизоваться и первый, второй шаг переносим в бд, следущие шаги также сохраняются в бд
			Если авторизован просто храним в бд
		=========================================*/
		$planModel = new Sibirix_Model_Plan();
		$projectModel = new Sibirix_Model_Project();
		$userModel = new Sibirix_Model_User();
		//$id = $this->getParam('id');
		
		//если авторизован
		$catalogSort = ['ASC'];
		if(Sibirix_Model_User::isAuthorized()){
			$id_user = $userModel->getId();
			$PROJECT_ARR = $projectModel->getProjectList(['=PROPERTY_USER' => $id_user, '=PROPERTY_STATUS' => PROJECT_STATUS_ADDED], $catalogSort, 1, false, 1);
			//получаем планировку
			$result = $planModel->getPlanList(['=ID' => $PROJECT_ARR->items[0]->PROPERTY_ID_PLAN], [], 1, false, 1);
			$item = $result->items[0];
	
				$fields = array();
				
			
			//провери есть ли уже добавленные заказы
			$count = $PROJECT_ARR->pageData->totalItemsCount;
			if($count == 0){
				$fields["PROPERTY_VALUES"]["STEP"] = 3;
				//адд
				if($projectModel -> addProject($fields)){
				};
			}
			else{
				//упдате
				$step = $PROJECT_ARR->items[0]->PROPERTY_STEP_VALUE;

				if(empty($step) || $step < 4){
					$fields["PROPERTY_VALUES"]["STEP"] = 4;
				}
				else{
					$fields["PROPERTY_VALUES"]["STEP"] = $PROJECT_ARR->items[0]->PROPERTY_STEP_VALUE;
				}
				CIBlockElement::SetPropertyValuesEx($PROJECT_ARR->items[0]->ID, IB_ORDER_PROJECT, $fields['PROPERTY_VALUES']);
			};
		}
		else{
			$APP = Zend_Registry::get('BX_APPLICATION');
			$APP->set_cookie("selectPlanItem", "", time()-3600);
			$APP->set_cookie("selectPlanItem", $id, time()+3600 * 24 * 3);
		};
		$this->_helper->json(['result' => 1, "response" => true]);
	}
	
	public function step4Action() {
		
		$APP = Zend_Registry::get('BX_APPLICATION');
		
		Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'project-page');
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'project');
		Zend_Registry::get('BX_APPLICATION')->SetTitle(("Выбор планировки"));
		
		$planModel = new Sibirix_Model_Plan();
		$projectModel = new Sibirix_Model_Project();
		$userModel = new Sibirix_Model_User();
		
		/*==========================================
			Узнаем есть ли у пользователя уже заказ
			Если есть берем
		=========================================*/
		$catalogSort["SORT"] = "ASC";	
		if(Sibirix_Model_User::isAuthorized()){
			$id_user = $userModel->getId();
			//заказ
			$PROJECT_ARR = $projectModel->getProjectList(['=PROPERTY_USER' => $id_user, '=PROPERTY_STATUS' => PROJECT_STATUS_ADDED], [], 1, false, 1);

			//если не пройден 1 шаг
			if($PROJECT_ARR->items[0]->PROPERTY_STEP_VALUE < 4){
				$this->redirect('/project/step3/');
			}
			$count = $PROJECT_ARR->pageData->totalItemsCount;
			if($count > 0){
				$item = $PROJECT_ARR->items[0];
				$id_plan = $item->PROPERTY_ID_PLAN_VALUE;
				$id_plan_option = $item->PROPERTY_ID_OPTION_PLAN_VALUE;
				$PLAN_SAVE = $planModel->getPlanList(['=ID' => Array($id_plan, $id_plan_option)], $catalogSort, 1, false, 2);	
			};
		}
		else{
			
		};

		$filterParams = $this->getAllParams();	
		// $catalogFilter = $this->_model->prepareFilter($filterParams);
		$catalogFilter = ['=PROPERTY_SHOW_STEP' => false, '=PROPERTY_SHOW_STEP_1_VALUE' => 'Y'];
		$pageTitle = $this->_model->getPageTitle($validFilterValues);
		

		//получим массив планировок
		$catalogSort["SORT"] = "ASC";	
		$PLAN_ARR = $planModel->getPlanList(['=ID' => $id_plan], $catalogSort, $this->getParam("page", 1), false, 999);
		$starting_price = ($PROJECT_ARR->items[0]->PROPERTY_PRICE_SQUARE_VALUE * $PLAN_ARR->items[0]->PROPERTY_AREA) + ($PROJECT_ARR->items[0]->PROPERTY_PRICE_SQUARE_VALUE * $PLAN_ARR->items[0]->PROPERTY_AREA)/ 100 * 21;

		$this->view->assign([
            "pageTitle" => $pageTitle,
            "itemList"  => $PLAN_ARR->items,
			"projectData" => $PROJECT_ARR->items[0],
			"starting_price" => $starting_price,
			"uploadItemList"  => $upload_result->items,
			"url"		=> '/project/step5/',
			"nextstep"		=> 'savestep4',
			"nextstep_d"	=> 'К настройкам <br/>заказа',
			"item_save"	=> $PLAN_SAVE->items,
            'paginator' => EHelper::getPaginator($result->pageData->totalItemsCount, $result->pageData->current, $result->pageData->size),
        ]);
		
		
		/*$APP = Zend_Registry::get('BX_APPLICATION');
		Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'project-page');
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'project');
		Zend_Registry::get('BX_APPLICATION')->SetTitle(("Создание семьи"));
		$planModel = new Sibirix_Model_Plan();
		$userModel = new Sibirix_Model_User();
		$step4Form = new Sibirix_Form_AddServiceFamily();
		$this->view->step4Form   = $step4Form;
		$save_arr = unserialize($APP->get_cookie("selectPlanItem"));
		if(!isset($save_arr['step2']) || !isset($save_arr['step1'])){
			$this->redirect('/project/step2');
		}
		else{
			$filter = ['=ID'=>[$save_arr['step1'], $save_arr['step2']]];
			$result = $planModel->getPlanList($filter);
			$item_save = $result;
		};
		
		$this->view->assign([
            "pageTitle" => $pageTitle,
			"item_save"	=> $item_save->items,
			"itemList"  => $result->items,
			"url"		=> '/project/step5/',
			"nextstep"		=> 'savestep4',
			"nextstep_d"	=> 'К настройке <br/>заказа',
            'paginator' => EHelper::getPaginator($result->pageData->totalItemsCount, $result->pageData->current, $result->pageData->size),
        ]);*/
    }
	
	public function savestep4Action(){
		/*========================================
			Сохранение
			Принцип работы: Если пользователь не авторизован, то храним в куку первый и второй шаг,
			далее он должен будет авторизоваться и первый, второй шаг переносим в бд, следущие шаги также сохраняются в бд
			Если авторизован просто храним в бд
		=========================================*/
		$defaultValues = $this->getAllParams();

		$planModel = new Sibirix_Model_Plan();
		$projectModel = new Sibirix_Model_Project();
		$userModel = new Sibirix_Model_User();
		//$id = $this->getParam('id');
		
		//если авториован
		
		if(Sibirix_Model_User::isAuthorized()){
			$id_user = $userModel->getId();
			//получаем планировку
			$result = $planModel->getPlanList(['=ID' => $id], [], 1, false, 1);
			$item = $result->items[0];
	
				$fields = array();
				for($i = 0; $i < count($defaultValues['age_man']) - 1; $i++){
					$fields["PROPERTY_VALUES"]["MAN_FAMILY"][$i] = $defaultValues['age_man'][$i] .'&&' . $defaultValues['place_man'][$i];
				};
				for($i = 0; $i < count($defaultValues['age_children']) - 1; $i++){
					$fields["PROPERTY_VALUES"]["CHILDREN_FAMILY"][$i] = $defaultValues['age_children'][$i] .'&&' . $defaultValues['place_children'][$i];
				};
				for($i = 0; $i < count($defaultValues['animal']) - 1; $i++){$fields["PROPERTY_VALUES"]["ANIMAL_FAMILY"][$i] = $defaultValues['animal'][$i];};
			
			//провери есть ли уже добавленные заказы
			$result_project = $projectModel->getProjectList(['=PROPERTY_USER' => $id_user, '=PROPERTY_STATUS' => PROJECT_STATUS_ADDED], [], 1, false, 1);
			$count = $result_project->pageData->totalItemsCount;
			if($count == 0){
				//адд
				if($projectModel -> addProject($fields)){
				};
			}
			else{
				//упдате
				$step = $result_project->items[0]->PROPERTY_STEP_VALUE;
				if($step < 5){
					$fields["PROPERTY_VALUES"]["STEP"] = 5;
				};
				CIBlockElement::SetPropertyValuesEx($result_project->items[0]->ID, IB_ORDER_PROJECT, $fields['PROPERTY_VALUES']);
			};
		}
		else{
			/*$APP = Zend_Registry::get('BX_APPLICATION');
			$APP->set_cookie("selectPlanItem", "", time()-3600);
			$APP->set_cookie("selectPlanItem", $id, time()+3600 * 24 * 3);*/
		};
		$this->_helper->json(['result' => 1, "response" => true]);
	}
	
	public function step5Action() {
		$APP = Zend_Registry::get('BX_APPLICATION');
		Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'project-page');
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'project');
		Zend_Registry::get('BX_APPLICATION')->SetTitle(("Настройки заказа"));
		$planModel = new Sibirix_Model_Plan();
		$projectModel = new Sibirix_Model_Project();
		$userModel = new Sibirix_Model_User();
		
		/*==========================================
			Узнаем есть ли у пользователя уже заказ
			Если есть берем
		=========================================*/
		$catalogSort = ['ASC'];
		if(Sibirix_Model_User::isAuthorized()){
			$id_user = $userModel->getId();
			//заказ
			$PROJECT_ARR = $projectModel->getProjectList(['=PROPERTY_USER' => $id_user, '=PROPERTY_STATUS' => PROJECT_STATUS_ADDED], [], 1, false, 1);
			$id_user = $userModel->getId();
			//заказ
			$PROJECT_ARR = $projectModel->getProjectList(['=PROPERTY_USER' => $id_user, '=PROPERTY_STATUS' => PROJECT_STATUS_ADDED], [], 1, false, 1);
			//если не пройден 1 шаг
			if($PROJECT_ARR->items[0]->PROPERTY_STEP_VALUE < 5){
				$this->redirect('/project/step4/');
			}
			$count = $PROJECT_ARR->pageData->totalItemsCount;
			if($count > 0){
				$item = $PROJECT_ARR->items[0];
				$id_plan = $item->PROPERTY_ID_PLAN_VALUE;
				$id_plan_option = $item->PROPERTY_ID_OPTION_PLAN_VALUE;
				$PLAN_SAVE = $planModel->getPlanList(['=ID' => Array($id_plan, $id_plan_option)], $catalogSort, 1, false, 2);	
			};
		}
		else{
			
		};
		/*$save_arr = unserialize($APP->get_cookie("selectPlanItem"));
		if(!isset($save_arr['step2']) || !isset($save_arr['step1'])){
			$this->redirect('/project/step2');
			exit;
		}
		else{
			$filter = ['=ID'=>[$save_arr['step1'], $save_arr['step2']]];
			$result = $planModel->getPlanList($filter);
			$item_save = $result;
		};
		$step4Form = new Sibirix_Form_AddServiceFamily();
		$this->view->step4Form   = $step4Form;*/
		//получим массив планировок
		
		$result = $planModel->getPlanList(['=ID' => $id_plan], $catalogSort, $this->getParam("page", 1), false, 999);
		if(empty($PROJECT_ARR->items[0]->PROPERTY_PRICE_SQUARE_VALUE)){
			
		}
		$starting_price = ($PROJECT_ARR->items[0]->PROPERTY_PRICE_SQUARE_VALUE * $result->items[0]->PROPERTY_AREA) + ($PROJECT_ARR->items[0]->PROPERTY_PRICE_SQUARE_VALUE * $result->items[0]->PROPERTY_AREA)/ 100 * 21;	
		$this->view->assign([
            "pageTitle" => $pageTitle,
			"projectData" => $PROJECT_ARR->items[0],
			"item_save"	=> $PLAN_SAVE->items,
			"url"		=> '/basket',
			"starting_price" => $starting_price,
			"layout"	=> $result->items[0]->PROPERTY_AREA,
			"nextstep"		=> 'savestep5',
			"nextstep_d"	=> 'К оплате',
            'paginator' => EHelper::getPaginator($result->pageData->totalItemsCount, $result->pageData->current, $result->pageData->size),
        ]);
    }
	
	public function savestep5Action(){
		/*========================================
			Сохранение
			Принцип работы: Если пользователь не авторизован, то храним в куку первый и второй шаг,
			далее он должен будет авторизоваться и первый, второй шаг переносим в бд, следущие шаги также сохраняются в бд
			Если авторизован просто храним в бд
		=========================================*/
		$defaultValues = $this->getAllParams();

		$planModel = new Sibirix_Model_Plan();
		$projectModel = new Sibirix_Model_Project();
		$userModel = new Sibirix_Model_User();
		$id = $this->getParam('id');
		//если авториован
		
		if(Sibirix_Model_User::isAuthorized()){
			$id_user = $userModel->getId();
			//получаем планировку
			$result = $planModel->getPlanList(['=ID' => $id], [], 1, false, 1);
			$item = $result->items[0];
				
				$fields = array();
				$fields["PROPERTY_VALUES"]["SUGGES"] = $defaultValues['comments'];
				$fields["PROPERTY_VALUES"]["PRICE_SQUARE"] = $defaultValues['price_square'];
				$fields["PROPERTY_VALUES"]["TIME"] = $defaultValues['slider_time'];
			
			//провери есть ли уже добавленные заказы
			$result_project = $projectModel->getProjectList(['=PROPERTY_USER' => $id_user, '=PROPERTY_STATUS' => PROJECT_STATUS_ADDED], [], 1, false, 1);
			$count = $result_project->pageData->totalItemsCount;
			if($count == 0){
				//адд
				if($projectModel -> addProject($fields)){
				};
			}
			else{
				//упдате
				$step = $result_project->items[0]->PROPERTY_STEP_VALUE;
				if($step < 5){
					$fields["PROPERTY_VALUES"]["STEP"] = 5;
				};
				CIBlockElement::SetPropertyValuesEx($result_project->items[0]->ID, IB_ORDER_PROJECT, $fields['PROPERTY_VALUES']);
			};
		}
		else{
			$APP = Zend_Registry::get('BX_APPLICATION');
			$APP->set_cookie("selectPlanItem", "", time()-3600);
			$APP->set_cookie("selectPlanItem", $id, time()+3600 * 24 * 3);
		};
		$this->_helper->json(['result' => 1, "response" => true]);
		
	}
	public function planlistAction(){
		$ajaxContext = $this->_helper->getHelper('SibirixAjaxContext');
		$ajaxContext->addActionContext('planlist', 'html')
		->initContext();
		$planModel = new Sibirix_Model_Plan();
		$planoptionModel = new Sibirix_Model_PlanOption();
		$projectModel = new Sibirix_Model_Project();
		$userModel = new Sibirix_Model_User();
		$id = $this->getParam('id_plan');
		
		$result = $planoptionModel->getPlanList(['=PROPERTY_PLAN_FLAT' => $id], [], 1, false, 999);
		foreach($result->items as $plan){
				if (!empty($plan->PROPERTY_IMAGES_VALUE)) {
					$image = Resizer::resizeImage($plan->PROPERTY_IMAGES_VALUE, 'COMPLEX_LIST');
				} else {
					$image = '/local/images/proomer2.png';
				}
				$plan->PROPERTY_IMAGES_VALUE = $image;
			}
		$this->view->assign([
			"planList" 	=> $result,
        ]);
	}
	public function projectcartAction(){

		$APP = Zend_Registry::get('BX_APPLICATION');

		$ajaxContext = $this->_helper->getHelper('SibirixAjaxContext');
		$ajaxContext->addActionContext('projectcart', 'html')
		->initContext();
		
		$userModel = new Sibirix_Model_User();
		/*==========================================
			Авторизован?
		=========================================*/
		if(Sibirix_Model_User::isAuthorized()){
			$auth = true;
		}
		else{
			$auth = false;
		}
		$planModel = new Sibirix_Model_Plan();
		$planoptionModel = new Sibirix_Model_PlanOption();
		$projectModel = new Sibirix_Model_Project();
		$userModel = new Sibirix_Model_User();
		
		$id_user = $userModel->getId();
		
		$id_items = array();
		
		if($this->getParam('type') == 'apartment'){
			$id_plan = $this->getParam('id_plan');
			$id_option = '';
			$filter = ['=ID'=>$id_plan];		
			$ARR_PLAN = $planModel->getPlanList($filter);
			foreach($ARR_PLAN->items as $plan){
				if (!empty($plan->PROPERTY_IMAGES_VALUE)) {
					$image = Resizer::resizeImage($plan->PROPERTY_IMAGES_VALUE, 'COMPLEX_LIST');
				} else {
					$image = '/local/images/proomer2.png';
				}
				$plan->PLAN_IMAGE = $image;
			}

		}
		else if($this->getParam('type') == 'plan'){
			$id_plan = $this->getParam('id_plan');
			$id_option = $this->getParam('id_plan_option');
			$catalogSort["SORT"] = "ASC";	
		
			$ARR_PLAN = $planModel->getPlanList(['=ID'=>$id_plan], $catalogSort, 1, false, 1);
			$ARR_PLAN_OPTION = $planoptionModel->getPlanList(['=ID'=>$id_option], $catalogSort, 1, false, 1);
			foreach($ARR_PLAN->items as $plan){
				if (!empty($plan->PROPERTY_IMAGES_VALUE)) {
					$image = Resizer::resizeImage($plan->PROPERTY_IMAGES_VALUE, 'COMPLEX_LIST');
				} else {
					$image = '/local/images/proomer2.png';
				}
				$plan->PLAN_IMAGE = $image;
			}
			foreach($ARR_PLAN_OPTION->items as $plan){
				if (!empty($plan->PROPERTY_IMAGES_VALUE)) {
					$image = Resizer::resizeImage($plan->PROPERTY_IMAGES_VALUE, 'COMPLEX_LIST');
				} else {
					$image = '/local/images/proomer2.png';
				}
				$plan->PLAN_IMAGE = $image;
			}
			
			//$APP->set_cookie("selectPlanItem", "", time()-3600);
			//$APP->set_cookie("selectPlanItem", serialize($save_arr), time()+3600 * 24 * 3);
		}
		else{
			return;
		};
			
		if(isset($id_plan) && !empty($id_option)){
			$url = '/project/step2/';
			$nextstep = 'savestep1';
			$nextstep_d =	'К параметрам <br/>дизайн-проекта';
			if(isset($id_option) && !empty($id_option)){
				$access = true;
				$url = '/project/step2/';
				$nextstep = 'savestep1';
				$nextstep_d =	'К параметрам <br/>дизайн-проекта';
			};
		}
		else{
			$url = '/basket';
			$nextstep = 'savestep3';
			$nextstep_d = 'К оплате';
		};

		$result_project = $projectModel->getProjectList(['=PROPERTY_USER' => $id_user, '=PROPERTY_STATUS' => PROJECT_STATUS_ADDED], [], 1, false, 1);
		$starting_price = ($result_project->items[0]->PROPERTY_PRICE_SQUARE_VALUE * $result->items[0]->PROPERTY_AREA) + ($result_project->items[0]->PROPERTY_PRICE_SQUARE_VALUE * $result->items[0]->PROPERTY_AREA)/ 100 * 21;	
        $this->view->assign([
            "pageTitle" => $pageTitle,
            "filter"    => $filter,
			"auth" => $auth,
			"access" => $access,
            "ARR_PLAN_AJAX" => $ARR_PLAN->items,
			"ARR_PLAN_OPTION_AJAX" => $ARR_PLAN_OPTION->items,
			"projectData" => $result_project->items[0],
			"starting_price"  => $starting_price,
			"url"		=> $url,
			"nextstep"		=> $nextstep,
			"nextstep_d"	=> $nextstep_d,
            'paginator' => EHelper::getPaginator($result->pageData->totalItemsCount, $result->pageData->current, $result->pageData->size),
        ]);
	}

    public function selectplanAction() {
		Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'service-page');
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'service');
		Zend_Registry::get('BX_APPLICATION')->SetTitle(("Выбор планировки"));
		$ajaxContext = $this->_helper->getHelper('SibirixAjaxContext');
		$ajaxContext->addActionContext('selectplan', 'html')
		->initContext();
		$planModel = new Sibirix_Model_Plan();
		$id_item = $this->getParam('id_item');
        $result = $planModel->getPlanList(['=ID'=>$id_item], [], 1, false, 1);
        $this->view->assign([
            "pageTitle" => $pageTitle,
            "filter"    => $filter,
            "test"  => $result->items,
            'paginator' => EHelper::getPaginator($result->pageData->totalItemsCount, $result->pageData->current, $result->pageData->size),
        ]);
    }

	public function showplanAction() {
		Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'service-page');
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'service');
		Zend_Registry::get('BX_APPLICATION')->SetTitle(("Варианты планировок"));
		$step4Form = new Sibirix_Form_AddDesignStep4();
		$this->view->step4Form   = $step4Form;
    }
	
	public function listAction() {

    }
	
	/*public function uploadplanAction() {
        $upload = new Zend_File_Transfer();
		$planModel = new Sibirix_Model_Plan();
		$projectModel = new Sibirix_Model_Project();
		$userModel = new Sibirix_Model_User();
        $fileModel = new Sibirix_Model_Files();
        $fileType   = $this->getParam("fileType");
        $resizeType = $this->getParam("resizeType");
		$id_user = $userModel->getId();
        $fields = array(
            "ID" => $designId,
            $fileType => $upload->getFileInfo()["file"]
        );

        $regep = '/^PROPERTY_(.*)$/';
        if (preg_match($regep, $fileType, $matches)) {
            $fields["PROPERTY_VALUES"][$matches[1]] = $fields[$fileType];
            unset($fields[$fileType]);
        }
		$fields['PROPERTY_USER'] = $id_user;
        $accessExtensions = "";
        switch ($fileType) {
            case "DETAIL_PICTURE":
            case "PROPERTY_PLAN_FLAT":
                $accessExtensions = "jpg,png,jpeg,gif,bmp";
                break;
            case "PROPERTY_DOCUMENTS":
                $accessExtensions = "doc,docx,xls,xlsx,pdf,rar,tar,zip";
                break;
            default:
                $this->_helper->json(['result' => false, "response" => "Invalid file type"]);
                break;
        }

        if (!$fileModel->checkFile($upload->getFileInfo()["file"], $accessExtensions)) {
            $this->_helper->json(['result' => false, "response" => "Invalid file type"]);
        }

        $editResult = $projectModel->editProject($fields);

        $newFile = false;
        if ($editResult) {
            $newFile = $this->_model->select([$fileType], true)->getElement($designId);
        }

        if ($this->getParam("files") == "true") {
            $response = array();
            if ($editResult) {
                $fileData = EHelper::getFileData($newFile->$fileType);
                $response = array(
                    "valueId" => $newFile->$fileType,
                    "fileType" => GetFileExtension($fileData["FILE_NAME"]),
                    "fileName" => $fileData["ORIGINAL_NAME"],
                    "fileSize" => round($fileData["FILE_SIZE"]/1024) . " Кб",
                );
            }
        } else {
            $response = array();
            if ($editResult) {
                if (empty($resizeType)) {
                    $resizeType = "DROPZONE_MAIN_PHOTO";
                }
                $response["imageSrc"] =  Resizer::resizeImage($newFile->$fileType, $resizeType);
            }

        }

        $this->_helper->json(['result' => (bool)$editResult, "response" => $response]);
    }*/
	
	    /**
     * Загрузка изображений с dropzone "налету"
     */
    public function uploadplanAction() {
        $upload = new Zend_File_Transfer();
		$planModel = new Sibirix_Model_Plan();
		$projectModel = new Sibirix_Model_Project();
		$userModel = new Sibirix_Model_User();
        $designId = $this->getParam("designId");
        $fileModel = new Sibirix_Model_Files();

        $fileType   = $this->getParam("fileType");
        $resizeType = $this->getParam("resizeType");

        $fields = array(
            "ID" => $designId,
            $fileType => $upload->getFileInfo()["file"]
        );

        $regep = '/^PROPERTY_(.*)$/';
        if (preg_match($regep, $fileType, $matches)) {
            $fields["PROPERTY_VALUES"][$matches[1]] = $fields[$fileType];
            unset($fields[$fileType]);
        }
		
		$id_user = $userModel->getId();
		
		$fields['PROPERTY_VALUES']['USER'] = $id_user;
		$fields['PROPERTY_VALUES']['STATUS'] = PROJECT_STATUS_MODERATION;
        $accessExtensions = "";
        switch ($fileType) {
            case "DETAIL_PICTURE":
			case "PREVIEW_PICTURE":
            case "PROPERTY_PLAN_FLAT":
                $accessExtensions = "jpg,png,jpeg,gif,bmp";
                break;
            case "PROPERTY_DOCUMENTS":
                $accessExtensions = "doc,docx,xls,xlsx,pdf,rar,tar,zip";
                break;
            default:
                $this->_helper->json(['result' => false, "response" => "Invalid file type"]);
                break;
        }

        if (!$fileModel->checkFile($upload->getFileInfo()["file"], $accessExtensions)) {
            $this->_helper->json(['result' => false, "response" => "Invalid file type"]);
        }
        $editResult = $projectModel->editProject($fields);

        $newFile = false;
        if ($editResult) {
            $newFile = $this->_model->select([$fileType], true)->getElement($designId);
        }

        if ($this->getParam("files") == "true") {
            $response = array();
            if ($editResult) {
                $fileData = EHelper::getFileData($newFile->$fileType);
                $response = array(
                    "valueId" => $newFile->$fileType,
                    "fileType" => GetFileExtension($fileData["FILE_NAME"]),
                    "fileName" => $fileData["ORIGINAL_NAME"],
                    "fileSize" => round($fileData["FILE_SIZE"]/1024) . " Кб",
                );
            }
        } else {
            $response = array();
            if ($editResult) {
                if (empty($resizeType)) {
                    $resizeType = "DROPZONE_MAIN_PHOTO";
                }
                $response["imageSrc"] =  Resizer::resizeImage($newFile->$fileType, $resizeType);
            }

        }

        $this->_helper->json(['result' => (bool)$editResult, "response" => $response]);
    }
	
	public function selectdesignAction() {

    }
	public function showdesignAction() {

    }
	public function orderAction() {

    }
	public function superManAction() {
        $form = new Sibirix_Form_SuperMan();
		$model = new Sibirix_Model_SuperMan();
        $formData = $this->getAllParams();
		
        if ($form->isValid($formData)) {
            $validData = $form->getValues();

            if (!$form->antiBotCheck()) {
                $this->_response->stopBitrix(true);
                $this->_helper->viewRenderer->setNoRender();
                return false;
            }
			$validData['city'] = $formData['city'];

            $addResult = $model->add($validData);

            if ($addResult) {
                $notification = new Sibirix_Model_Notification();
                $emailTo = Settings::getOption("FEEDBACK_EMAIL_TO");
                $titleMail = "Вызов замерщика";

                $notification->sendFeedback($addResult, $validData, 'alexme777@yandex.ru', $titleMail);
            } else {
                $form->setFieldErrors("name", "Ошибка добавления");
            }

        } else {
            $form->getFieldsErrors();
        }

        $this->_helper->json(['success' => !$form->issetError(), 'errorFields' => $form->formErrors]);
    }
}