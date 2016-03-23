<?

/**
 * Class DesignController
 */
class ArticleController extends Sibirix_Controller {
	
    /**
     * @var Sibirix_Model_Design
     */
    protected $_model;

    public function init() {
		session_start();
        /**
         * @var $ajaxContext Sibirix_Controller_Action_Helper_SibirixAjaxContext
         */
        $ajaxContext = $this->_helper->getHelper('SibirixAjaxContext');
        $ajaxContext->addActionContext('article-detail', 'html')
            ->initContext();
		CModule::IncludeModule("catalog");
		CModule::IncludeModule("iblock");
		CModule::IncludeModule("blog");
        $this->_model = new Sibirix_Model_Article();
		$this->user = Sibirix_Model_User::getCurrent();
    }

    public function indexAction() {
  
    }

    public function articleAction() {
	//	ini_set('error_reporting', E_ALL);
	//	ini_set('display_errors', 1);
		//ini_set('display_startup_errors', 1);
		$id = $this->getParam("id");
		$article = $this->_model->getArticle($id);
		//$result = array();
		#SHOW_GALLERY||group|http://pics.rbcdaily.ru/rbcdaily_pics/v4/80/42/8a957dda23216e25823ab86a98e6661f.jpg,http://pics.rbcdaily.ru/rbcdaily_pics/v4/80/42/8a957dda23216e25823ab86a98e6661f.jpg, http://pics.rbcdaily.ru/rbcdaily_pics/v4/80/42/8a957dda23216e25823ab86a98e6661f.jpg#
		//echo preg_match_all("#SHOW_GALLERY#" , $article->DETAIL_TEXT, $result);
		/*echo print_r($article);
		exit;*/
		//здесь есть проблемы
		$article_bx = $this->_model->test();

		if(CModule::IncludeModule("iblock"))
			CIBlockElement::CounterInc($id);
		/*=============================================================================
		/		Получает цепочку категорий от текущей до корневой					/
		*============================================================================*/
	   	$filter['where'] = ["=ID" => $article->IBLOCK_SECTION_ID];
		function getCategoryUp($where){
			static $chain_category = array();
			static $i = 0;
			$model = new Sibirix_Model_Article();
			$categories = $model->getCategory($where);
			array_push($chain_category, $categories[$i]);
			if(!empty($categories[$i]->IBLOCK_SECTION_ID)){
				$filter['where'] = ["=ID" => $categories[$i]->IBLOCK_SECTION_ID];
				getCategoryUp($filter);
				$i+=1;
			}
			return array_reverse($chain_category);
		};

		$ch_categories = getCategoryUp($filter);
		$date = new DateTime($article->DATE_CREATE);
		$article->DATE_CREATE = $date->format('d-m-Y');
		foreach($article_bx as $a){
			if($a['TITLE'] == $article->NAME){
				$article->NUM_COMMENTS = $a["NUM_COMMENTS"];
				break;
			}
		}
		array_push($recom_article, $articles[$v]);
		//массив рекомендованных статей
		$recom_article = array();
		$articles = $this->_model->getArticles();
		$rand_keys = array_rand($articles, 2);
		foreach($rand_keys as $k=>$v){
			$articles[$v]->DATE_CREATE = $date->format('d-m-Y');
			foreach($article_bx as $a){
				if($a['TITLE'] == $articles[$v]->NAME){
					$articles[$v]->NUM_COMMENTS = $a["NUM_COMMENTS"];
					break;
				}
			}
			array_push($recom_article, $articles[$v]);
		}
		
		//Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'shop-detail');
        Zend_Registry::get('BX_APPLICATION')->SetTitle($article->NAME);
		$recom_article = $this->_model -> getImgItems($recom_article);
		$this->view->article = $article;
		$this->view->ch_categories = $ch_categories;
		$this->view->id = $id;
		$this->view->recom_article = $recom_article;
		
    }

}