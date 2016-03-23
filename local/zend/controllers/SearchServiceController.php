<?

/**
 * Контроллер Сервиса поиска дизайна для квартиры
 * Class SearchServiceController
 */
class SearchServiceController extends Sibirix_Controller {

     public function indexAction() {
        $formStep = new Sibirix_Form_SearchService_Step1();
        $this->view->formStep = $formStep;

        $this->_response->stopBitrix(true);
    }

    public function addIndexAction() {
        $formStep = new Sibirix_Form_SearchService_Step1();
        $this->view->formStep = $formStep;

        $this->_response->stopBitrix(true);
    }

    /**
     *  Step one actions
     */
    public function step1Action() {
        $formStep = new Sibirix_Form_SearchService_Step1();
        $defaultValues = $this->getAllParams();

        $complexModel = new Sibirix_Model_Complex();
        if ($defaultValues["complexId"] > 0) {
            if (empty($defaultValues["complexName"]) || $defaultValues["complexName"] == "false") {
                $complexList = $complexModel->where(["ID" => $defaultValues["complexId"]])->select(array("ID", "NAME", "PROPERTY_CITY"), true)->getElements();
                $defaultValues["complexName"] = current($complexList)->NAME;
            }

            $houseModel = new Sibirix_Model_House();
            $complexList  = $complexModel->select(array("ID", "NAME", "PROPERTY_CITY"), true)
                ->where([
                    [
                        "LOGIC" => "OR",
                        ["NAME" => "%" . $defaultValues["complexName"] . "%"],
                        ["PROPERTY_LOCATION" => "%" . $defaultValues["complexName"] . "%"],
                        ["ID" => $houseModel->orderBy([], true)->select(['PROPERTY_COMPLEX'], true)->where(['PROPERTY_STREET' => "%" . $defaultValues["complexName"] . "%"])->asSubQuery()]
                    ],
                    "PROPERTY_CITY" => Sibirix_Model_User::getUserLocation()
                ])
                ->getElements();

            $complexList = EHelper::prepareForForm($complexList, "none");
            $formStep->complexId->setMultiOptions($complexList);
        }
        $formStep->populate($defaultValues);

        $this->view->formStep = $formStep;
        $this->_response->stopBitrix(true);
    }

    /**
     * Поиск комплекса по названию
     */
    public function searchComplexAction() {
        $formStep = new Sibirix_Form_SearchService_Step1();
        $formStep->populate($this->getAllParams());

        $complexModel = new Sibirix_Model_Complex();
        $houseModel   = new Sibirix_Model_House();

        $complexList  = $complexModel->select(array("ID", "NAME", "PROPERTY_CITY"), true)
            ->where([
                [
                    "LOGIC" => "OR",
                    ["NAME" => "%" . $formStep->getValue("complexName") . "%"],
                    ["PROPERTY_LOCATION" => "%" . $formStep->getValue("complexName") . "%"],
                    ["ID" => $houseModel->orderBy([], true)->select(['PROPERTY_COMPLEX'], true)->where(['PROPERTY_STREET' => "%" . $formStep->getValue("complexName") . "%"])->asSubQuery()]
                ],
                "PROPERTY_CITY" => Sibirix_Model_User::getUserLocation()
            ])
            ->getElements();

        $complexList = EHelper::prepareForForm($complexList, "none");

        $this->view->itemList = $complexList;
        $this->_response->stopBitrix(true);
    }

