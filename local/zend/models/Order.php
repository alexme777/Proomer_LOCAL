<?

/**
 * Class Sibirix_Model_Order
 */
class Sibirix_Model_Order extends Sibirix_Model_Bitrix{

    /**
     * @var CSaleOrder
     */
	protected $_iblockId = IB_ORDERS;
    protected $_bxOrder;

    protected $_instanceClass = 'Sibirix_Model_Order_Row';

    public function init($initParams = NULL) {
        CModule::IncludeModule("sale");
        CModule::IncludeModule("catalog");
        $this->_bxOrder = new CSaleOrder();
    }

    public function getElement($params = []) {
        $params['limit'] = 1;
        $elements = $this->getElements($params);

        if (0 === count($elements)) {
            return false;
        }

        return $elements[0];
    }

    /**
     * @param array $params
     * @return array
     */
    public function getElements($params = []) {
        $this->_bxOrder = new CSaleOrder();

        if(is_array($params['filter'])) {
            $filter = $params['filter'];
        } else {
            $filter = ['USER_ID' => Sibirix_Model_User::getId()];
        }

        $dbRes = $this->_bxOrder->GetList(
            ["ID" => "DESC"], $filter, false, false, []
        );

        $elements = [];
        while ($item = $dbRes->Fetch()) {
            $elements[] = $item;
        }

        $elements = $this->_postProcessElements($elements, $params);

        return $elements;
    }

    /**
     * @param array $orders
     * @param array $params
     * @return array
     */
    protected function _postProcessElements(array $orders, $params = []) {

        $this->_collectionToObject($orders);

        // Id заказов для подгрузки связанных данных
        $orderIdList = [];
        foreach ($orders as $order) {
            $orderIdList[] = $order->ID;
        }
        if (!count($orderIdList)) {
            return $orders;
        }

        // Загрузка свойств заказа
        $bxSaleOrderProps = new CSaleOrderPropsValue();
        $res = $bxSaleOrderProps->GetList([], ['ORDER_ID' => $orderIdList]);
        $props = [];
        while ($prop = $res->Fetch()) {
            $props[$prop['ORDER_ID']][$prop['CODE']] = $prop['VALUE'];
        }
        foreach ($orders as $ind => $order) {
            $orders[$ind]->PROPS = $props[$order->ID];
        }

        // подгрузка элементов корзины
        $bxBasketModel = new Sibirix_Model_Basket();
        $basketItems = $bxBasketModel->getBasket($orderIdList)['basketItems'];

        foreach ($orders as $ind => $order) {
            if ($basketItems) {
                $orders[$ind]->PRODUCTS = array_filter($basketItems, function ($obj) use ($order) {
                    return $obj['ORDER_ID'] == $order->ID;
                });

            } else {
                $orders[$ind]->PRODUCTS = [];
            }
        }

        return $orders;
    }

    /**
     * @param $elements
     * @return $this
     * @throws Zend_Db_Exception
     */
    protected function _collectionToObject(&$elements) {
        if (!empty($this->_instanceClass)) {
            $elObj = new $this->_instanceClass();
        } else {
            $class = get_called_class() . '_Row';
            if (!class_exists($class)) {
                throw new Zend_Db_Exception('not found row class "' . $class . '"');
            }

            $elObj = new $class();
        }

        foreach ($elements as &$element) {
            // @var $_elObj self
            $_elObj = clone($elObj);
            $_elObj->setData($element);
            $element = $_elObj;
        }
        reset($elements);

        return $this;
    }

