<?

/**
 * Контроллер страницы жилых компексов
 * Class ComplexController
 */
class ComplexController extends Sibirix_Controller {

    /**
     * @var Sibirix_Model_Complex
     */
    protected $_model;

    public function init() {
        /**
         * @var $ajaxContext Sibirix_Controller_Action_Helper_SibirixAjaxContext
         */
        $ajaxContext = $this->_helper->getHelper('SibirixAjaxContext');
        $ajaxContext->addActionContext('complex-list', 'html')
            ->initContext();

        $this->_model = new Sibirix_Model_Complex();
    }

    public function complexListAction() {
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'complex-list');
        Zend_Registry::get('BX_APPLICATION')->SetTitle("Список жилых комплексов");

        if ($this->getParam("viewCounter") > 0) {
            Sibirix_Model_ViewCounter::setViewCounter($this->getParam("viewCounter"));
            $this->_model->reinitViewCounter();
        }

        $sortData = $this->getParam("sort");

        if (!empty($sortData)) {
            if (!empty($sortData["popular"])) {
                $catalogSort["PROPERTY_DESIGN_CNT"] = strtoupper($sortData["popular"]);
            }
            if (!empty($sortData["price"])) {
                $catalogSort["PROPERTY_AVERAGE_DESIGN_PRICE"] = strtoupper($sortData["price"]);
            }
        }
        $catalogSort["SORT"] = "ASC";

        $filter = new Sibirix_Form_FilterComplex();

        $filterParams = $this->getAllParams();

        if ($filterParams["avgPriceMin"] === null || $filterParams["avgPriceMax"] === null) {
            $filterParams["avgPrice"] = $this->_model->getExtremeAvgPrice();
        } else {
            $filterParams["avgPrice"] = array(
                $filterParams["avgPriceMin"],
                $filterParams["avgPriceMax"]
            );
        }

        $filter->populate($filterParams);

        $catalogFilter = $this->_model->prepareFilter($filter->getValues());

        $result = $this->_model->getComplexList($catalogFilter, $catalogSort, $this->getParam("page", 1));

        $this->view->assign([
            "filter"    => $filter,
            "itemList"  => $result->items,
            'paginator' => EHelper::getPaginator($result->pageData->totalItemsCount, $result->pageData->current, $result->pageData->size),
        ]);
    }

    public function detailAction() {

        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'complex-detail');

        \Bitrix\Main\Page\Asset::getInstance()->addJs(GOOGLE_MAPS);

        $elementCode = $this->getParam("elementCode");
        $complex = $this->_model->where(["CODE" => $elementCode])->getElement();
        if (!$complex) {
            throw new Zend_Exception('Not found', 404);
        }
        $this->_model->getImageData($complex, "DETAIL_PICTURE");
        $this->_model->getSeoElementParams($complex);

        $houseModel = new Sibirix_Model_House();
        $houseList = $houseModel
            ->where(["PROPERTY_COMPLEX" => $complex->ID])
            ->getElements();

        $this->_model->getImageData($houseList, "PROPERTY_PLAN_VALUE");
        $complex->HOUSES = $houseList;

        $flats = $houseModel->getFlatList($houseList[0]->ID);

        $complex->FIRST_HOUSE_FLATS = $flats;

        $designModel = new Sibirix_Model_Design();
        $elementCnt = Settings::getOption('designSliderCount', 20);

        $complex->DESIGNS = $designModel->getSliderItems($complex->ID, $elementCnt);

        if (empty($complex->PROPERTY_SIMILAR_COMPLEX_VALUE)) {
            $complex->SIMILAR_COMPLEX = [];
        } else {
            $complex->SIMILAR_COMPLEX = $this->_model->where(['ID' => $complex->PROPERTY_SIMILAR_COMPLEX_VALUE])->getElements();
        }

        $this->_setSeoElementParams($complex);
        $this->view->complex = $complex;
    }

    public function flatCountAction() {
        $houseId = $this->getParam('houseId');
        $houseModel = new Sibirix_Model_House();
        $flats = $houseModel->getFlatList($houseId);

        $this->view->flats = $flats;
        $this->_response->stopBitrix(true);
    }
}