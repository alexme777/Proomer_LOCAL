<?

/**
 * Class DesignController
 */
class DesignController extends Sibirix_Controller {
    /**
     * @var Sibirix_Model_Design
     */
    protected $_model;

    public function init() {
        /**
         * @var $ajaxContext Sibirix_Controller_Action_Helper_SibirixAjaxContext
         */
        $ajaxContext = $this->_helper->getHelper('SibirixAjaxContext');
        $ajaxContext->addActionContext('design-list', 'html')
            ->initContext();

        $this->_model = new Sibirix_Model_Design();
    }

    public function designListAction() {
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'design-list');
        Zend_Registry::get('BX_APPLICATION')->SetTitle("Готовые дизайны для новостроек");

        $mainSliderModel = new Sibirix_Model_MainSlider();
        $slides = $mainSliderModel->getItems();
        $this->view->slides = $slides;

        if ($this->getParam("viewCounter") > 0) {
            Sibirix_Model_ViewCounter::setViewCounter($this->getParam("viewCounter"));
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


        if ($filterParams["priceMin"] === null || $filterParams["priceMax"] === null) {
          //  $filterParams["price"] = $this->_model->getExtremePrice();
        } else {
            $filterParams["price"] = array(
                $filterParams["priceMin"],
                $filterParams["priceMax"]
            );
        }

        if ($filterParams["budgetMin"] === null || $filterParams["budgetMax"] === null) {
            $filterParams["budget"] = $this->_model->getExtremeBudget();
        } else {
            $filterParams["budget"] = array(
                $filterParams["budgetMin"],
                $filterParams["budgetMax"]
            );
        }

        $filter->populate($filterParams);
        $validFilterValues = $filter->getValues();
        $catalogFilter = $this->_model->prepareFilter($validFilterValues);
        $pageTitle     = $this->_model->getPageTitle($validFilterValues);

        $result = $this->_model->getDesignList($catalogFilter, $catalogSort, $this->getParam("page", 1));
        $this->view->assign([
            "pageTitle" => $pageTitle,
            "filter"    => $filter,
            "itemList"  => $result->items,
            'paginator' => EHelper::getPaginator($result->pageData->totalItemsCount, $result->pageData->current, $result->pageData->size),
        ]);
    }

    public function detailAction() {

        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'design-detail');

        $elementCode = $this->getParam("elementCode");
        $design = $this->_model->where(["CODE" => $elementCode])->getElement();

        if (!$design || $design->PROPERTY_STATUS_ENUM_ID != DESIGN_STATUS_PUBLISHED) {
            throw new Zend_Exception('Not found', 404);
        }
        $this->_model->getImageData($design, ["DETAIL_PICTURE", "PROPERTY_PLAN_FLAT_VALUE", "PROPERTY_ESTIMATE_VALUE", "PROPERTY_DOCUMENTS_VALUE"]);
        $this->_model->getSeoElementParams($design);

        $modelUser = new Sibirix_Model_User();
        $design->DESIGNER = $modelUser->where(["ID" => $design->CREATED_BY])->getElement();
        $modelRoom = new Sibirix_Model_Room();
		$modelPin = new Sibirix_Model_Pin();
        $basketModel = new Sibirix_Model_Basket();
        $basketItems = $basketModel->getBasketProductsId();
		
        $design->IS_IN_BASKET = (!empty($basketItems))? in_array($design->ID, $basketItems) : false;
        $design->ROOMS = $modelRoom->where(['PROPERTY_DESIGN' => $design->ID])->getElements();
		
	
        $modelRoom->getImageData($design->ROOMS, 'PROPERTY_IMAGES_VALUE');
	
		//Получаем пины
		foreach($design->ROOMS as $room){
			$room->PINS = $modelPin->getPin($room->ID);
			foreach($room->PINS as $pin){
				//x = 488px - 100%; y = 493px - 100%;
				$coords = explode(",", $pin->PROPERTY_COORDS_VALUE[0]);

				$x = (($coords[0] - 19) / 488) * 100;
				$y = (($coords[1] - 19) / 493) * 100;
				$pin->X = $x;
				$pin->Y = $y;
			}
		}
	
