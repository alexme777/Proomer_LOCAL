<?

/**
 * Class Sibirix_Model_Notification
 *
 */
class Sibirix_Model_Notification {

    /**
     * Форма обратной связи
     * @param $feedbackId
     * @param $repairFields
     * @param $emailTo
     * @param $theme
     */
    public function sendFeedback($feedbackId, $formFields, $emailTo, $theme) {
        $fields = [
            "FEEDBACK_ID" => $feedbackId,
            "EMAIL_TO"    => $emailTo,
            "THEME"       => $theme
        ];
        $this->sendNotification("NEW_FEEDBACK", array_merge($fields, $formFields));
    }

    /**
     * Оповещения при регистрации
     * @param $userId
     * @param $formFields
     * @param $emailTo
     */
    public function registrationSuccess($userId, $name, $email, $type) {
        $type = ($type == DESIGNER_TYPE_ID)?'Дизайнер': 'Клиент';

        $fields = [
            "USER_ID" => $userId,
            "EMAIL_TO" => Settings::getOption('NEW_USER_EMAIL_TO'),
            "TYPE" => $type,
            "NAME" => $name,
            "EMAIL" => $email,
            "THEME" => 'Регистрация на сайте Proomer'
        ];
        $this->sendNotification("NEW_USER", $fields);
    }

    public function statusModeraion($design, $designer) {
        $fields = [
            "DESIGN_ID" => $design->ID,
            "DESIGN_NAME" => $design->NAME,
            "EMAIL_TO" => Settings::getOption('MODERATION_EMAIL_TO'),
            "LAST_NAME" => $designer->LAST_NAME,
            "NAME" => $designer->NAME,
            "THEME" => 'Дизайн-проект был отправлен на модерацию'
        ];
        $this->sendNotification("STATUS_MODERATION", $fields);
    }

    public function statusPublished($design, $designer) {
        $fields = [
            "DESIGN_CODE" => $design->CODE,
            "DESIGN_NAME" => $design->NAME,
            "EMAIL_TO" => $designer->EMAIL,
            "LAST_NAME" => $designer->LAST_NAME,
            "NAME" => $designer->NAME,
            "THEME" => 'Ваш дизайн-проект прошел модерацию'
        ];
        $this->sendNotification("STATUS_PUBLISHED", $fields);
    }

    public function statusError($design, $designer, $comment) {
        $fields = [
            "DESIGN_CODE" => $design->CODE,
            "DESIGN_NAME" => $design->NAME,
            "EMAIL_TO" => $designer->EMAIL,
            "LAST_NAME" => $designer->LAST_NAME,
            "NAME" => $designer->NAME,
            "COMMENT" => $comment,
            "THEME" => 'Ваш дизайн-проект не прошел модерацию'
        ];
        $this->sendNotification("STATUS_ERROR", $fields);
    }