    /**
     *  Step two actions
     */
    public function step2Action() {
        $prevForm = new Sibirix_Form_SearchService_Step1();
        $prevForm->populate($this->getAllParams());
        $complexId = $prevForm->getValue("complexId");

        $complexModel = new Sibirix_Model_Complex();
        $complex = $complexModel->select(["ID", "NAME", "PROPERTY_COMPLEX_PLAN"], true)->getElement($complexId);
        $complexModel->getImageData($complex, "PROPERTY_COMPLEX_PLAN_VALUE");

        $houseModel = new Sibirix_Model_House();
        $houseList = $houseModel->where(["PROPERTY_COMPLEX" => $complexId])->select(["ID", "NAME", "PROPERTY_STREET", "PROPERTY_HOUSE_NUMBER", "PROPERTY_COORDS", "PROPERTY_PIN_TEXT"], true)->getElements();

        $complexPinList = array();
        foreach ($houseList as $house) {
            if (empty($house->PROPERTY_COORDS_VALUE)) continue;
            $complexPinList[] = array(
                "HOUSE"  => $house->ID,
                "COORDS" => $house->PROPERTY_COORDS_VALUE,
                "TEXT"   => $house->PROPERTY_PIN_TEXT_VALUE
            );
        }

        $formStep = new Sibirix_Form_SearchService_Step2();
        $defaultValues = $this->getAllParams();

        if ($defaultValues["house"] > 0) {
            $entranceModel = new Sibirix_Model_Entrance();
            $entranceList = $entranceModel->where(["PROPERTY_HOUSE" => $defaultValues["house"]])->select(["ID", "NAME"], true)->getElements();

            $entranceList = EHelper::prepareForForm($entranceList, true, "NAME");
            $formStep->entrance->setMultiOptions($entranceList);
        }

        if ($defaultValues["entrance"] > 0) {
            $floorModel = new Sibirix_Model_Floor();
            $floorList = $floorModel->where(["PROPERTY_ENTRANCE" => $defaultValues["entrance"]])->select(["ID", "NAME"], true)->getElements();

            $floorList = EHelper::prepareForForm($floorList, true, "NAME");
            $formStep->floor->setMultiOptions($floorList);
        }

        $formStep->populate($defaultValues);

        $houseList = EHelper::prepareForForm($houseList, true, "ADDRESS");
        $formStep->house->setMultiOptions($houseList);

        $formStep->complexName->setValue($complex->NAME);
        $formStep->complexId->setValue($complexId);

        $this->view->complexPlanImg = $complex->PROPERTY_COMPLEX_PLAN_VALUE;
        $this->view->complexPinList = $complexPinList;

        $this->view->formStep = $formStep;
        $this->_response->stopBitrix(true);
    }

    /**
     * Поиск подъездов по дому
     */
    public function entranceHouseAction() {
        $formStep = new Sibirix_Form_SearchService_Step2();
        $formStep->populate($this->getAllParams());

        $entranceModel = new Sibirix_Model_Entrance();
        $entranceList = $entranceModel->where(["PROPERTY_HOUSE" => $formStep->getValue("house")])->select(["ID", "NAME"], true)->getElements();

        $entranceList = EHelper::prepareForForm($entranceList, true, "NAME", true);
        $this->_helper->json(['entranceList' => $entranceList]);
    }

    /**
     * Поиск этажей по подъезду
     */
    public function floorEntranceAction() {
        $formStep = new Sibirix_Form_SearchService_Step2();
        $formStep->populate($this->getAllParams());

        $floorModel = new Sibirix_Model_Floor();
        $floorList = $floorModel->where(["PROPERTY_ENTRANCE" => $formStep->getValue("entrance")])->select(["ID", "NAME"], true)->getElements();

        $floorList = EHelper::prepareForForm($floorList, true, "NAME", true);
        $this->_helper->json(['floorList' => $floorList]);
    }