        $list = [];
        if (Sibirix_Model_User::isAuthorized()) {
            $hh = Highload::instance(HL_LIKES)->cache(0);
            $list = $hh->where(['UF_USER_ID' => Sibirix_Model_User::getId(), 'UF_DESIGN' => $design->ID])->fetch();
        }
        $design->IS_LIKED = (!empty($list)) ? true : false;
		
        //Похожие дизайны - получаем до переопределения PROPERTY_STYLE_VALUE и PROPERTY_PRIMARY_COLOR_VALUE
        $filter = [
            "!ID" => $design->ID,
            "PROPERTY_PLAN" => $design->PROPERTY_PLAN,
            "PROPERTY_STATUS" => DESIGN_STATUS_PUBLISHED,
            [
                "LOGIC" => "OR",
                ["PROPERTY_STYLE" => $design->PROPERTY_STYLE],
                ["PROPERTY_PRIMARY_COLOR" => $design->PROPERTY_PRIMARY_COLOR],
            ],
        ];
		
        $limit = Settings::getOption('similarDesignSliderCount', DEFAULT_SLIDES_COUNT);
        $similarDesigns = $this->_model->getRandElements('similarDesign'.$design->ID, [], $filter, $limit, '_getDesignInfo', $this->_model);

        //Получаем характеристики
        $styleList = Sibirix_Model_Reference::getReference(HL_STYLE, array("UF_NAME"), "UF_XML_ID");

        $propertyStylesValue = array();
        foreach ($design->PROPERTY_STYLE_VALUE as $key => $stylesValue) {
            $propertyStylesValue[$key] = $styleList[$stylesValue];
        }
        $design->PROPERTY_STYLE_VALUE = $propertyStylesValue;

        //Получаем цвета
        $colorList = Sibirix_Model_Reference::getReference(HL_PRIMARY_COLORS, array("UF_HEX"), "UF_XML_ID");

        $propertyColorsValue = array();
        foreach ($design->PROPERTY_PRIMARY_COLOR_VALUE as $key => $colorsValue) {
            $propertyColorsValue[$key] = $colorList[$colorsValue];
        }
        $design->PROPERTY_PRIMARY_COLOR_VALUE = $propertyColorsValue;

