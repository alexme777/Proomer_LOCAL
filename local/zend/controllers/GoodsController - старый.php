<?php

class GoodsController extends Sibirix_Controller {
	
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
			$data['DETAIL_TEXT'] = $this->getParam('disc');
			if(!$data['NAME']) $error['NAME'] = 'Это поле необходимо заполнить.';
			
			//PROPERTY_VALUES - массив содержащий пользовательские св-ва
			$data['PROPERTY_VALUES'] = array();
			$data['IBLOCK_SECTION_ID'] = $this->getParam('category');
			$data['PROPERTY_VALUES']['COLOR'] = $this->getParam('color');
			$data['PROPERTY_VALUES']['MADEIN'] = $this->getParam('madein');
			$data['PROPERTY_VALUES']['STYLE'] = $this->getParam('style');
			if(!$data['IBLOCK_SECTION_ID']) $error['IBLOCK_SECTION_ID'] = 'Выберите категорию.';
			if(!$data['PROPERTY_VALUES']['COLOR']) $error['PROPERTY_VALUES']['COLOR'] = 'Выберите цвет.';
			if(!$data['PROPERTY_VALUES']['MADEIN']) $error[]['PROPERTY_VALUES']['MADEIN'] = 'Выберите производителя.';
			if(!$data['PROPERTY_VALUES']['STYLE']) $error['PROPERTY_VALUES']['STYLE'] = 'Выберите стиль.';
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
			if(!$data['DETAIL_TEXT']) $error['DETAIL_TEXT'] = 'Это поле необходимо заполнить.';
			
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
				$data['PREVIEW_PICTURE'] = $_FILES['image']['tmp_name'];
				$ext =  substr(strrchr($realfile_img, '.'), 1);
				if(strtolower($ext)!='png' and strtolower($ext)!='jpg' and  strtolower($ext)!='jpeg' and  strtolower($ext)!='gif' )
				$error['PROPERTY_VALUES']['IMG'] = 'Возможна загрузка файлов только с расширением png, jpg, jpeg, gif<br>';
				if( $_FILES['image']['size'] >= 8*1024*1024*3)  $error .= 'Загружаемый файл \"'.$_FILES['image']['tmp_name'].'\" больше 3Мб<br>';
			}
			//куда делась?
			else{
				//что-то случилось
				$error['PROPERTY_VALUES']['IMG'] = 'Не выбрано изображение';
			};
			
			
			
			
			 /**
         * Шаг 2
         */
        $step2Form = new Sibirix_Form_AddDesignStep2();
		
        $roomModel = new Sibirix_Model_Room();
		$designData = 1977;
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
				$model -> addGoods($data);
			}
			else{

			};
		
			$filterParams = $this->getAllParams($this->user->ID);
			$this->view->error = $error;
			$this->view->data = $data;

			
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
		//ini_set('error_reporting', E_ALL);
		//ini_set('display_errors', 1);
		//ini_set('display_startup_errors', 1);
		$uploaddir = $_SERVER['DOCUMENT_ROOT'].'/upload/files/';
		include($_SERVER['DOCUMENT_ROOT'].'/local/include/XMLValid.php');
		//yml
		$realfile = $_FILES['files']['name'];
		
		//если есть файл
		if($realfile){
			$validate = new XmlValidator();
			$xml = file_get_contents($_FILES['files']['tmp_name']);

			$xmlContent = file_get_contents($xmlFilename);

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