    public function newOrder($orderId) {
        $orderModel  = new Sibirix_Model_Order();
        $orderData = $orderModel->getElement(["filter" => ["ID" => $orderId]]);

        $designList = '';
        $designers = [];

        foreach ($orderData->PRODUCTS as $ind => $product) {
            $designList .= ($ind+1) . '. ' . $product['NAME'] . ' — ' . $product['PRICE'] . ' р<br>';
            $designers[] = $product['DESIGNER'];
        }

        $designerIds = array_map(function($obj){return $obj->ID;}, $designers);
        $designerIds = array_unique($designerIds);

        $designerList = [];
        foreach ($designers as $designer) {
            $designerList[$designer->ID] = $designer;
        }

        foreach ($designerIds as $designerId) {
            $designDesignerList = '';

            $counter = 0;
            foreach ($orderData->PRODUCTS as $product) {
                if ($product['DESIGNER']->ID == $designerId) {
                    $counter++;
                    $designDesignerList .= $counter . '. <a href="http://' . $_SERVER['HTTP_HOST'] . $product['DETAIL_PAGE_URL'] . '">' .
                        $product['NAME'] . '</a> — ' . $product['PRICE'] . ' р<br>';
                }
            }

            $fields = [
                "THEME_DESIGNER" => 'Ваш дизайн-проект был куплен',
                "ORDER_LIST" => $designDesignerList,
                "EMAIL_DESIGNER" => $designerList[$designerId]->EMAIL,
                "NAME_DESIGNER" => $designerList[$designerId]->getFullName(),
                'ORDER_ID' => $orderData->ID,
                'ORDER_DATE' => date('d/m/Y', strtotime($orderData->DATE_INSERT)),
                'ORDER_PRICE' => $orderData->PRICE . ' р',
                'SALE_EMAIL' => Settings::getOption('SALE_EMAIL'),
            ];

            $this->sendNotification("NEW_ORDER_DESIGNER", $fields);
        }


        $fields = [
            "THEME_ADMIN" => 'На сайте был оформлен новый заказ',
            "THEME_USER" => 'Вы оформили новый заказ на сайте',
            "EMAIL_ADMIN" => Settings::getOption('ORDER_EMAIL_TO'),
            "ORDER_LIST" => $designList,
            "EMAIL_USER" => $orderData->PROPS['EMAIL'],
            "PHONE_USER" => $orderData->PROPS['PHONE'],
            "NAME_USER" => trim($orderData->PROPS['NAME'] . ' ' . $orderData->PROPS['LAST_NAME']),
            'ORDER_ID' => $orderData->ID,
            'ORDER_DATE' => date('d/m/Y', strtotime($orderData->DATE_INSERT)),
            'ORDER_PRICE' => $orderData->PRICE . ' р',
            'SALE_EMAIL' => Settings::getOption('SALE_EMAIL'),
        ];

        $this->sendNotification("SALE_NEW_ORDER", $fields);
    }
	
	
	public function testOrder($orderId, $seller, $orderParams) {

		$orderModel  = new Sibirix_Model_Order();
        $orderData = $orderModel->getElement(["filter" => ["ID" => $orderId]]);

        $designList = '';
        $designers = [];
		
        foreach ($orderData->PRODUCTS as $ind => $product) {
            $designList .= ($ind+1) . '. ' . $product['NAME'] . ' — ' . $product['PRICE'] . ' р<br>';
            $designers[] = $product['DESIGNER'];
        }

        $designerIds = array_map(function($obj){return $obj->ID;}, $designers);
        $designerIds = array_unique($designerIds);

        $designerList = [];
		
        foreach($designers as $designer) {
            $designerList[$designer->ID] = $designer;
        }

		//письмо продавцу
		$fields = [	
			'ORDER_ID' => $orderId,
			'ORDER_DATE' => date('d/m/Y', strtotime($orderData->DATE_INSERT)),
			'ORDER_USER' => $orderData->USER_NAME .' '. $orderData->USER_LAST_NAME,
			'PRICE' => $orderData->PRICE . ' р',
			'EMAIL' => $seller['EMAIL'],
			'ORDER_LIST' => $designDesignerList,
			'SALE_EMAIL' => $orderData->PROPS['EMAIL']
		];
			
		$this->sendNotification("NEW_ORDER", $fields);
		
		//письмо клиенту
		$fields = [	
			'ORDER_ID' => $orderId,
			'ORDER_DATE' => date('d/m/Y', strtotime($orderData->DATE_INSERT)),
			'ORDER_USER' => $orderParams['PROPERTY_VALUES']['FIRST_NAME'] .' '. $orderParams['PROPERTY_VALUES']['SECOND_NAME'],
			'PRICE' => $orderData->PRICE . ' р',
			'EMAIL' => $orderParams['PROPERTY_VALUES']['EMAIL'],
			'ORDER_LIST' => $designDesignerList,
			'SALE_EMAIL' => Settings::getOption('SALE_EMAIL')
        ];

        $this->sendNotification("SALE_NEW_ORDER", $fields);	
    }
	
    /**
     * @param $eventId
     * @param $fields
     */
    private function sendNotification($eventId, $fields) {
        $bxEvent = new CEvent();
        $bxEvent->Send($eventId, "s1", $fields);
    }
}
