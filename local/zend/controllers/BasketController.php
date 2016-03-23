<?

/**
 * Class BasketController
 */

class BasketController extends Sibirix_Controller {

    /**
     * @var Sibirix_Model_Basket
     */
    protected $_model;


    public function init() {
		
        $this->_model = new Sibirix_Model_Basket();
    }

    public function indexAction() {
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'basket');
        Zend_Registry::get('BX_APPLICATION')->SetTitle('Корзина заказа');

        $basketData = $this->_model->getBasket('N');
		
        $this->view->assign([
            "basketItems" => $basketData["basketItems"],
            "basketTotal" => $basketData["basketTotal"]
        ]);
    }

    public function addDesignAction() {
        if (!check_bitrix_sessid()) {
            $this->_helper->json(['success' => false]);
        }

        $designId = $this->getParam("designId");
		$fields['DELAY'] = $this->getParam("delay");
        $addResult = $this->_model->addDesign($designId, 1, $fields);
        $result = $this->_model->getBasketTotal();
        if ($addResult > 0) {
            $success = true;
            $item = $this->_model->getItem($addResult);
        } else {
            $success = false;
        }

        $this->_helper->json(['success' => $success, 'basketCnt' => $result['basketTotal']['totalCount'], 'totalPrice' => $result['basketTotal']['totalPrice'], 'item' => $item]);
    }
	
	public function addProjectAction() {
        if (!check_bitrix_sessid()) {
            $this->_helper->json(['success' => false]);
        }

        $projectId = $this->getParam("designId");
		$fields['DELAY'] = $this->getParam("delay");
        $addResult = $this->_model->addProject($projectId, 1, $fields);
	
        $result = $this->_model->getBasketTotal();
	
        if ($addResult > 0) {
            $success = true;
            $item = $this->_model->getItemProject($addResult);
        } else {
            $success = false;
        }

        $this->_helper->json(['success' => $success, 'basketCnt' => $result['basketTotal']['totalCount'], 'totalPrice' => $result['basketTotal']['totalPrice'], 'item' => $item]);
    }
		
	public function addGoodsAction() {
        if (!check_bitrix_sessid()) {
            $this->_helper->json(['success' => false]);
        }

        $designId = $this->getParam("designId");
		$fields['DELAY'] = $this->getParam("delay");
        $addResult = $this->_model->addGoods($designId, 1, $fields);
        $result = $this->_model->getBasketTotal();
        if ($addResult > 0) {
            $success = true;
            $item = $this->_model->getItemShop($addResult);
        } else {
            $success = false;
        }

        $this->_helper->json(['success' => $success, 'basketCnt' => $result['basketTotal']['totalCount'], 'totalPrice' => $result['basketTotal']['totalPrice'], 'item' => $item]);
    }

    public function deleteDesignAction() {
        if (!check_bitrix_sessid()) {
            $this->_helper->json(['success' => false]);
        }

        $designId = $this->getParam("designId");
        $result = $this->_model->deleteDesign($designId);
        $this->_helper->json(['success' => ($result !== false), 'basketCnt' => $result['basketTotal']['totalCount'], 'totalPrice' => $result['basketTotal']['totalPrice']]);
    }

    public function deleteGoodsAction() {
        if (!check_bitrix_sessid()) {
            $this->_helper->json(['success' => false]);
        }

        $designId = $this->getParam("designId");
        $result = $this->_model->deleteGoods($designId);

        $this->_helper->json(['success' => ($result !== false), 'basketCnt' => $result['basketTotal']['totalCount'], 'totalPrice' => $result['basketTotal']['totalPrice']]);
    }
}