        $this->_setSeoElementParams($design);
        $this->view->design = $design;
        $this->view->similarDesigns = $similarDesigns;
    }

    /**
     * Добавление дизайна
     */
    public function editAction() {
		
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'profile-inner-page');
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'design-edit');

        if (!Sibirix_Model_User::isAuthorized() || Sibirix_Model_User::getCurrent()->getType() != DESIGNER_TYPE_ID) {
            Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'not-found-page');
            throw new Zend_Exception('Not found', 404);
        }

        $designId = $this->getParam("designId", 0);
        Zend_Registry::get('BX_APPLICATION')->SetTitle(($designId > 0?"Редактирование проекта" : "Добавление проекта"));
		
        $planName = "";
        if ($designId > 0) {
            $designData = $this->_model->getElement($designId);
            if ($designData->PROPERTY_STATUS_ENUM_ID ==  EnumUtils::getListIdByXmlId(IB_DESIGN, "STATUS", "moderation")) {
                LocalRedirect($this->view->url([],"profile-design"));
            }

            $planModel = new Sibirix_Model_Plan();
            $planName = $planModel->getElement($designData->PROPERTY_PLAN_VALUE)->NAME;

            if (!$this->_model->checkDesignAccess($designId, $designData->CREATED_BY)) {
                Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'not-found-page');
                throw new Zend_Exception('Not found', 404);
            }
        }

        /**
         * Шаг 1
         */
        //Выбор по планеровке
        $planForm = new Sibirix_Form_SearchService_StepPlan();

        //Выбор по квартире
        $complexForm = $this->view->action("add-index", "search-service");

        /**
         * Шаг 2
         */
        $step2Form = new Sibirix_Form_AddDesignStep2();

        $roomModel = new Sibirix_Model_Room();
        if (!empty($designData)) {

            //Получаем цену
            $price = $designData->getPrice();

            //Получаем комнаты
            $roomList = $roomModel->cache(0)->where(["PROPERTY_DESIGN" => $designId])->getElements();
            $roomValue = array();

            if (!empty($roomList)) {
                foreach ($roomList as $room) {
                    $roomValue[] = array(
                        "ID"      => $room->ID,
                        "NAME"    => $room->NAME,
                        "AREA"    => $room->PROPERTY_AREA_VALUE,
                        "HAS_IMG" => !empty($room->PROPERTY_IMAGES_VALUE),
                    );
                }
            }

            $step2FormData = array(
                "designName"        => $designData->getName(),
                "shortDescription"  => $designData->PREVIEW_TEXT,
                "designDescription" => $designData->DETAIL_TEXT,
                "mainPhoto"         => !empty($designData->DETAIL_PICTURE) ? Resizer::resizeImage($designData->DETAIL_PICTURE, "DROPZONE_MAIN_PHOTO") : "",
                "style"             => $designData->PROPERTY_STYLE_VALUE,
                "color"             => $designData->PROPERTY_PRIMARY_COLOR_VALUE,
                "designPrice"       => ($price > 0 ? $price : ""),
                "designFree"        => ($price > 0 ? false : true),
                "square"            => $designData->PROPERTY_AREA_VALUE,
                "roomList"          => $roomValue,
            );

            $step2Form->populate($step2FormData);
        }

        /**
         * Шаг 3
         */
        $step3Form = $this->view->action("room-form", "room", false, ["designId" => $designId]);

        /**
         * Шаг 4
         */
        $step4Form = new Sibirix_Form_AddDesignStep4();
        if (!empty($designData) && !empty($designData->PROPERTY_DOCUMENTS_VALUE)) {
            $fileData = EHelper::getFileData($designData->PROPERTY_DOCUMENTS_VALUE);

            $step4FormData["docs"][$designData->PROPERTY_DOCUMENTS_VALUE] = array(
                "fileType" => GetFileExtension($fileData["FILE_NAME"]),
                "fileName" => $fileData["ORIGINAL_NAME"],
                "fileSize" => round($fileData["FILE_SIZE"]/1024) . " Кб",
            );

            $step4Form->populate($step4FormData);
        }

        if (!empty($designData)) {
            $this->view->designData = $designData;
        }
        $this->view->planName    = "Дизайн для планировки: " . $planName;
        $this->view->planForm    = $planForm;
        $this->view->step2Form   = $step2Form;
        $this->view->step3Form   = $step3Form;
        $this->view->step4Form   = $step4Form;
        $this->view->complexForm = $complexForm;
    }

    /**
     * Сохранение первого шага
     */
    public function addSaveStepFirstAction() {
        $stepValues = $this->getAllParams();

        $planId = false;
        if ($stepValues["planId"] > 0) {
            $planId = $stepValues["planId"];

        } elseif ($stepValues["flatId"] > 0) {
            $flatModel = new Sibirix_Model_Flat();
            $flat = $flatModel->select(["ID", "PROPERTY_PLAN"], true)->getElement($stepValues["flatId"]);
            if (!empty($flat)) {
                $planId = $flat->PROPERTY_PLAN_VALUE;
            }

        }

        if ($stepValues["designId"] > 0) {
            if (!$this->_model->checkDesignAccess($stepValues["designId"])) {
                Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'not-found-page');
                throw new Zend_Exception('Not found', 404);
            }
        }

        $fields = array(
            "ID"              => $stepValues["designId"],
            "PROPERTY_VALUES" => array(
                "PLAN" => $planId
            )
        );

        $planModel = new Sibirix_Model_Plan();
        $plan = $planModel->getElement($planId);

        $stepValue = "Дизайн для планировки: ".$plan->NAME;

        $editResult = $this->_model->editDesign($fields);
        $this->_helper->json(['result' => (bool)$editResult, 'newId' => (is_numeric($editResult) ? $editResult : ""), "stepValue" => $stepValue]);
    }

    /**
     * Загрузка изображений с dropzone "налету"
     */
    public function uploadFileAction() {
        $upload = new Zend_File_Transfer();
        $designId = $this->getParam("designId");

        $fileModel = new Sibirix_Model_Files();

        if (!$this->_model->checkDesignAccess($designId)) {
            Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'not-found-page');
            throw new Zend_Exception('Not found', 404);
        }

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

        $editResult = $this->_model->editDesign($fields);

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

    /**
     * Удаляет файл
     */
    public function deleteFileAction() {
        $designId = $this->getParam("designId");

        if (!$this->_model->checkDesignAccess($designId)) {
            Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'not-found-page');
            throw new Zend_Exception('Not found', 404);
        }
        $fileType   = $this->getParam("fileType");

        $fields = array(
            "ID" => $designId,
            $fileType => array("del" => "Y")
        );

        $regep = '/^PROPERTY_(.*)$/';
        if (preg_match($regep, $fileType, $matches)) {
            $fields["PROPERTY_VALUES"][$matches[1]] = $fields[$fileType];
            unset($fields[$fileType]);
        }
        $editResult = $this->_model->editDesign($fields);

        $this->_helper->json(['result' => (bool)$editResult]);
    }

    /**
     * Сохранение второго шага
     */
    public function addSaveStepSecondAction() {
        $designId = $this->getParam("designId");

        if (!$this->_model->checkDesignAccess($designId)) {
            Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'not-found-page');
            throw new Zend_Exception('Not found', 404);
        }

        $step2Form = new Sibirix_Form_AddDesignStep2();
        $step2Form->populate($this->getAllParams());
        $validValues = $step2Form->getValues();

        if ($step2Form->isValid($validValues)) {
            $fields = array(
                "ID"              => $designId,
                "NAME"            => $validValues["designName"],
                "PREVIEW_TEXT"    => $validValues["shortDescription"],
                "DETAIL_TEXT"     => $validValues["designDescription"],
                "PROPERTY_VALUES" => array(
                    "PRIMARY_COLOR" => $validValues["color"],
                    "STYLE"         => $validValues["style"],
                    "AREA"          => $validValues["square"],
                ),
                "PRICE_VALUE"     => array(
                    "PRODUCT_ID"       => $designId,
                    "CATALOG_GROUP_ID" => BASE_PRICE,
                    "PRICE"            => ($validValues["designPrice"] > 0 ? $validValues["designPrice"] : 0),
                    "CURRENCY"         => "RUB"
                )
            );

            $editResult = $this->_model->editDesign($fields);
        } else {
            $step2Form->getFieldsErrors();
        }

        $this->_helper->json(['result' => (bool)$editResult, 'newId' => (is_numeric($editResult) ? $editResult : ""), 'errorFields' => $step2Form->formErrors]);
    }

    public function publishDesignAction() {
        $designId = $this->getParam("designId");

        if (!$this->_model->checkDesignAccess($designId)) {
            Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'not-found-page');
            throw new Zend_Exception('Not found', 404);
        }

        $fields = array(
            "ID"              => $designId,
            "PROPERTY_VALUES" => array(
                "STATUS" => EnumUtils::getListIdByXmlId(IB_DESIGN, "STATUS", "moderation")
            )
        );
        $editResult = $this->_model->editDesign($fields);

        $this->_helper->json(['result' => (bool)$editResult]);
    }

    public function deleteAction() {
        $designId = $this->getParam("designId", 0);

        if (!$this->_model->checkDesignAccess($designId) || !check_bitrix_sessid()) {
            Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'not-found-page');
            throw new Zend_Exception('Not found', 404);
        }

        $result = $this->_model->update($designId, ['PROPERTY_VALUES' => ['STATUS' => DESIGN_STATUS_DELETED]]);
        $this->_helper->json(['success' => true, 'result' => $result]);
    }

    public function likeAddAction() {
        if (!check_bitrix_sessid() || !Sibirix_Model_User::isAuthorized()) {
            Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'not-found-page');
            throw new Zend_Exception('Not found', 404);
        }
	
        $likeModel = new Sibirix_Model_Like();
        $itemId = (int) $this->getParam("itemId");
		
        $likeModel->add($itemId);
		
        $count = $this->_model->cacheLikes($itemId);

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
        $count = $this->_model->cacheLikes($itemId);

        $this->_helper->json(['success' => true, 'likeCnt' => $count]);
    }

    /**
     * Загрузка плана
     */
    public function addPlanAction() {
        $upload = new Zend_File_Transfer();
        $designId = $this->getParam("designId");
        $fileModel = new Sibirix_Model_Files();

        if (!$this->_model->getElement($designId)->CREATED_BY == Sibirix_Model_User::getId()) {
            Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'not-found-page');
            throw new Zend_Exception('Not found', 404);
        }

        $imageFile = $upload->getFileInfo()["file"];

        if (!$fileModel->checkFile($upload->getFileInfo()["file"], "jpg,png,jpeg,gif,bmp")) {
            $this->_helper->json(['result' => false, "response" => "Invalid file type"]);
        }
        $resultImages = $this->_model->addPlan($designId, $imageFile);

        $this->_helper->json(['result' => true, 'response' => $resultImages]);
    }

    /**
     * Удаление изображения плана
     */
    public function deletePlanAction() {
        $designId = $this->getParam("designId");
        $imageId = $this->getParam("imageId");

        if (!$this->_model->getElement($designId)->CREATED_BY == Sibirix_Model_User::getId()) {
            Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'not-found-page');
            throw new Zend_Exception('Not found', 404);
        }

        $resultImages = $this->_model->deletePlan($designId, $imageId);

        $this->_helper->json(['result' => true, 'response' => $resultImages]);
    }
	
	public function getImgsAction() {
        $ibType = $this->getParam("type");
		$indx = $this->getParam("indx");
        /*switch ($ibType) {
            case "complex":
                $model = new Sibirix_Model_Complex();
                $property = "PROPERTY_COMPLEX_PLAN";
                break;
            case "floor":
                $model = new Sibirix_Model_Floor();
                $property = "PROPERTY_FLOOR_PLAN";
                break;
			case "room":
                $model_room = new Sibirix_Model_Room();
				$model_pin = new Sibirix_Model_Pin();
                $property = "PROPERTY_IMAGES";
			  // $property = "PROPERTY_ROOM_PLAN";
                break;
        }*/

		$model_room = new Sibirix_Model_Room();
		$model_pin = new Sibirix_Model_Pin();
		$property = "PROPERTY_IMAGES";
        if (empty($model_room) || empty($model_pin) || empty($property)) {
            return false;
        }

        $elementId = $this->getParam("id");

        if (is_numeric($elementId) && $elementId > 0) {
            $item = $model_room->select(["ID", $property], true)->getElement($this->getParam("id"));
			//Получаем пины
			$item->PINS = $model_pin->getPin($item->ID);
			foreach($item->PINS as $pin){
				//x = 488px - 100%; y = 493px - 100%;
				$coords = explode(",", $pin->PROPERTY_COORDS_VALUE[$indx]);
				if($coords[0] > 19 && $coords[1] > 19){
					$x = $coords[0] - 19;
					$y = $coords[1] - 19;
				}
				else{
					$x = $coords[0];
					$y = $coords[1];
				};
				$pin->X = $x;
				$pin->Y = $y;
			}
        }
	
        if (!empty($item)){
            $model_room->getImageData($item, $property."_VALUE");
            $propertyValue = $property . "_VALUE";
			$this->view->imageUrl = Resizer::resizeImage($item->PROPERTY_IMAGES_VALUE[$indx], 'SEARCH_SERVICE_PLAN');
			$this->view->item = $item;
        }
        $this->_response->stopBitrix(true);
	
    }
	
	public function addPinAction() {
		//ini_set('error_reporting', E_ALL);
		//ini_set('display_errors', 1);
		//ini_set('display_startup_errors', 1);
        $coords = $this->getParam("coords");
		$name = $this->getParam("name");
		$url = $this->getParam("url");
		$roomId = $this->getParam("roomId");
		$indx = $this->getParam("indx");
		$data = array();
		$data['NAME'] = $name;
		$data['PROPERTY_VALUES']['URL'] = $url;
		$data['PROPERTY_VALUES']['ROOM'] = $roomId;
		$data['PROPERTY_VALUES']['COORDS'][$indx] = $coords;
		$model = new Sibirix_Model_Pin();
		$arr_pins = $model->getPins(["=NAME" => $name, "=PROPERTY_ROOMS" => $roomId]);
		
		if(count($arr_pins) == 1){
			if(!$arr_pins[0]->PROPERTY_COORDS_VALUE[$indx] || $arr_pins[0]->PROPERTY_COORDS_VALUE[$indx] == '0, 0'){
				$data['ID'] = $arr_pins[0]->ID;
				$data['PROPERTY_VALUES']['COORDS'] = $arr_pins[0]->PROPERTY_COORDS_VALUE;
				$data['PROPERTY_VALUES']['COORDS'][$indx] = $coords;
				$model->updatePin($data);
			}
		}
		else{
			if(empty($name)){
				$data['NAME'] = 'new_pin_'.time();
			};
		
			for($i = 0; $i < $indx; $i++){
				if(!$data['PROPERTY_VALUES']['COORDS'][$i]){
					$data['PROPERTY_VALUES']['COORDS'][$i] = '0, 0';
				};
			}
	
			$data['PROPERTY_VALUES']['COORDS'][$indx] = $coords;
			sort($data['PROPERTY_VALUES']['COORDS']);
			$model->addPin($data, $indx);
		}

	

		/*$indx = $this->getParam("indx");
		
        switch ($ibType) {
            case "complex":
                $model = new Sibirix_Model_Complex();
                $property = "PROPERTY_COMPLEX_PLAN";
                break;
            case "floor":
                $model = new Sibirix_Model_Floor();
                $property = "PROPERTY_FLOOR_PLAN";
                break;
			case "room":
                $model = new Sibirix_Model_Room();
                $property = "PROPERTY_IMAGES";
			  // $property = "PROPERTY_ROOM_PLAN";
                break;
        }

        if (empty($model) || empty($property)) {
            return false;
        }

        $elementId = $this->getParam("id");
        if (is_numeric($elementId) && $elementId > 0) {
            $item = $model->select(["ID", $property], true)->getElement($this->getParam("id"));
        }

        if (!empty($item)) {
            $model->getImageData($item, $property."_VALUE");
            $propertyValue = $property . "_VALUE";
			
			$this->view->imageUrl = Resizer::resizeImage($item->PROPERTY_IMAGES_VALUE[$indx], 'SEARCH_SERVICE_PLAN');
        }*/
		$this->view->pinCoords = $coords;
        $this->_response->stopBitrix(true);
	
    }
	
	public function delPinAction() {
        $pinId = $this->getParam("pinId");
		$model = new Sibirix_Model_Pin();
		$result = $model->delPin($pinId);
		$this->_helper->json(['result' => true, 'response' => $result]);
    }
	
	public function changePosPinAction() {
        $pinId = $this->getParam("pinId");
		$pos = $this->getParam("pos");
		$indxs = $this->getParam("indx");
		$model = new Sibirix_Model_Pin();
		$data = array();
		$data['ID'] = $pinId;
		$data['PROPERTY_VALUES']['COORDS'][$indxs] = $pos;
		$result = $model->updatePin($data);
		$this->_helper->json(['result' => true, 'response' => $result]);
    }
	
}