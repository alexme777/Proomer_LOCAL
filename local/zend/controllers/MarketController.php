<?php

class MarketController extends Sibirix_Controller {
	
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
		//ini_set('error_reporting', E_ALL);
		//ini_set('display_errors', 1);
		//ini_set('display_startup_errors', 1);
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'profile-inner');
        Zend_Registry::get('BX_APPLICATION')->SetTitle('Профиль');
		
		//$cat_model = new Sibirix_Model_Categories;
		$model = new Sibirix_Model_Goods;
		$categories = $model->getCat();
		
		//если был тык на "Добавить"
		if(!empty($this->getParam('addgoods'))){
			
			//$data - массив будет содержать все св-ва
			$data = array();
			$error = array();
			$data['NAME'] = $this->getParam('name');
			
			if(!$data['NAME']) $error['NAME'] = 'Это поле необходимо заполнить.';
			
			//PROPERTY_VALUES - массив содержащий пользовательские св-ва
			$data['PROPERTY_VALUES'] = array();
			$data['IBLOCK_SECTION_ID'] = $this->getParam('category');
			if(!$data['IBLOCK_SECTION_ID']) $error['IBLOCK_SECTION_ID'] = 'Выберите категорию.';
			//Габариты
			$profile = 0;
			$length = $this->getParam('length');
			$width = $this->getParam('width');
			$height = $this->getParam('height');
			$data['PROPERTY_VALUES']['LENGTH'] = $length;		
			$data['PROPERTY_VALUES']['WIDTH'] = $width;
			$data['PROPERTY_VALUES']['HEIGHT'] = $height;
			
			if(!$data['PROPERTY_VALUES']['LENGTH'] || 
			   !$data['PROPERTY_VALUES']['WIDTH']  ||
			   !$data['PROPERTY_VALUES']['HEIGHT']) 
			$error['PROPERTY_VALUES']['PROFILE'] = 'Это поле необходимо заполнить.';
			
			$data['PROPERTY_VALUES']['STATUS'] = 16;//16 - на модерации, 15 - опубликован, 17 - отклонен, 18 - ожидание
			$data['PROPERTY_VALUES']['PROFILE'] = $length * $width * $height;
			$data['PROPERTY_VALUES']['MATERIAL'] = $this->getParam('material');
			
			if(!$data['PROPERTY_VALUES']['MATERIAL']) $error['PROPERTY_VALUES']['MATERIAL'] = 'Это поле необходимо заполнить.';
			
			$data['PROPERTY_VALUES']['PRICE'] = $this->getParam('price');
			
			if(!$data['PROPERTY_VALUES']['PRICE']) $error['PROPERTY_VALUES']['PRICE'] = 'Это поле необходимо заполнить.';
			
			$data['PROPERTY_VALUES']['ARTICLE'] = $this->getParam('article');
			
			if(!$data['PROPERTY_VALUES']['ARTICLE']) $error['PROPERTY_VALUES']['ARTICLE'] = 'Это поле необходимо заполнить.';
			
			//для привязки товара к пользователю
			$data['PROPERTY_VALUES']['ID_USER'] = $this->user->ID;
			
			$uploaddir_img = $_SERVER['DOCUMENT_ROOT'].'/upload/goods/';
			$uploaddir_img_small = $_SERVER['DOCUMENT_ROOT'].'/upload/goods/small/';
			//картинка
			$realfile_img = $_FILES['image']['name'];
			//если есть картинка
			if($realfile_img){
				//что-то делаем с картинкой
				$infoimg = getimagesize($_FILES['image']['tmp_name']);
				$ext =  substr(strrchr($realfile_img, '.'), 1);
				if(strtolower($ext)!='png' and strtolower($ext)!='jpg' and  strtolower($ext)!='jpeg' and  strtolower($ext)!='gif' )
				$error['PROPERTY_VALUES']['IMG'] = 'Возможна загрузка файлов только с расширением png, jpg, jpeg, gif<br>';
				if( $_FILES['image']['size'] >= 8*1024*1024*3)  $error .= 'Загружаемый файл \"'.$_FILES['image']['tmp_name'].'\" больше 3Мб<br>';
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
					$data['photo'] = $filename_img;
					chmod($_FILES['image']['tmp_name'], 0666);
					if(@move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)){
						
						require_once($_SERVER['DOCUMENT_ROOT'].'/local/include/SimpleImage.php');
						
						$image = new SimpleImage();
						$image->load($uploaddir_img . $filename_img);
						$image->resize(450, 300);
						$image->save($uploaddir_img_small . $filename_img);
					};
					$data['PROPERTY_VALUES']['IMG'] = '/upload/goods/' . $filename_img;
				};
			}
			else{

			};
		
			$filterParams = $this->getAllParams($this->user->ID);
			$model -> addGoods($data);
			$this->view->error = $error;
			$this->view->data = $data;
			//$this->redirect('/goods');
			/*$this->redirect('/goods');
			if ($categories) {
				$this->redirect('/goods');
			}
			else{
				$this->redirect('/goods');
			}*/
			
		};
     

		//Добавим в элементы категорю
		$row_goods = $model -> getGoods($this->user->ID);
			
		foreach($row_goods['ITEMS'] as $row){
			
			$row->IBLOCK_SECTION_NAME = '';
	
			//$row=>IBLOCK_SECTION_ID = '123123';
			//array_push($row[0], 'IBLOCK_SECTION_NAME' => '22222');
			foreach($categories as $cat){
				
				if($row->IBLOCK_SECTION_ID == $cat->ID){
					$row->IBLOCK_SECTION_NAME = $cat->NAME; 
					
				};
		
			};
		};

        $this->view->user = $this->user;
		$this->view->categories = $categories;
		$this->view->row_goods = $row_goods;
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
	
		$uploaddir = $_SERVER['DOCUMENT_ROOT'].'/upload/files/';
		//yml
		$realfile = $_FILES['files']['name'];
	
		//если есть файл
		if($realfile){
			//что-то делаем с файлом
			$infoimg = getimagesize($_FILES['files']['tmp_name']);
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
					echo $uploaddir . $filename;
					exit;
					/*include($_SERVER['DOCUMENT_ROOT'].'/local/include/XMLReader.php');
						echo "<pre>";
						$file = $uploaddir . $filename;
						$reader = new XmlReader();
						$reader->open($file);
						$reader->parse();
						$reader->close();
						exit;*/
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

            $result = $model->updateDesignerProfile($this->user, $name, $lastName, $phone, $email, $portfolio, $about, $styles);

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