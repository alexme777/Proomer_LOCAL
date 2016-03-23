<?

/**
 * Контроллер страницы дизайнера
 * Class DesignerController
 */
class DesignerController extends Sibirix_Controller {
    /**
     * @var Sibirix_Model_User
     */
    protected $_model;

    public function init() {
        /**
         * @var $ajaxContext Sibirix_Controller_Action_Helper_SibirixAjaxContext
         */
        $ajaxContext = $this->_helper->getHelper('SibirixAjaxContext');
        $ajaxContext->addActionContext('detail', 'html')
            ->initContext();

        $this->_model = new Sibirix_Model_User();
    }

    public function detailAction() {
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'designer');

        $elementId = $this->getParam("elementId");

        $designer = $this->_model->where(["ID" => $elementId])->getElement();
        if (!$designer || $designer->getType() != DESIGNER_TYPE_ID) {
            throw new Zend_Exception('Not found', 404);
        }

        Zend_Registry::get('BX_APPLICATION')->SetTitle($designer->getFullName());

        $this->_model->getImageData($designer, 'PERSONAL_PHOTO');

        //Получаем стили
        if (!empty($designer->UF_STYLES)) {
            $styleList = Sibirix_Model_Reference::getReference(HL_STYLE, array("UF_NAME"), "ID");

            $propertyStylesValue = array();
            foreach ($designer->UF_STYLES as $key => $stylesValue) {
                $propertyStylesValue[$key] = $styleList[$stylesValue];
            }
            $designer->UF_STYLES = $propertyStylesValue;
        }

        $sort = ["SORT" => "ASC"];


        $designModel = new Sibirix_Model_Design();
        $designModel->reinitPageSize(Settings::getOption('designerPageSize', 18));

        $designs = $designModel->getDesignList(["CREATED_BY" => $designer->ID], $sort, $this->getParam("page", 1));

        $this->view->assign([
            'designer' => $designer,
            'designs' => $designs,
            'paginator' => EHelper::getPaginator($designs->pageData->totalItemsCount, $designs->pageData->current, $designs->pageData->size),
        ]);
    }
}