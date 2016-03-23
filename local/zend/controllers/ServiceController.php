<?

/**
 * Class FeedbackController
 */
class ServiceController extends Sibirix_Controller {

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
		Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'service-page index');
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'service');
		Zend_Registry::get('BX_APPLICATION')->SetTitle(("Выбор планировки"));

		$planModel = new Sibirix_Model_Plan();
		$designModel = new Sibirix_Model_Design();
        $catalogSort["SORT"] = "ASC";	
        $filter = new Sibirix_Form_FilterDesign();	
        $filterParams = $this->getAllParams();	
        $catalogFilter = $this->_model->prepareFilter($filterParams);
        $pageTitle     = $this->_model->getPageTitle($validFilterValues);
		if($this->getParam('page')){
			$limit = $this->getParam('page') * 10;
		}
		else{
			$limit = 10;
		}
		
        $result = $planModel->getPlanList($catalogFilter, $catalogSort, 1, false, $limit);
		$option_id = $this->getParam('options');
		$designs_id = $this->getParam('designs');
		$resultOptions = array();
		if($option_id){
			$resultOptions = $planModel->getPlanOptionList(['=PROPERTY_OPTIONS'=>$option_id], $catalogSort, 1, false, 999);
		};
		if($designs_id){
			$resultDesigns = $designModel->getDesignList(['=PROPERTY_PLAN'=>$designs_id], $catalogSort, 1, false, 999);
		};
		/*?><pre><?echo print_r($result)?></pre><?
		exit;*/
        $this->view->assign([
            "pageTitle" => $pageTitle,
            "filter"    => $filter,
            "itemList"  => $result->items,
			"itemListPlan"  => $resultOptions->items,
			"itemListDesign"  => $resultDesigns->items,
            'paginator' => EHelper::getPaginator($result->pageData->totalItemsCount, $result->pageData->current, $result->pageData->size),
        ]);
    }
	
	public function step1Action() {
		$APP = Zend_Registry::get('BX_APPLICATION');
		Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'service-page');
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'service');
		Zend_Registry::get('BX_APPLICATION')->SetTitle(("Выбор планировки"));
		
		$planModel = new Sibirix_Model_Plan();
		$userModel = new Sibirix_Model_User();
		
		//если куку
		$item_save = array();
		$selected_plan = array();
		$save_arr = unserialize($APP->get_cookie("selectPlanItem"));
		if(isset($save_arr)){
			$selected_plan['step1'] = $save_arr['step1'];
			$selected_plan['step2'] = $save_arr['step2'];
			$selected_plan['step3'] = $save_arr['step3'];
		}
		$item_save = array();
		
		if($selected_plan && $selected_plan['step1']){
			$filter = ['=ID'=>[$save_arr['step1'], $save_arr['step2']]];
			$result = $planModel->getPlanList($filter);
			$item_save = $result;
		}
	
        $catalogSort["SORT"] = "ASC";	
        $filter = new Sibirix_Form_FilterDesign();	
        $filterParams = $this->getAllParams();	
       // $catalogFilter = $this->_model->prepareFilter($filterParams);
		$catalogFilter = ['=PROPERTY_OPTIONS' => false];
        $pageTitle = $this->_model->getPageTitle($validFilterValues);
		$id_item = $this->getParam('id_item');
		
		//получим массив планировок
		$result = $planModel->getPlanList($catalogFilter, $catalogSort, $this->getParam("page", 1), false, 999);
		//получим массив загруженных файлов
		$upload_result = $planModel->getPlanList(['=PROPERTY_USER' => 83], $catalogSort, 1, false, 999);
		$planModel->getImageData($upload_result->items, 'DETAIL_PICTURE');

        $this->view->assign([
            "pageTitle" => $pageTitle,
            "filter"    => $filter,
            "itemList"  => $result->items,
			"uploadItemList"  => $upload_result->items,
			"nextstep"		=> '/service/step2',
			"nextstep_d"	=> 'К вариантам <br/>планировок',
			"item_save"	=> $item_save->items,
            'paginator' => EHelper::getPaginator($result->pageData->totalItemsCount, $result->pageData->current, $result->pageData->size),
        ]);
    }
	
	public function step2Action() {
		$APP = Zend_Registry::get('BX_APPLICATION');
		Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'service-page');
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'service');
		Zend_Registry::get('BX_APPLICATION')->SetTitle(("Выбор планировки"));
		$planModel = new Sibirix_Model_Plan();
		$selected_plan = array();

				//если куку
		$item_save = array();
		$selected_plan = array();
		$save_arr = unserialize($APP->get_cookie("selectPlanItem"));
		if(isset($save_arr)){
			$selected_plan['step1'] = $save_arr['step1'];
			$selected_plan['step2'] = $save_arr['step2'];
			$selected_plan['step3'] = $save_arr['step3'];
		}

		if(!isset($selected_plan['step1'])){
			$this->redirect('/service/step1');
		}
		else{
			$filter = ['=ID'=>[$selected_plan['step1'], $selected_plan['step2']]];
			
			$result = $planModel->getPlanList($filter);
			$item_save = $result;
		};
		
        $catalogSort["SORT"] = "ASC";	
        $filter = new Sibirix_Form_FilterDesign();	
        $pageTitle = $this->_model->getPageTitle($validFilterValues);
		$id_item = $this->getParam('id_item');

		$result = $planModel->getPlanList(['=PROPERTY_OPTIONS' => $save_arr['step1']], $catalogSort, $this->getParam("page", 1), false, 999);

	
        $this->view->assign([
            "pageTitle" => $pageTitle,
            "filter"    => $filter,
            "itemList"  => $result->items,
			"item_save"	=> $item_save->items,
			"nextstep"		=> '/service/step3',
			"nextstep_d"	=> 'К выбору <br/>дизайна',
            'paginator' => EHelper::getPaginator($result->pageData->totalItemsCount, $result->pageData->current, $result->pageData->size),
        ]);
    }
	
	public function step3Action() {
		$APP = Zend_Registry::get('BX_APPLICATION');
		Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'service-page');
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'service');
		Zend_Registry::get('BX_APPLICATION')->SetTitle(("Ваши предпочтения"));
		$planModel = new Sibirix_Model_Plan();
		$userModel = new Sibirix_Model_User();
        $catalogSort["SORT"] = "ASC";
		$save_arr = unserialize($APP->get_cookie("selectPlanItem"));
		if(!isset($save_arr['step2']) || !isset($save_arr['step1'])){
			$this->redirect('/service/step2');
		}
		else{
			$filter = ['=ID'=>[$save_arr['step1'], $save_arr['step2']]];
			$result = $planModel->getPlanList($filter);
			$item_save = $result;
		};
        $filter = new Sibirix_Form_FilterDesign();	
        $filterParams = $this->getAllParams();	
        $catalogFilter = $this->_model->prepareFilter($filterParams);
        $pageTitle = $this->_model->getPageTitle($validFilterValues);
		$id_item = $this->getParam('id_item');
		
		//получим массив планировок
		//$result = $planModel->getPlanList($catalogFilter, $catalogSort, $this->getParam("page", 1), false, 999);
		//получим массив загруженных файлов
		//$upload_result = $planModel->getPlanList(['=PROPERTY_USER' => 1], $catalogSort, 1, false, 999);
		//$planModel->getImageData($upload_result->items, 'DETAIL_PICTURE');
		/*?><pre><?echo print_r($result)?></pre><?
		exit;*/
        $this->view->assign([
            "pageTitle" => $pageTitle,
            "filter"    => $filter,
            "itemList"  => $result->items,
			"item_save"	=> $item_save->items,
			"uploadItemList"  => $upload_result->items,
			"nextstep"		=> '/service/step4',
			"nextstep_d"	=> 'К составу <br/>семьи',
            'paginator' => EHelper::getPaginator($result->pageData->totalItemsCount, $result->pageData->current, $result->pageData->size),
        ]);
    }
	
	public function step4Action() {
		$APP = Zend_Registry::get('BX_APPLICATION');
		Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'service-page');
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'service');
		Zend_Registry::get('BX_APPLICATION')->SetTitle(("Создание семьи"));
		$planModel = new Sibirix_Model_Plan();
		$userModel = new Sibirix_Model_User();
		$step4Form = new Sibirix_Form_AddServiceFamily();
		$this->view->step4Form   = $step4Form;
		$save_arr = unserialize($APP->get_cookie("selectPlanItem"));
		if(!isset($save_arr['step2']) || !isset($save_arr['step1'])){
			$this->redirect('/service/step2');
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
			"nextstep"		=> '/service/step5',
			"nextstep_d"	=> 'К настройке <br/>заказа',
            'paginator' => EHelper::getPaginator($result->pageData->totalItemsCount, $result->pageData->current, $result->pageData->size),
        ]);
    }
	
	public function step5Action() {
		$APP = Zend_Registry::get('BX_APPLICATION');
		Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'service-page');
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'service');
		Zend_Registry::get('BX_APPLICATION')->SetTitle(("Настройки заказа"));
		$planModel = new Sibirix_Model_Plan();
		$userModel = new Sibirix_Model_User();
		$save_arr = unserialize($APP->get_cookie("selectPlanItem"));
		if(!isset($save_arr['step2']) || !isset($save_arr['step1'])){
			$this->redirect('/service/step2');
			exit;
		}
		else{
			$filter = ['=ID'=>[$save_arr['step1'], $save_arr['step2']]];
			$result = $planModel->getPlanList($filter);
			$item_save = $result;
		};
		$step4Form = new Sibirix_Form_AddServiceFamily();
		$this->view->step4Form   = $step4Form;
		$this->view->assign([
            "pageTitle" => $pageTitle,
			"item_save"	=> $item_save->items,
			"nextstep"		=> '/basket',
			"nextstep_d"	=> 'К оплате',
            'paginator' => EHelper::getPaginator($result->pageData->totalItemsCount, $result->pageData->current, $result->pageData->size),
        ]);
    }
	
	public function projectcartAction(){
		$APP = Zend_Registry::get('BX_APPLICATION');
		$save_arr = unserialize($APP->get_cookie("selectPlanItem"));

		$ajaxContext = $this->_helper->getHelper('SibirixAjaxContext');
		$ajaxContext->addActionContext('projectcart', 'html')
		->initContext();
		$id_items = array();
		$planModel = new Sibirix_Model_Plan();

		if($this->getParam('type') == '/service/step1'){
			$save_arr['step1'] = $this->getParam('id_item');
			unset($save_arr['step2']);
			$filter = ['=ID'=>[$save_arr['step1']]];		
			$result = $planModel->getPlanList($filter);
			
			//setcookie("selectPlanItem", serialize($id_items['step1'] = 111), time()+3600 * 24 * 3);
			//unset($_COOKIE["selectPlanItem"]);\
			$APP->set_cookie("selectPlanItem", "", time()-3600);
			$APP->set_cookie("selectPlanItem", serialize($save_arr), time()+3600 * 24 * 3);
		}
		else if($this->getParam('type') == '/service/step2' && isset($save_arr['step1'])){


			$save_arr['step2'] = $this->getParam('id_item');

			$filter = ['=ID'=>[$save_arr['step1'], $save_arr['step2']]];
			$result = $planModel->getPlanList($filter);
		
			$APP->set_cookie("selectPlanItem", "", time()-3600);
			$APP->set_cookie("selectPlanItem", serialize($save_arr), time()+3600 * 24 * 3);
			

		}
		else if($this->getParam('type') == '/service/step3' && isset($_COOKIE['selectPlanItem']['step2']) && isset($_COOKIE['selectPlanItem']['step1'])){
			$id_items['step3'] = $this->getParam('id_item');
			$filter = ['=ID'=>[$_COOKIE['selectPlanItem']['step1'], $_COOKIE['selectPlanItem']['step2'], $id_items['step3']]];
			$result = $planModel->getPlanList($filter);
			setcookie("selectPlanItem", '5555', time()+3600 * 24 * 3);
		}
		else{
			return;
		};
			
		if(isset($save_arr['step1'])){
			$nextstep = '/service/step2';
			$nextstep_d =	'К вариантам <br/>планировок';
			if(isset($save_arr['step2'])){
				$nextstep = '/service/step3';
				$nextstep_d = 'К выбору <br/>дизайна';
				if(isset($save_arr['step3'])){
					$nextstep = '/service/step4';
					$nextstep_d = 'К составу <br/>семьи';
					if(isset($save_arr['step4'])){
						$nextstep = '/service/step5';
						$nextstep_d = 'К настройке <br/>заказа';
						if(isset($save_arr['step5'])){
							$nextstep = '/basket';
							$nextstep_d = 'К оплате';
						};
					};
				};
			};
		};
        $this->view->assign([
            "pageTitle" => $pageTitle,
            "filter"    => $filter,
            "item_ajax"  => $result->items,
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
	
	public function listplanAction() {

    }
	
	public function uploadplanAction() {
		$planModel = new Sibirix_Model_Plan();
		$userModel = new Sibirix_Model_User();
		//если не авторизованы
		if(!Sibirix_Model_User::isAuthorized()){return false;};
		//получим id пользователя
		$user_id = $userModel->getId();
		//проверим есть ли у пользователя уже загруженная планировка со статусом "PLAN_STATUS_MODERATION"
		$check = $this->_model->select('ID')->where(["=PROPERTY_USER" => $user_id, "=PROPERTY_STATUS" => PLAN_STATUS_MODERATION])->getElements();

		if(count($check) > 0){
			$this->_helper->json(['result' => false, "response" => "Invalid file type"]);
			return;
		}
		$upload = new Zend_File_Transfer();
        $fileModel = new Sibirix_Model_Files();

        $fileType   = $this->getParam("fileType");
        $resizeType = $this->getParam("resizeType");

        $fields["NAME"] = 'Без названия';
        $fields["PROPERTY_VALUES"]["USER"] = $user_id;
		$fields["PROPERTY_VALUES"]["STATUS"] = 61;
        $fields[$fileType] = $upload->getFileInfo()["file"];
	
       /* $regep = '/^PROPERTY_(.*)$/';
        if (preg_match($regep, $fileType, $matches)) {
            $fields["PROPERTY_VALUES"][$matches[1]] = $fields[$fileType];
            unset($fields[$fileType]);
        }*/

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

        $editResult = $planModel->addPlan($fields);
        $newFile = false;
        if ($editResult) {
            $newFile = $this->_model->select([$fileType], true)->getElement($editResult);
        }

        if ($newFile) {
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
		 $form = new Sibirix_Form_Feedback();
        $formData = $this->getAllParams();

        if ($form->isValid($formData)) {
            $validData = $form->getValues();

            if (!$form->antiBotCheck()) {
                $this->_response->stopBitrix(true);
                $this->_helper->viewRenderer->setNoRender();
                return false;
            }

            $addResult = $this->_model->add($validData);

            if ($addResult) {
                $notification = new Sibirix_Model_Notification();
                $emailTo = Settings::getOption("FEEDBACK_EMAIL_TO");
                $titleMail = "Новое сообщение";

                $notification->sendFeedback($addResult, $validData, $emailTo, $titleMail);
            } else {
                $form->setFieldErrors("name", "Ошибка добавления");
            }

        } else {
            $form->getFieldsErrors();
        }

        $this->_helper->json(['success' => true, 'errorFields' => []]);
    }
}