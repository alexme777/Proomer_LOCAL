<?
/**
 * Class Sibirix_Controller_Action_Helper_SibirixAjaxContext
 */
class Sibirix_Controller_Action_Helper_SibirixAjaxContext extends Zend_Controller_Action_Helper_AjaxContext {

    public function __construct() {
        parent::__construct();

        /**
         * перекрыть html контекст для поддержки stopBitrix
         */
        $this->setContext('html', array(
            'suffix' => 'ajax',
            'callbacks' => array(
                'post' => 'postHtmlContext'
            )
        ));

        /**
         * дефолтный контекст "html"
         */
        $this->getRequest()->setParam(
            $this->getContextParam(),
            $this->getRequest()->getParam($this->getContextParam(), 'html')
        );
    }

    /**
     * расширение json контекста поддержкой stopBitrix
     */
    public function postJsonContext() {
        parent::postJsonContext();

        if (!$this->getAutoJsonSerialization()) {
            return;
        }

        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $view = $viewRenderer->view;
        if ($view instanceof Zend_View_Interface) {
            /**
             * @see Zend_Json
             */
            if(method_exists($view, 'getVars')) {
                require_once 'Zend/Json.php';
                //Zend_Json::$useBuiltinEncoderDecoder = true;
                $vars = Zend_Json::encode($view->getVars());
                $this->getResponse()->setBody($vars);
            } else {
                require_once 'Zend/Controller/Action/Exception.php';
                throw new Zend_Controller_Action_Exception('View does not implement the getVars() method needed to encode the view into JSON');
            }
        }

        $this->_stopBitrix();
    }

    /**
     * recursion Zend_Json::encode
     * @param $vars
     *
     * @return string
     */
    public function _jsonEncode($vars) {
        foreach ($vars as $_key => $_var) {
            if (is_array($_var)) {
                $vars[$_key] = $this->_jsonEncode($_var);
            }
        }

        var_export($_var);
        return Zend_Json::encode($vars);
    }

    /**
     * коллбек для html контекста с поддержкой stopBitrix
     */
    public function postHtmlContext() {
        $this->_stopBitrix();
    }

    private function _stopBitrix() {
        $response = $this->getResponse();
        if (method_exists($response, 'stopBitrix')) {
            $response->stopBitrix(true);
        }
    }
}
