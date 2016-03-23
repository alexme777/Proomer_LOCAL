<?

/**
 * Class DesignController
 */
class ProviderController extends Sibirix_Controller {
	
    /**
     * @var Sibirix_Model_Design
     */
    protected $_model;

    public function init() {
		session_start();
        /**
         * @var $ajaxContext Sibirix_Controller_Action_Helper_SibirixAjaxContext
         */
        $ajaxContext = $this->_helper->getHelper('SibirixAjaxContext');
        $ajaxContext->addActionContext('design-list', 'html')
            ->initContext();

        $this->_model = new Sibirix_Model_Goods();
		$this->user = Sibirix_Model_User::getCurrent();
    }

    /**
     * Добавление дизайна
     */
    public function editAction() {
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'profile-inner-page');
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'provider-edit');

        if (!Sibirix_Model_User::isAuthorized() || Sibirix_Model_User::getCurrent()->getType() != PROVIDER_TYPE_ID) {
            Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'not-found-page');
            throw new Zend_Exception('Not found', 404);
        }

        $designId = $this->getParam("designId", 0);
        Zend_Registry::get('BX_APPLICATION')->SetTitle("Редактирование товара");
		
		if ($designId > 0) {
            $designData = $this->_model->getElement($designId);
           /* if ($designData->PROPERTY_STATUS_ENUM_ID ==  EnumUtils::getListIdByXmlId(IB_DESIGN, "STATUS", "moderation")) {
                LocalRedirect($this->view->url([],"profile-design"));
            }*/

            /*if (!$this->_model->checkDesignAccess($designId, $designData->CREATED_BY)) {
                Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'not-found-page');
                throw new Zend_Exception('Not found', 404);
            }*/
        }
		
        /**
         * Шаг 2
         */
        //Редактирование товара
        $step2Form = new Sibirix_Form_EditProviderStep2();
		//Получаем превью

		$previewValue = array();
		$previewList = $designData->PROPERTY_PREVIEW_VALUE;
		
		if (!empty($previewList)) {
			foreach ($previewList as $preview) {
				$previewValue[] = CFile::GetPath($preview);
			}
		}
		
        if (!empty($designData)) {
            $step2FormData = array(
				"designId"        	=> $designId,
				"designName"        => $designData->NAME,
				"designPrice"		=> $designData->PROPERTY_PRICE,
				"shortDescription"	=> $designData->PREVIEW_TEXT,
				"designDescription"	=> $designData->DETAIL_TEXT,
				"matherial"			=> $designData->PROPERTY_MATERIAL,
				"width"				=> $designData->PROPERTY_WIDTH,
				"length"			=> $designData->PROPERTY_LENGTH,
				"height"			=> $designData->PROPERTY_HEIGHT,
				"article"			=> $designData->PROPERTY_ARTICLE,
				"madein"			=> $designData->PROPERTY_MADEIN_VALUE,
				"planImage"			=> $previewValue,
				//"planImage"			=> !empty($designData->PROPERTY_PREVIEW_PROPERTY_VALUE_ID[1]) ? Resizer::resizeImage($designData->PROPERTY_PREVIEW_PROPERTY_VALUE_ID[1], "DROPZONE_PREVIEW_PHOTO") : "",
				"category"			=> Array($designData->IBLOCK_SECTION_ID),

				//"anonsPhoto"         => !empty($designData->PREVIEW_PICTURE) ? Resizer::resizeImage($designData->PREVIEW_PICTURE, "DROPZONE_ANONS_PHOTO") : "",
               // "mainPhoto"         => !empty($designData->DETAIL_PICTURE) ? Resizer::resizeImage($designData->DETAIL_PICTURE, "DROPZONE_MAIN_PHOTO") : "",
				//"category"          => $designData->PROPERTY_CATEGORY_VALUE,
                "style"             => $designData->PROPERTY_STYLE_VALUE,
				"color"             => $designData->PROPERTY_COLOR_VALUE,
               // "designFree"        => ($price > 0 ? false : true),
            );
            $step2Form->populate($step2FormData);
        }

        /**
         * Шаг 3
         */
       // $step3Form = $this->view->action("room-form", "room", false, ["designId" => $designId]);
		//$step3Form = new Sibirix_Form_AddProviderStep3();
        /**
         * Шаг 4
         */
        $step4Form = new Sibirix_Form_AddProviderStep4();
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
		$model = new Sibirix_Model_Goods;
		$row_goods = $model -> getGoods($this->user->ID);
		$row_goods = $model -> getImgItems($row_goods['ITEMS']);
		
        $this->view->planName    = "Дизайн для планировки: " . $planName;
      //  $this->view->planForm    = $planForm;
        $this->view->step2Form   = $step2Form;
        $this->view->step3Form   = $step3Form;
        $this->view->step4Form   = $step4Form;
		$this->view->row_goods = $row_goods;
        //$this->view->complexForm = $complexForm;
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
        $fileType   = $this->getParam("fileType");
        $resizeType = $this->getParam("resizeType");
	
        $fields = array(
			"NAME" => 'Без названия',
			"PROPERTY_VALUES" => array(
				"STATUS" => 52
			),
        );
	
		$updatefields = array(
			$fileType => $upload->getFileInfo()["file"]
        );
		$updatefieldsProp = array(
			"STATUS" => 52,
        );
			
        $regep = '/^PROPERTY_(.*)$/';
        if (preg_match($regep, $fileType, $matches)) {
            $fields["PROPERTY_VALUES"][$matches[1]] = $fields[$fileType];
            unset($fields[$fileType]);
        }

        $accessExtensions = "";
        switch ($fileType) {
			case "PREVIEW_PICTURE":
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
		
		$model = new Sibirix_Model_Goods();
		//если создан товар, то делаем апдэйт
		if(isset($_SESSION['designId']) && $_SESSION['designId'] > 0){
			$editResult = $model->updateGoods($_SESSION['designId'], $updatefields, $updatefieldsProp);
			$editResult = $_SESSION['designId'];
			$designId = $editResult;
		}
		//иначе создаем
		else{
			$editResult = $model->addGoods($fields);
			$designId = $editResult;
			$_SESSION['designId'] = $designId;
		}
        $newFile = false;
        if ($editResult) {
            $newFile = $model->select([$fileType], true)->getElement($designId);
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
		$response["designId"] = $designId;
        $this->_helper->json(['result' => (bool)$editResult, "response" => $response]);
    }

    /**
     * Удаляет файл
     */
    public function deleteFileAction() {
        $designId = $this->getParam("designId");
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
       
       /* if (!$this->_model->checkDesignAccess($designId)) {
            Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'not-found-page');
            throw new Zend_Exception('Not found', 404);
        }*/

        $step2Form = new Sibirix_Form_AddProviderStep2();
		$model = new Sibirix_Model_Goods();
        $step2Form->populate($this->getAllParams());
        $validValues = $step2Form->getValues();
		$designId = $this->getParam("designId");
		if(!$designId){$designId = $_SESSION['designId'];};
      //  if ($step2Form->isValid($validValues)) {
	
				$fields = array(
					"NAME"            => $validValues["designName"],
					"PREVIEW_TEXT"    => $validValues["shortDescription"],
					"DETAIL_TEXT"     => $validValues["designDescription"],
					"IBLOCK_SECTION_ID" => $validValues["category"][0]
				   /* "PRICE_VALUE"     => array(
						"PRODUCT_ID"       => $designId,
						"CATALOG_GROUP_ID" => BASE_PRICE,
						"PRICE"            => ($validValues["designPrice"] > 0 ? $validValues["designPrice"] : 0),
						"CURRENCY"         => "RUB"
					)*/
				);
				$fields_prop = array(
					"ID_USER" => $this->user->ID,
					"ARTICLE" => $this->getParam("article"),
					"PRICE" => $validValues["designPrice"],
					"MATERIAL" => $this->getParam("matherial"),
					"MADEIN" => $this->getParam("madein"),
					"HEIGHT" => $this->getParam("height"),
					"WIDTH" => $this->getParam("width"),
					"LENGTH" => $this->getParam("length"),
					"COLOR" => $this->getParam("color"),
					"STYLE"         => $this->getParam("style"),
					"STATUS"         => 16	
				);
				//если создан товар, то делаем апдэйт
				if(isset($designId) && $designId > 0){
					$editResult = $model->updateGoods($designId,$fields, $fields_prop);
					//$editResult = $_SESSION['designId'];
					//$designId = $editResult;
				}
				//иначе создаем
				else{
					$editResult = $model->addGoods($fields);
					$designId = $editResult;
					$_SESSION['designId'] = $designId;
				}
				unset($_SESSION['designId']);
				exit;
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
        $designId = 2256;
        $fileModel = new Sibirix_Model_Files();
		$model = new Sibirix_Model_Goods;

       /* if (!$model->getElement($designId)->CREATED_BY == Sibirix_Model_User::getId()) {
            Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'not-found-page');
            throw new Zend_Exception('Not found', 404);
        }*/

        $imageFile = $upload->getFileInfo()["file"];
		$fields = array(
			"NAME" => 'Без названия',
			"PROPERTY_VALUES" => array(
				"STATUS" => 52
			),
        );
		
        if(!$fileModel->checkFile($upload->getFileInfo()["file"], "jpg,png,jpeg,gif,bmp")) {
            $this->_helper->json(['result' => false, "response" => "Invalid file type"]);
        }
		
		//если создан товар, то делаем апдэйт
		if(isset($designId) && $designId > 0){
			$resultImages = $model->addPlan($designId, $imageFile);
			$editResult = $designId;
			$designId = $editResult;
		}
		//иначе создаем
		else{
			$editResult = $model->addGoods($fields);
			$resultImages = $model->addPlan($editResult, $imageFile);
			$designId = $editResult;
			$_SESSION['designId'] = $designId;
		}

        $this->_helper->json(['result' => true, 'response' => $resultImages]);
    }

    /**
     * Удаление изображения плана
     */
    public function deletePlanAction() {
        $designId = $this->getParam("designId");
		if(!$designId){$designId = $_SESSION['designId'];};
        $imageId = $this->getParam("imageId");
        /*if (!$this->_model->getElement($designId)->CREATED_BY == Sibirix_Model_User::getId()) {
            Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'not-found-page');
            throw new Zend_Exception('Not found', 404);
        }*/
		$model = new Sibirix_Model_Goods;
        $resultImages = $model->deletePlan($designId, $imageId);

        $this->_helper->json(['result' => true, 'response' => $resultImages]);
    }
}