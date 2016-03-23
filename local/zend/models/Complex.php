<?

/**
 * Class Sibirix_Model_Complex
 *
 */
class Sibirix_Model_Complex extends Sibirix_Model_Bitrix {

    protected $_iblockId = IB_COMPLEX;

    protected $_instanceClass = 'Sibirix_Model_Complex_Row';

    protected $_selectFields = array(
        'ID',
        'CODE',
        'NAME',
        'DETAIL_PICTURE',
        'PREVIEW_TEXT',
        'DETAIL_TEXT',
        'PROPERTY_DESIGN_CNT',
        'PROPERTY_CONSTRUCTOR',
        'PROPERTY_SIMILAR_COMPLEX'
    );

    protected $_selectListFields = [
        'ID',
        'CODE',
        'NAME',
        'DETAIL_PICTURE',
        'PREVIEW_TEXT',
        'PROPERTY_CONSTRUCTOR',
        'PROPERTY_AVERAGE_DESIGN_PRICE',
        'PROPERTY_DESIGN_CNT',
    ];

    public function init($initParams = NULL) {
        $this->_pageSize = Sibirix_Model_ViewCounter::getViewCounter();
    }

    /**
     * Перезаписывание актуального количества элементов на странице
     */
    public function reinitViewCounter() {
        $this->_pageSize = Sibirix_Model_ViewCounter::getViewCounter();
    }

    /**
     * Список комплексов
     * @param $filter
     * @param $sort
     * @param $page
     * @return object
     */
    public function getComplexList($filter, $sort, $page) {
        $complexList = $this->select($this->_selectListFields, true)->orderBy($sort, true)->where($filter)->getPage($page);

        $this->getImageData($complexList->items, "DETAIL_PICTURE");

        return $complexList;
    }

    /**
     * Формирование фильтрующего массива
     * @param $getParams
     * @return array
     */
    public function prepareFilter($getParams) {
        if (empty($getParams)) return array();

        $filterKeys = array("search", "popular", "complexSize", "avgPrice");
        foreach ($getParams as $paramKey => $param) {
            if (!in_array($paramKey, $filterKeys)) {
                unset($getParams[$paramKey]);
            }
        }

        if (empty($getParams)) return array();

        $filterArray = array();

        //По городу
        $filterArray["=PROPERTY_CITY"] = Sibirix_Model_User::getUserLocation();

        //По популярности
        if (!empty($getParams["popular"])) {
            $popularArray = explode("-", $getParams["popular"]);
            if ($popularArray[0] > 0) {
                $filterArray[">=PROPERTY_DESIGN_CNT"] = $popularArray[0];
            }
            if ($popularArray[1] > 0) {
                if (empty($popularArray[0])) {
                    $filterArray[] = array(
                        "LOGIC" => "OR",
                        ["<=PROPERTY_DESIGN_CNT" => $popularArray[1]],
                        ["=PROPERTY_DESIGN_CNT"  => false],
                    );
                } else {
                    $filterArray["<=PROPERTY_DESIGN_CNT"] = $popularArray[1];
                }

            }
        }

        //По средней цене
        if (!empty($getParams["avgPrice"])) {
            $avgPriceArray = explode(":", $getParams["avgPrice"]);
            if ($avgPriceArray[0] == 0) {
                $filterArray[] = array(
                    "LOGIC" => "OR",
                    [">=PROPERTY_AVERAGE_DESIGN_PRICE" => $avgPriceArray[0], "<=PROPERTY_AVERAGE_DESIGN_PRICE" => $avgPriceArray[1]],
                    ["=PROPERTY_AVERAGE_DESIGN_PRICE"  => false],
                );
            } else {
                $filterArray[">=PROPERTY_AVERAGE_DESIGN_PRICE"] = $avgPriceArray[0];
                $filterArray["<=PROPERTY_AVERAGE_DESIGN_PRICE"] = $avgPriceArray[1];
            }
        }

        //По размеру комплекса
        if (!empty($getParams["complexSize"])) {
            $complexSizeArray = explode("-", $getParams["complexSize"]);
            $cntFilter = array();
            if (!empty($complexSizeArray[0])) {
                $cntFilter[">=CNT"] = $complexSizeArray[0];
            }
            if (!empty($complexSizeArray[1])) {
                $cntFilter["<=CNT"] = $complexSizeArray[1];
            }

            $filterComplexList = $this->getHouseCount($cntFilter);
            if (empty($filterComplexList)) {
                $filterArray["ID"] = 0;
            } else {
                $filterArray["ID"] = array_keys($filterComplexList);
            }
        }

        //По названию ЖК или по адресу его дома
        if (!empty($getParams["search"])) {
            $houseModel = new Sibirix_Model_House();
            $filterArray[] = array(
                "LOGIC" => "OR",
                ["NAME" => "%".$getParams["search"]."%"],
                ["PROPERTY_LOCATION" => "%" . $getParams["search"] . "%"],
                ["ID" => $houseModel->orderBy([], true)->select(['PROPERTY_COMPLEX'], true)->where(['PROPERTY_STREET' => "%".$getParams["search"]."%"])->asSubQuery()]
            );
        }
        return $filterArray;
    }