    /**
     *  Step three actions
     */
    public function step3Action() {
        $values = $this->getAllParams();

        $prevForm = new Sibirix_Form_SearchService_Step2();

        $houseModel = new Sibirix_Model_House();

        $houseList = array();
        if ($values["complexId"] > 0) {
            $houseList = $houseModel->where(["PROPERTY_COMPLEX" => $values["complexId"]])->select(["ID", "NAME", "PROPERTY_STREET", "PROPERTY_HOUSE_NUMBER"], true)->getElements();
        }

        $currentHouse = array_filter($houseList, function ($obj) use ($values) {
            return ($obj->ID == $values["house"]);
        });

        $currentHouse = current(array_filter($currentHouse));

        $houseList  = EHelper::prepareForForm($houseList, true, "ADDRESS");

        $entranceModel = new Sibirix_Model_Entrance();
        $entranceList  = $entranceModel->where(["PROPERTY_HOUSE" => $values["house"]])->select(["ID", "NAME"], true)->getElements();
        $entranceList  = EHelper::prepareForForm($entranceList);

        $floorModel = new Sibirix_Model_Floor();
        $floorList  = $floorModel->where(["PROPERTY_ENTRANCE" => $values["entrance"]])->select(["ID", "NAME", "PROPERTY_FLOOR_PLAN"], true)->getElements();
        $floorPlan  = EHelper::prepareForForm($floorList, "none", "PROPERTY_FLOOR_PLAN_VALUE")[$values["floor"]];
        $floorList  = EHelper::prepareForForm($floorList);

        $prevForm->house->setMultiOptions($houseList);
        $prevForm->entrance->setMultiOptions($entranceList);
        $prevForm->floor->setMultiOptions($floorList);

        $prevForm->populate($values);
        $validValues = $prevForm->getValues();

        $flatModel = new Sibirix_Model_Flat();
        $flatList  = $flatModel->where(["PROPERTY_FLOOR" => $validValues["floor"]])->select(["ID", "NAME", "PROPERTY_PLAN.PROPERTY_AREA", "PROPERTY_COORDS", "PROPERTY_PIN_TEXT"], true)->getElements();

        $floorPinList = array();
        foreach ($flatList as $flat) {
            if (empty($flat->PROPERTY_COORDS_VALUE)) continue;
            $floorPinList[] = array(
                "FLAT"  => $flat->ID,
                "COORDS" => $flat->PROPERTY_COORDS_VALUE,
                "TEXT"   => $flat->PROPERTY_PIN_TEXT_VALUE
            );
        }

        $flatSquareList = EHelper::prepareForForm($flatList, "none", "PROPERTY_PLAN_PROPERTY_AREA_VALUE");
        $flatList       = EHelper::prepareForForm($flatList, false);

        $complex = false;
        if ($values["complexId"] > 0) {
            $complexModel = new Sibirix_Model_Complex();
            $complex      = $complexModel->select(["ID", "NAME" ], true)->getElement($values["complexId"]);
        }


        $formStep = new Sibirix_Form_SearchService_Step3();
        $formStep->populate($values);
        $formStep->flat->setMultiOptions($flatList);

        $this->view->floorPlanImg = EHelper::getFileData($floorPlan);
        $this->view->floorPinList = $floorPinList;

        $formStep->complexName->setValue($complex->NAME);
        $formStep->houseAddress->setValue("ул." . $currentHouse->PROPERTY_STREET_VALUE . ", <span>" . $currentHouse->PROPERTY_HOUSE_NUMBER_VALUE . "</span>");
        $formStep->entranceInfo->setValue("ПОДЪЕЗД <span>" . $entranceList[$values["entrance"]] . "</span>");
        $formStep->floorInfo->setValue("ЭТАЖ <span>" . $floorList[$values["floor"]] . "</span>");
        $formStep->square->setValue('Площадь: <span class="js-flat-square-value" data-flats=\'' . json_encode($flatSquareList) . '\'></span>М<sup>2</sup>');

        $this->view->formStep = $formStep;
        $this->_response->stopBitrix(true);
    }

    /**
     * Поиск плана по названию
     */
    public function searchPlanAction() {
        $formStep = new Sibirix_Form_SearchService_StepPlan();
        $formStep->populate($this->getAllParams());

        $planModel = new Sibirix_Model_Plan();
        $planList = $planModel->select(array("ID", "NAME"), true)
            ->where(["NAME" => "%" . $formStep->getValue("planName") . "%"])
            ->getElements();

        $planList = EHelper::prepareForForm($planList, "none");

        $this->view->itemList = $planList;
        $this->_response->stopBitrix(true);
    }
	/**
     * Поиск плана по адресу
     */
    public function searchPlanNameAction() {
		$ajaxContext = $this->_helper->getHelper('SibirixAjaxContext');
        $ajaxContext->addActionContext('search-plan-name', 'html')
            ->initContext();
		
        $address = $this->getParam('address');
        $planModel = new Sibirix_Model_House();
        $planList = $planModel->select(array("ID", "NAME", "PROPERTY_ADDRESS", "PROPERTY_STREET", "PROPERTY_HOUSE_NUMBER"), true)
            ->where(
			
				["PROPERTY_STREET" => "%" . $address . "%"]
			
			)->limit(5)
            ->getElements();

        //$planList = EHelper::prepareForForm($planList, "none");

		$this->view->assign([
            "itemList"  => $planList,
        ]);
       // $this->view->itemList = $planList;
        //$this->_response->stopBitrix(true);
    }
}