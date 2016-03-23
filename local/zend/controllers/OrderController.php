<?

/**
 * Контроллер страницы оформления заказа
 * Class OrderController
 */
class OrderController extends Sibirix_Controller {

    /**
     * @var Sibirix_Model_Order
     */
    protected $_model;

    public function init() {
        $this->_model  = new Sibirix_Model_Order();
    }
	public function addAction() {
        $form = new Sibirix_Form_Order();
		$basket = new Sibirix_Model_Basket();
		$order = new Sibirix_Model_Order();
        $formData = $this->getAllParams();

        if ($form->isValid($formData)) {
            $validData = $form->getValues();
			$data = array();
			$data['PROPERTY_VALUES']['ID_GOODS'] = $validData['goodsId'];
			
			$model_goods = new Sibirix_Model_Goods();
			$item = $model_goods->select(['CREATED_BY'], false)->where(['ID'=>$validData['goodsId']], false)->getElement();
			$data['PROPERTY_VALUES']['ID_USER'] = $item->CREATED_BY;
			$data['PROPERTY_VALUES']['FIRST_NAME'] = $validData['firstname'];
			$data['PROPERTY_VALUES']['COUNT'] = $validData['count'];
			$data['PROPERTY_VALUES']['SECOND_NAME'] = $validData['secondname'];
			$data['PROPERTY_VALUES']['PHONE'] = $validData['phone'];
			$data['PROPERTY_VALUES']['EMAIL'] = $validData['email'];
			//$data['PROPERTY_VALUES']['MATHERIAL'] = $validData['matherialId'];
			//$data['PROPERTY_VALUES']['COLOR'] = $validData['colorId'];
			//$data['PROPERTY_VALUES']['SIZE'] = $validData['sizeId'];
            if (!$form->antiBotCheck()) {
                $this->_response->stopBitrix(true);
                $this->_helper->viewRenderer->setNoRender();
                return false;
            }
			  /*=========================================================/
			 /	Расчитаем цену с учетом торговых предложений			/
			/*========================================================*/
			//торговое предложение для цвета
			$offerColorPrice = CPrice::GetBasePrice($validData['colorId']);
			//торговое предложение для размера
			$offerSizePrice = CPrice::GetBasePrice($validData['sizeId']);
			//торговое предложение для материала
			$offerMaterialPrice = CPrice::GetBasePrice((int)$validData['materialId']);
			//узнаем базовую цену для нашего товара
			$product = $model_goods->getGoodsAItem($validData['goodsId']);
			//теперь сравним базовую цену с ценами св-св торговых предложений и получим итоговую цену
			$bases_price = $product->PROPERTY_PRICE_VALUE;
			$new_price = $bases_price;
			$new_price += $offerColorPrice['PRICE'];
			$new_price += $offerSizePrice['PRICE'];
			$new_price += $offerMaterialPrice['PRICE'];
			if(isset($product->DISCOUNT)){
				$discount = ($new_price/100) * $product->DISCOUNT['VALUE'];
				$new_price = $new_price - $discount;
			};
			$new_price = $new_price * $validData['count'];
	
			$productSecondName = '';
			if(!empty($validData['colorName']) && !empty(sizeName) && !empty($validData['materialName'])){
				$productSecondName = '('.$validData['colorName'].','.$validData['sizeName'].','.$validData['materialName'].')';
			};
	
			$data['NAME'] = $product->NAME .$productSecondName;
			$data['PROPERTY_VALUES']['PRICE'] = $new_price;
			$data['PREVIEW_PICTURE'] = CFile::MakeFileArray($product->PREVIEW_PICTURE['SRC']); 
			$data['DETAIL_PICTURE'] = CFile::MakeFileArray($product->DETAIL_PICTURE['SRC']);
			$result = $order->add($data);

			//добавляет параметры товара
			$arFields = array(
				"ID" => $result, 
				"VAT_ID" => 1, //тип ндс
				"VAT_INCLUDED" => "Y" //НДС входит в стоимость
            );
			
			CCatalogProduct::Add($arFields);
   
		   // Установление цены для товара
		   $PRICE_TYPE_ID = 1;

		   $arFields = array(
				"PRODUCT_ID" => $result,
				"CATALOG_GROUP_ID" => $PRICE_TYPE_ID,
				"PRICE" => $new_price,
				"CURRENCY" => "RUB",
				"QUANTITY_FROM" => 1,
				"QUANTITY_TO" => 10
		   );
   
		   CPrice::Add($arFields);
			  
			  /*=========================================================/
			 /	Берем товары из корзины									/
			/*========================================================*/
			$delay = "N";
			$basketData = $basket->getBasket($delay);
			//Очистим корзину
			foreach($basketData['basketItems'] as $data){
				CSaleBasket::Delete($data['ID']);
			};
			//Добавим в корзину
			$designId = $validData['goodsId'];
			$fields['DELAY'] = 'N';
		
			$addResult = Add2BasketByProductID($result, $validData['count'], array(), array());
		
			/*$addResult = $basket->addGoods($designId, $validData['count'], $fields, array(
                        array("NAME" => "Цвет", "CODE" => "CLR", "VALUE" => $validData['colorId']),
                        array("NAME" => "Размер", "VALUE" => $validData['sizeId']),
						array("NAME" => "Материал", "VALUE" => $validData['materialId'])
                    ));*/
			  /*=========================================================/
			 /	Добавляем заказ											/
			/*========================================================*/
            $addResult = $this->_model->makeAOrder($data);
	
			foreach($basketData['basketItems'] as $data){
				//$addResult = $basket->addGoods($data['PRODUCT_ID'], 1, array());
				Add2BasketByProductID($data['PRODUCT_ID'], 1, array(), array());
			};
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

        $this->_helper->json(['success' => !$form->issetError(), 'errorFields' => $form->formErrors]);
    }
	
    public function processAction() {
        $form         = new Sibirix_Form_Order();
        $formData     = $this->getAllParams();
        $errorsFormat = [];
        $orderResult  = false;

        if (empty($errorsFormat)) {
            if ($form->isValid($formData)) {

                $orderResult = $this->_model->makeOrder($formData);

                if (!empty($orderResult["ERROR"])) {
                    $errorsFormat[] = [
                        "fieldId" => "NAME",
                        "message" => $orderResult["ERROR"]
                    ];
                }
            } else {
                $errors       = $form->getElementMessages();
                $errorsFormat = $form->formatZendValidator($errors);
            }
        }

        $this->_helper->json(['success' => (empty($errorsFormat) && $orderResult > 0), 'errorFields' => $errorsFormat, 'orderId' => $orderResult]);
    }


    public function successAction() {
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'basket');
        Zend_Registry::get('BX_APPLICATION')->SetTitle('Оплата заказа');

        $orderId = $this->getParam('orderId');
        $orderData = $this->_model->getElement(["filter" => ["ID" => $orderId]]);

        if(!$orderData) {
            throw new Zend_Exception('Not found', 404);
        }

        // Не принадлежит пользователю или оплачен
        if(Sibirix_Model_User::isAuthorized()) {
            if ($orderData->USER_ID != Sibirix_Model_User::getId() || $orderData->PAYED == 'Y') {
                throw new Zend_Exception('Not found', 404);
            }
        } else {
            throw new Zend_Exception('Not found', 404);
        }

        $this->view->orderData  = $orderData;
    }

    public function paymentSuccessAction() {
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'basket');
        Zend_Registry::get('BX_APPLICATION')->SetTitle('Оплата прошла успешно');
    }

    public function paymentFailAction() {
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'basket');
        Zend_Registry::get('BX_APPLICATION')->SetTitle('Ошибка при оплате');
    }
}