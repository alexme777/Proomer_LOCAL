<?

/**
 * Class ProfileController
 */
class ProfileController extends Sibirix_Controller {

    /**
     * @var $user Sibirix_Model_User_Row
     */
    protected $user;

    public function preDispatch() {
	
        /**
         * @var $ajaxContext Sibirix_Controller_Action_Helper_SibirixAjaxContext
         */
        $ajaxContext = $this->_helper->getHelper('SibirixAjaxContext');
        $ajaxContext->addActionContext('design', 'html')
            ->initContext();

        $this->user = Sibirix_Model_User::getCurrent();
        if (!$this->user) {
            $this->redirect('/');
        }
    }

    public function indexAction() {

        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'profile-inner');
        Zend_Registry::get('BX_APPLICATION')->SetTitle('Профиль');
		
        $this->view->user = $this->user;
    }

    public function designerUpdateAction() {
        $form = new Sibirix_Form_ProfileDesigner();

        if (!(Sibirix_Model_User::isAuthorized())) {
            $this->_helper->json(['success' => false, 'errorFields' =>  ['NAME' => 'Пользователь не авторизован']]);
        }

        if ($form->isValid($this->getAllParams())) {
            $model = new Sibirix_Model_User();
            $name = $form->getValue('name');
            $lastName = $form->getValue('lastName');
            $phone = $form->getValue('phone');
            $email = $form->getValue('email');
            $portfolio = $form->getValue('portfolio');
            $about = $form->getValue('about');
            $styles = $form->getValue('styles');
			$city = $form->getValue('city');
			$kompany = $form->getValue('kompany');
			$address = $form->getValue('address');

            $result = $model->updateDesignerProfile($this->user, $name, $lastName, $phone, $email, $portfolio, $about, $styles, $kompany, $address, $city);

            if ($result === true) {
                $this->_helper->json(['success' => true]);
            } else {
                foreach ($result as $ind => $error) {
                    $form->getElement($ind)->addErrors($result);
                }
                $form->markAsError();
                $form->getFieldsErrors();
                $this->_helper->json(['success' => false, 'errorFields' =>  $form->formErrors]);
            }
        } else {
            $form->getFieldsErrors();
        }

        $this->_helper->json(['success' => false, 'errorFields' =>  $form->formErrors]);
    }

    public function providerUpdateAction() {
        $form = new Sibirix_Form_ProfileProvider();

        $userId = $this->getParam('userId');
	
        if (!(Sibirix_Model_User::getId() == $userId)) {
            $this->_helper->json(['success' => false, 'errorFields' =>  ['NAME' => $userId]]);
        }

        if ($form->isValid($this->getAllParams())) {
            $model = new Sibirix_Model_User();
            $name = $form->getValue('name');
            $lastName = $form->getValue('lastName');
            $phone = $form->getValue('phone');
            $email = $form->getValue('email');

            $result = $model->updateProviderProfile($this->user, $name, $lastName, $phone, $email);
            if ($result === true) {
                $this->_helper->json(['success' => true]);
            } else {
                foreach ($result as $ind => $error) {
                    $form->getElement($ind)->addErrors($result);
                }
                $form->markAsError();
                $form->getFieldsErrors();
                $this->_helper->json(['success' => false, 'errorFields' =>  $form->formErrors]);
            }
        } else {
            $form->getFieldsErrors();
        }

        $this->_helper->json(['success' => false, 'errorFields' =>  $form->formErrors]);
    }
	
	public function clientUpdateAction() {
        $form = new Sibirix_Form_ProfileClient();

        $userId = $this->getParam('userId');
        if (!(Sibirix_Model_User::getId() == $userId)) {
            $this->_helper->json(['success' => false, 'errorFields' =>  ['NAME' => $userId]]);
        }

        if ($form->isValid($this->getAllParams())) {
            $model = new Sibirix_Model_User();
            $name = $form->getValue('name');
            $lastName = $form->getValue('lastName');
            $phone = $form->getValue('phone');
            $email = $form->getValue('email');

            $result = $model->updateClientProfile($this->user, $name, $lastName, $phone, $email);
            if ($result === true) {
                $this->_helper->json(['success' => true]);
            } else {
                foreach ($result as $ind => $error) {
                    $form->getElement($ind)->addErrors($result);
                }
                $form->markAsError();
                $form->getFieldsErrors();
                $this->_helper->json(['success' => false, 'errorFields' =>  $form->formErrors]);
            }
        } else {
            $form->getFieldsErrors();
        }

        $this->_helper->json(['success' => false, 'errorFields' =>  $form->formErrors]);
    }

    public function changePasswordAction() {
        $form = new Sibirix_Form_ProfileChangePassword();

        $userId = $this->getParam('userId');
        if (!Sibirix_Model_User::isAuthorized() || !(Sibirix_Model_User::getId() == $userId)) {
            $this->_helper->json(['success' => false, 'errorFields' =>  ['NAME' => $userId]]);
        }

        if ($form->isValid($this->getAllParams())) {
            $model = new Sibirix_Model_User();
            $password = $form->getValue('password');
            $newPassword = $form->getValue('newPassword');
            $newPasswordConfirm = $form->getValue('newPasswordConfirm');

            $result = $model->changeProfilePassword($this->user, $password, $newPassword, $newPasswordConfirm);
            if ($result === true) {
                $this->_helper->json(['success' => true]);
            } else {
                foreach ($result as $ind => $error) {
                    $form->getElement($ind)->addErrors($result);
                }
                $form->markAsError();
                $form->getFieldsErrors();
                $this->_helper->json(['success' => false, 'errorFields' =>  $form->formErrors]);
            }
        } else {
            $form->getFieldsErrors();
        }

        $this->_helper->json(['success' => false, 'errorFields' =>  $form->formErrors]);
    }

    public function uploadPhotoAction() {
        $upload = new Zend_File_Transfer();
        $fileInfo = $upload->getFileInfo()['avatar'];
        $fileModel = new Sibirix_Model_Files();

        if (!$fileModel->checkFile($upload->getFileInfo()["avatar"], "jpg,png,jpeg")) {
            $this->_helper->json(['success' => false, 'messageTitle' => Settings::getOption('formatErrorMessage'), 'messageText' => Settings::getOptionText('formatErrorMessage')]);
        }

        if ($fileInfo["size"] > MAX_FILE_SIZE) {
            $this->_helper->json(['success' => false, 'messageTitle' => Settings::getOption('sizeErrorMessage'), 'messageText' => Settings::getOptionText('sizeErrorMessage')]);
        }

        $userModel = new Sibirix_Model_User();
        $result = $userModel->updatePhoto($this->user, $fileInfo);

        $newPhoto = $userModel->select(['PERSONAL_PHOTO'], false)->getElement($this->user->ID);

        $avatar = Resizer::resizeImage($newPhoto->PERSONAL_PHOTO, "PROFILE_AVATAR");
        $headerAvatar = Resizer::resizeImage($newPhoto->PERSONAL_PHOTO, "HEADER_AVATAR");

        if ($result === true) {
            $this->_helper->json(['success' => true, 'avatar' => $avatar, 'headerAvatar' => $headerAvatar]);
        }

        $this->_helper->json(['success' => true, 'file' => $fileInfo]);
    }

    public function removePhotoAction() {
        if (!check_bitrix_sessid()) {
            $this->_helper->json(['success' => false]);
        }

        $userModel = new Sibirix_Model_User();
        $result = false;
        if (!empty($this->user->PERSONAL_PHOTO)) {
            $result = $userModel->updatePhoto($this->user, ['del' => 'Y']);
        }

        $avatar = Resizer::resizeImage(Settings::getOptionFile('defaultAvatar'), "PROFILE_AVATAR");
        $headerAvatar = Resizer::resizeImage(Settings::getOptionFile('defaultAvatar'), "HEADER_AVATAR");
        if ($result === true) {
            $this->_helper->json(['success' => true, 'avatar' => $avatar, 'headerAvatar' => $headerAvatar]);
        }

        $this->_helper->json(['success' => false, 'message' => 'Файл не был удален']);
    }
	
	public function providerAction() {
		Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'profile-inner-page');
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'provider-edit');

        if (!Sibirix_Model_User::isAuthorized() || Sibirix_Model_User::getCurrent()->getType() != PROVIDER_TYPE_ID) {
            Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'not-found-page');
            throw new Zend_Exception('Not found', 404);
        }

        $designId = $this->getParam("designId", 0);
        Zend_Registry::get('BX_APPLICATION')->SetTitle(($designId > 0?"Редактирование проекта" : "Добавление проекта"));

        $planName = "";
        if ($designId > 0) {
            $designData = $this->_model->getElement($designId);
            if ($designData->PROPERTY_STATUS_ENUM_ID ==  EnumUtils::getListIdByXmlId(IB_DESIGN, "STATUS", "moderation")) {
                LocalRedirect($this->view->url([],"profile-provider"));
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
       // $planForm = new Sibirix_Form_SearchService_StepPlan();

        //Выбор по квартире
        //$complexForm = $this->view->action("add-index", "search-service");

        /**
         * Шаг 2
         */
        $step2Form = new Sibirix_Form_AddProviderStep2();

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
				"anonsPhoto"         => !empty($designData->PREVIEW_PICTURE) ? Resizer::resizeImage($designData->PREVIEW_PICTURE, "DROPZONE_ANONS_PHOTO") : "",
                "mainPhoto"         => !empty($designData->DETAIL_PICTURE) ? Resizer::resizeImage($designData->DETAIL_PICTURE, "DROPZONE_MAIN_PHOTO") : "",
				"category"          => $designData->PROPERTY_CATEGORY_VALUE,
                "style"             => $designData->PROPERTY_STYLE_VALUE,
                "color"             => $designData->PROPERTY_PRIMARY_COLOR_VALUE,
                "designPrice"       => ($price > 0 ? $price : ""),
               // "designFree"        => ($price > 0 ? false : true),
                "square"            => $designData->PROPERTY_AREA_VALUE,
                "roomList"          => $roomValue
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
	
	public function editAction() {
		Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'profile-inner-page');
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'provider-edit');

        if (!Sibirix_Model_User::isAuthorized() || Sibirix_Model_User::getCurrent()->getType() != PROVIDER_TYPE_ID) {
            Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'not-found-page');
            throw new Zend_Exception('Not found', 404);
        }

        $designId = $this->getParam("designId", 0);
        Zend_Registry::get('BX_APPLICATION')->SetTitle(($designId > 0?"Редактирование проекта" : "Добавление проекта"));

        $planName = "";
        if ($designId > 0) {
            $designData = $this->_model->getElement($designId);
            if ($designData->PROPERTY_STATUS_ENUM_ID ==  EnumUtils::getListIdByXmlId(IB_DESIGN, "STATUS", "moderation")) {
                LocalRedirect($this->view->url([],"profile-provider"));
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
       // $planForm = new Sibirix_Form_SearchService_StepPlan();

        //Выбор по квартире
        //$complexForm = $this->view->action("add-index", "search-service");

        /**
         * Шаг 2
         */
        $step2Form = new Sibirix_Form_AddProviderStep2();

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
				"anonsPhoto"         => !empty($designData->PREVIEW_PICTURE) ? Resizer::resizeImage($designData->PREVIEW_PICTURE, "DROPZONE_ANONS_PHOTO") : "",
                "mainPhoto"         => !empty($designData->DETAIL_PICTURE) ? Resizer::resizeImage($designData->DETAIL_PICTURE, "DROPZONE_MAIN_PHOTO") : "",
				"category"          => $designData->PROPERTY_CATEGORY_VALUE,
                "style"             => $designData->PROPERTY_STYLE_VALUE,
                "color"             => $designData->PROPERTY_PRIMARY_COLOR_VALUE,
                "designPrice"       => ($price > 0 ? $price : ""),
               // "designFree"        => ($price > 0 ? false : true),
                "square"            => $designData->PROPERTY_AREA_VALUE,
                "roomList"          => $roomValue
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
	
    public function designAction() {
        if (!$this->user->getType() == DESIGNER_TYPE_ID) {
            Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'not-found-page');
            throw new Zend_Exception('Not found', 404);
        }

        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'profile-inner-page');
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'profile-design');
        Zend_Registry::get('BX_APPLICATION')->SetTitle('Мои проекты');
        $designModel = new Sibirix_Model_Design();

        if ($this->getParam("viewCounter") > 0) {
            Sibirix_Model_ViewCounter::setProfileViewCounter($this->getParam("viewCounter"));
            $designModel->reinitProfileViewCounter();
        } else {
            $designModel->reinitProfileViewCounter();
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

        $filter = new Sibirix_Form_FilterProfileDesign();
        $filterParams = $this->getAllParams();

        if ($filterParams["priceMin"] === null || $filterParams["priceMax"] === null) {
            $filterParams["price"] = $designModel->getExtremePrice($this->user->ID);
        } else {
            $filterParams["price"] = array(
                $filterParams["priceMin"],
                $filterParams["priceMax"]
            );
        }

        $filter->populate($filterParams);
        $validFilterValues = $filter->getValues();
        $catalogFilter = $designModel->prepareFilter($validFilterValues);
        $catalogFilter["CREATED_BY"] = $this->user->ID;
        $catalogFilter["!PROPERTY_STATUS"] = DESIGN_STATUS_DELETED;

        $result = $designModel->getDesignList($catalogFilter, $catalogSort, $this->getParam("page", 1), true);
        $status = EnumUtils::getPropertyValuesList(IB_DESIGN, 'STATUS');

        $this->view->assign([
            "filter"    => $filter,
            "itemList"  => $result->items,
            'paginator' => EHelper::getPaginator($result->pageData->totalItemsCount, $result->pageData->current, $result->pageData->size),
            'status' => $status,
        ]);
    }
	
	public function basketAction() {

		Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'profile-inner');
        Zend_Registry::get('BX_APPLICATION')->SetTitle('Профиль');
		$type = $this->getParam('type');
		/*if($type == 'favourite'){
			$delay = "Y";
			$pageTitle = "Избранные товары";
		}
		else if($type == 'basket'){
			$delay = "N";
			$pageTitle = "Корзина";
		}
		else{
			Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'not-found-page');
            throw new Zend_Exception('Not found', 404);
		}*/
		$delay = "N";
		$pageTitle = "Корзина";
		$basketModel = new Sibirix_Model_Basket();
		
		$basketData = $basketModel->getBasket($delay);

		$this->view->assign([
            "pageTitle" => $pageTitle,
            //"filter"    => $filter,
            "itemList"  => $basketData['basketItems'],
            //'paginator' => EHelper::getPaginator($result->pageData->totalItemsCount, $result->pageData->current, $result->pageData->size),
        ]);
    }
	
	public function favouriteAction() {
		Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'profile-inner');
        Zend_Registry::get('BX_APPLICATION')->SetTitle('Профиль');
		$type = $this->getParam('type');
		$basketModel = new Sibirix_Model_Basket();

		if($type == 'goods'){
			$delay = "Y";
			$pageTitle = "Избранные товары";
			$basketData = $basketModel->getBasket($delay);
			$favoutiteData = array();
			foreach($basketData['basketItems'] as $item){
				$result = CIBlockElement::GetByID($item['PRODUCT_ID'])->Fetch();
				if($result['IBLOCK_ID'] == IB_GOODS){
					array_push($favoutiteData, $item);
				}
			}
		}
		else if($type == 'design'){
			$delay = "Y";
			$pageTitle = "Избранные дизайны";
			$basketData = $basketModel->getBasket($delay);
			$favoutiteData = array();
			foreach($basketData['basketItems'] as $item){
				$result = CIBlockElement::GetByID($item['PRODUCT_ID'])->Fetch();
				if($result['IBLOCK_ID'] == IB_DESIGN){
					array_push($favoutiteData, $item);
				}
			}
		}
		else{
			Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'not-found-page');
            throw new Zend_Exception('Not found', 404);
		}

		$this->view->assign([
            "pageTitle" => $pageTitle,
            //"filter"    => $filter,
            "itemList"  => $favoutiteData,
            //'paginator' => EHelper::getPaginator($result->pageData->totalItemsCount, $result->pageData->current, $result->pageData->size),
        ]);
    }
	
	public function orderAction() {
		Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'profile-inner');
		Zend_Registry::get('BX_APPLICATION')->SetTitle('Мои заказы');
    }
	
    public function typeAction() {
        if ($this->user->getType() !== UNDEFINED_TYPE_ID) {
            Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'not-found-page');
            throw new Zend_Exception('Not found', 404);
        }

        $form = new Sibirix_Form_ProfileType();

        if (!Sibirix_Model_User::isAuthorized()) {
            $this->_helper->json(['success' => false, 'errorFields' =>  ['profileType' => 'Пользователь не авторизован']]);
        }

        if ($form->isValid($this->getAllParams())) {
            $model = new Sibirix_Model_User();
            $type = $form->getValue('profileType');
	
            $result = $model->updateType($this->user, $type);
			
            if ($result === true) {
                $this->_helper->json(['success' => true]);
            } else {
                foreach ($result as $ind => $error) {
                    $form->getElement($ind)->addErrors($result);
                }
                $form->markAsError();
                $form->getFieldsErrors();
                $this->_helper->json(['success' => false, 'errorFields' =>  $form->formErrors]);
            }
        } else {
            $form->getFieldsErrors();
        }

        $this->_helper->json(['success' => false, 'errorFields' =>  $form->formErrors]);
    }
	
}