    /**
     * Создает новый заказ
     * @param $orderParams
     * @return bool
     */
    /*public function makeOrder($orderParams) {
        $basketModel  = new Sibirix_Model_Basket();
        $bxSaleBasket = new CSaleBasket();

        $currentUserId = Sibirix_Model_User::getId();
		if(!$currentUserId){
			$currentUserId = 1;
		}
        $prices    = $basketModel->getBasketTotal();
        $price = $prices["basketTotal"]["totalPrice"];

        $orderFields = [
            "LID"              => DEFAULT_SITE_ID,
            "PERSON_TYPE_ID"   => INDIVIDUAL,
            "PRICE"            => $price,
            "CURRENCY"         => "RUB",
            "USER_ID"          => $currentUserId,
            "PAY_SYSTEM_ID"    => PAY_SYSTEM_ID,
        ];
		
        $newOrder = $this->_bxOrder->Add($orderFields);
		$this->add($orderParams);
	
        if($newOrder) {
            $this->addOrderProps($newOrder, $orderParams);
            $bxSaleBasket->OrderBasket($newOrder, $bxSaleBasket->GetBasketUserID(), DEFAULT_SITE_ID);
            $result = $newOrder;
			$seller = CUser::GetByID($orderParams['PROPERTY_VALUES']['ID_USER'])->Fetch();
			//Уведомления
			$notificationModel = new Sibirix_Model_Notification();
			$notificationModel->testOrder($newOrder, $seller, $orderParams);
        } else {
            $result["ERROR"] = "Ошибка добавления заказа";
        }

        return $result;
    }
	 */
	    public function makeOrder($orderParams) {
        $basketModel  = new Sibirix_Model_Basket();
        $bxSaleBasket = new CSaleBasket();

        $currentUserId     = Sibirix_Model_User::getId();
        $prices    = $basketModel->getBasketTotal();
        $price = $prices["basketTotal"]["totalPrice"];

        $orderFields = [
            "LID"              => DEFAULT_SITE_ID,
            "PERSON_TYPE_ID"   => INDIVIDUAL,
            "PRICE"            => $price,
            "CURRENCY"         => "RUB",
            "USER_ID"          => $currentUserId,
            "PAY_SYSTEM_ID"    => PAY_SYSTEM_ID,
        ];

        $newOrder = $this->_bxOrder->Add($orderFields);

        if($newOrder) {
            $this->addOrderProps($newOrder, $orderParams);
            $bxSaleBasket->OrderBasket($newOrder, $bxSaleBasket->GetBasketUserID(), DEFAULT_SITE_ID);
            $result = $newOrder;

            //Уведомления
            $notificationModel = new Sibirix_Model_Notification();
            $notificationModel->newOrder($newOrder);
        } else {
            $result["ERROR"] = "Ошибка добавления заказа";
        }

        return $result;
    }
	 /**
     * Создает новый заказ для одного
     * @param $orderParams
     * @return bool
     */
    public function makeAOrder($orderParams) {
		
		$editResult = $this->updateValue($result, [], []);
		
        $basketModel  = new Sibirix_Model_Basket();
        $bxSaleBasket = new CSaleBasket();

        $currentUserId = Sibirix_Model_User::getId();
		if(!$currentUserId){
			$currentUserId = 1;
		}
        $prices    = $basketModel->getBasketTotal();
        $price = $prices["basketTotal"]["totalPrice"];

        $orderFields = [
            "LID"              => DEFAULT_SITE_ID,
            "PERSON_TYPE_ID"   => INDIVIDUAL,
            "PRICE"            => $price,
            "CURRENCY"         => "RUB",
            "USER_ID"          => $currentUserId,
            "PAY_SYSTEM_ID"    => PAY_SYSTEM_ID,
        ];
		
        $newOrder = $this->_bxOrder->Add($orderFields);
		
        if($newOrder) {
            $this->addOrderProps($newOrder, $orderParams);
            $bxSaleBasket->OrderBasket($newOrder, $bxSaleBasket->GetBasketUserID(), DEFAULT_SITE_ID);
            $result = $newOrder;
			$seller = CUser::GetByID($orderParams['PROPERTY_VALUES']['ID_USER'])->Fetch();
			//Уведомления
			$notificationModel = new Sibirix_Model_Notification();
			$notificationModel->testOrder($newOrder, $seller, $orderParams);
        } else {
            $result["ERROR"] = "Ошибка добавления заказа";
        }

        return $result;
    }

    /**
     * Добавляет значения свойств к заказу
     * @param $orderId
     * @param $orderParams
     */
    public function addOrderProps($orderId, $orderParams) {
        $bxOrderProps    = new CSaleOrderProps();
        $bxOrderPropsVal = new CSaleOrderPropsValue();

        $orderPropList    = [];
        $orderPropGetList = $bxOrderProps->GetList([], ["ACTIVE" => "Y"]);

        while ($orderProp = $orderPropGetList->Fetch()) {
            $orderPropList[] = [
                "ID"   => $orderProp["ID"],
                "NAME" => $orderProp["NAME"],
                "CODE" => $orderProp["CODE"],
            ];
        }

        foreach ($orderPropList as $orderProp) {
            $addFields = [
                "ORDER_ID"       => $orderId,
                "ORDER_PROPS_ID" => $orderProp["ID"],
                "NAME"           => $orderProp["NAME"],
                "CODE"           => $orderProp["CODE"],
                "VALUE"          => $orderParams[$orderProp["CODE"]]
            ];
            $bxOrderPropsVal->Add($addFields);
        }
    }
}
