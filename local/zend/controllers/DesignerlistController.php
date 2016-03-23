<?

/**
 * Class DesignController
 */
class DesignerlistController extends Sibirix_Controller {
    /**
     * @var Sibirix_Model_Designerlist
     */
    protected $_model;

    public function init() {
        /**
         * @var $ajaxContext Sibirix_Controller_Action_Helper_SibirixAjaxContext
         */
        $ajaxContext = $this->_helper->getHelper('SibirixAjaxContext');
        $ajaxContext->addActionContext('designer-list', 'html')
            ->initContext();

        $this->_model = new Sibirix_Model_Design();
    }

    public function designListAction() {
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'designer-list');
        Zend_Registry::get('BX_APPLICATION')->SetTitle("Список дизайнеров");

        $mainSliderModel = new Sibirix_Model_MainSlider();
        $slides = $mainSliderModel->getItems();
        $this->view->slides = $slides;
	
        if ($this->getParam("viewCounter") > 0) {
            Sibirix_Model_ViewCounter::setViewCounter($this->getParam("viewCounter"));
            $this->_model->reinitViewCounter();
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

        $filter = new Sibirix_Form_FilterDesign();

        $filterParams = $this->getAllParams();


        if ($filterParams["priceMin"] === null || $filterParams["priceMax"] === null) {
            $filterParams["price"] = $this->_model->getExtremePrice();
        } else {
            $filterParams["price"] = array(
                $filterParams["priceMin"],
                $filterParams["priceMax"]
            );
        }

        if ($filterParams["budgetMin"] === null || $filterParams["budgetMax"] === null) {
            $filterParams["budget"] = $this->_model->getExtremeBudget();
        } else {
            $filterParams["budget"] = array(
                $filterParams["budgetMin"],
                $filterParams["budgetMax"]
            );
        }

        $filter->populate($filterParams);
        $validFilterValues = $filter->getValues();
        $catalogFilter = $this->_model->prepareFilter($validFilterValues);
        $pageTitle     = $this->_model->getPageTitle($validFilterValues);

        $result = $this->_model->getDesignList($catalogFilter, $catalogSort, $this->getParam("page", 1));

        $this->view->assign([
            "pageTitle" => $pageTitle,
            "filter"    => $filter,
            "itemList"  => $result->items,
            'paginator' => EHelper::getPaginator($result->pageData->totalItemsCount, $result->pageData->current, $result->pageData->size),
        ]);
    }


}