    /**
     * Получает максимальную и минимальную среднюю цену дизайна
     * @return array
     */
    public function getExtremeAvgPrice() {
        $filter = [];
        $filter["=PROPERTY_CITY"] = Sibirix_Model_User::getUserLocation();
        $minAvgPrice = $this->select(["PROPERTY_AVERAGE_DESIGN_PRICE"], true)->where($filter)->orderBy(["PROPERTY_AVERAGE_DESIGN_PRICE" => "ASC"], true)->getElement();
        $maxAvgPrice = $this->select(["PROPERTY_AVERAGE_DESIGN_PRICE"], true)->where($filter)->orderBy(["PROPERTY_AVERAGE_DESIGN_PRICE" => "DESC"], true)->getElement();

        $result = array(
            "from" => (empty($minAvgPrice->PROPERTY_AVERAGE_DESIGN_PRICE_VALUE) ? 0 : $minAvgPrice->PROPERTY_AVERAGE_DESIGN_PRICE_VALUE),
            "to"   => (empty($maxAvgPrice->PROPERTY_AVERAGE_DESIGN_PRICE_VALUE) ? 100000 : $maxAvgPrice->PROPERTY_AVERAGE_DESIGN_PRICE_VALUE)
        );

        return $result;
    }

    /**
     * Количетсво домов в комплексе
     * @param array $filter
     * @return array
     */
    public function getHouseCount($filter = array()) {
        $modelHouse = new Sibirix_Model_House();
        $cacheTime = 60 * 60 * 24 * 30;
        $obCache = new CPHPCache();
        if ($obCache->InitCache($cacheTime, 'houseCntByComplex', '/houseCntByComplex/')) {
            $vars = $obCache->GetVars();
            $data = $vars['houseCntByComplex'];
        } else {
            $data = $modelHouse->orderBy(["CNT" => "DESC"], true)->uniqueBy("PROPERTY_COMPLEX")->getElements();

            if ($obCache->StartDataCache()) {
                $obCache->EndDataCache(array('houseCntByComplex' => $data));
            }
        }
        if (!empty($filter)) {
            if (!empty($filter["ID"]) && !is_array($filter["ID"])) {
                $filter["ID"] = [$filter["ID"]];
            }
        }

        $result = array();
        foreach ($data as $item) {
            if (!empty($filter["ID"]) && !in_array($item->PROPERTY_COMPLEX_VALUE, $filter["ID"])) continue;
            if (!empty($filter[">=CNT"]) && ($item->CNT < $filter[">=CNT"])) continue;
            if (!empty($filter["<=CNT"]) && ($item->CNT > $filter["<=CNT"])) continue;

            $result[$item->PROPERTY_COMPLEX_VALUE] = array(
                "ID"  => $item->PROPERTY_COMPLEX_VALUE,
                "CNT" => $item->CNT
            );
        }

        return $result;
    }

    /**
     * Кеширование средней стоимости и количество дизайнов
     * @param $fields
     * @return mixed
     */
    public static function cacheComplexData($fields) {
        if ($fields["IBLOCK_ID"] != IB_DESIGN) return $fields;

        //Получаем комплексы, к которым относится дизайн
        $designModel = new Sibirix_Model_Design();
        $complexList = $designModel->getComplexListByDesign($fields["ID"]);

        //Кэштруем каждый комплекс отдельно
        foreach ($complexList as $complex) {

            //Выбираем все дизайны комплекса
            /* @var $complex Sibirix_Model_Complex_Row */
            $publishedStatusId = EnumUtils::getListIdByXmlId(IB_DESIGN, "STATUS", "published");
            $designList = $complex->getDesignList(["PROPERTY_STATUS" => $publishedStatusId]);

            //Получаем все цены дизайнов
            $designPriceList = $designModel->getPrice($designList);
            $prices = array_map(function($el){return $el["PRICE"];}, $designPriceList);

            $designCount  = count($designList);
            $averagePrice = EHelper::averageValue($prices);
            CIBlockElement::SetPropertyValuesEx($complex->ID, IB_COMPLEX, array(
                "AVERAGE_DESIGN_PRICE" => $averagePrice,
                "DESIGN_CNT" => $designCount
            ));
        }

        return $fields;
    }

    /**
     * Список комплексов для слайдера на главной
     * @return array
     */
    public function getSliderList() {
        $cityId = Sibirix_Model_User::getUserLocation();
        $filter = ['PROPERTY_CITY' => $cityId, 'PROPERTY_SHOW_SLIDER_VALUE' => 'Y'];
        $sliderFields = [
            'ID',
            'CODE',
            'NAME',
			'PREVIEW_PICTURE',
			'DETAIL_PICTURE',
            'PROPERTY_CITY',
            'PROPERTY_SHOW_SLIDER',
            'PROPERTY_SLIDER_IMAGE',
            'PROPERTY_LOCATION',
			'PROPERTY_DESIGN_CNT'
        ];

        $sliderList = $this->getRandElements('complexesSlider4'.$cityId, $sliderFields, $filter, Settings::getOption('slidesComplexCount', 12));

		$this->getImageData($sliderList, 'PROPERTY_SLIDER_IMAGE_VALUE');

        return $sliderList;
    }
}
