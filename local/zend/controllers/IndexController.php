<?

/**
 * Контроллер главной страницы
 * Class IndexController
 */
class IndexController extends Sibirix_Controller {

    public function indexAction() {
	
			//ini_set('error_reporting', E_ALL);
		//ini_set('display_errors', 1);
		//ini_set('display_startup_errors', 1);
		//echo '<H1>Ща все будет!</H1>';
		//exit;
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'main');
        Zend_Registry::get('BX_APPLICATION')->SetTitle("Proomer");
        $promoModel = new Sibirix_Model_Promo();
        $sliderModel = new Sibirix_Model_MainSlider();
        $complexModel = new Sibirix_Model_Complex();
		$designModel = new Sibirix_Model_Design();
		$modelRoom = new Sibirix_Model_Room();
		$goodsModel = new Sibirix_Model_Goods();
		$userModel = new Sibirix_Model_User();
		
		$modelSibirix_Model_Goods = new Sibirix_Model_Goods();
		$modelPin = new Sibirix_Model_Pin();
		$design = $designModel->where(["CODE" => "klassika"])->getElement();
		$design->ROOMS = $modelRoom->where(['PROPERTY_DESIGN' => $design->ID])->getElements();
		$modelSibirix_Model_Goods->getImageData($design->ROOMS, ["PROPERTY_IMAGES_VALUE"]);

		//Получаем пины
		foreach($design->ROOMS as $room){
			$room->PINS = $modelPin->getPin($room->ID);
			foreach($room->PINS as $pin){
				//x = 488px - 100%; y = 493px - 100%;
				$coords = explode(",", $pin->PROPERTY_COORDS_VALUE[0]);
				//$y_y = 569 / 493;
				//$x_x = 1024 / 488;
	
				$x = $coords[0] - 16;
				$y = $coords[1] + 16;
				$pin->X = $x;
				$pin->Y = $y;
			}
		}
		foreach($design->ROOMS as $room){
			$modelSibirix_Model_Goods->getImageData($room->PINS, ["PREVIEW_PICTURE"]);
		}
        $promo = $promoModel->getItems();
        $slides = $sliderModel->getItems();
        $complexes = $complexModel->getSliderList();
		
		//для блока мастеров
		$users = $userModel->getUserList(['GROUPS_ID'=>5], ['PERSONAL_PHOTO'=>'DESC'], 1, false, 6);

		foreach($users->items as $user){
			if (!empty($user->PERSONAL_PHOTO)) {
				$image = Resizer::resizeImage($user->PERSONAL_PHOTO, 'MAIN_PAGE_DESIGNERS_PREVIEW');
			} else {
				$image = '/local/images/proomer2.png';
			}
			$user->PERSONAL_PHOTO = $image;
		}
		
		/*===================================================*/
		
		$goods = $goodsModel->getProductList([], 'SHOW_COUNTER', 4);
        $this->view->assign([
            'slides' => $slides,
			'userList' => $users->items,
            'promo' => $promo,
            'complexes' => $complexes,
			'design' => $design,
			'goods' => $goods->items
        ]);
			
    }

    public function sliderRefreshAction() {
        $complexId = $this->getParam('complexId');
        $elementCnt = Settings::getOption('slidesDesignsElementsCount', 12);
        $designModel = new Sibirix_Model_Design();
        $designs = $designModel->getSliderItems($complexId, $elementCnt);

        $this->view->designs = $designs;
        $this->view->complexId = $complexId;
        $this->_response->stopBitrix(true);
    }
	
}