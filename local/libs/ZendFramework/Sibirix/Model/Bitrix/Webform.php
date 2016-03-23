<?
/**
 * @property int ID
 * Class Sibirix_Model_Bitrix_Webform
 */
class Sibirix_Model_Bitrix_Webform extends Sibirix_Model_Bitrix {

    /**
     * list field description of webform
     *
     * @var array $_cform_data
     */
    private $_cform_data = null;

    /**
     * list fields of webform
     *
     * @var array
     */
    private $_cform_elements = null;

    /**
     * Last error messages
     * @var string
     */
    private $_lastErrors = array();

    /**
     * @param int $id
     * @param array $config
     * @throws Zend_Exception
     */
    public function __construct($id, $config = array()) {
        if (!CModule::IncludeModule("form")) {
            throw new Zend_Exception('fail load bitrix module WebForm');
        }

        parent::__construct($config);

        if (!$this->getById($id)) {
            throw new Zend_Exception('webform not found');
        }
    }

    /**
     * @return mixed|void
     */
    public function init() {}

    /**
     * load webform by ID
     *
     * @param int $id
     * @return bool
     */
    public function getById($id) {
        $cacheId = __METHOD__ . $id;
        $this->_cform_data = $this->_getCache($cacheId);
        if (is_array($this->_cform_data)) {
            return true;
        }

        $cForm = new CForm();
        $dbRes = $cForm->GetByID($id);
        if ($arRes = $dbRes->Fetch()) {
            $this->_cform_data     = $arRes;
            $this->_setCache($cacheId, $this->_cform_data);

            $this->_cform_elements = array();
            return true;
        }

        return false;
    }

    /**
     * return field webform
     *
     * @param $key
     * @return mixed
     * @throws Zend_Exception
     */
    public function __get($key) {
        if (!isset($this->_cform_data[$key])) {
            throw new Zend_Exception('webform field [' . $key . '] not found');
        }

        return $this->_cform_data[$key];
    }

    /**
     * gets list questions on webform
     * load questiuons if questions list not loaded
     *
     * @return array
     */
    public function getElements() {
        if (!$this->_cform_elements) {
            $cacheId = __METHOD__ . $this->ID;

            $this->_cform_elements = $this->_getCache($cacheId);
            if (!is_array($this->_cform_elements)) {
                $cFormField = new CFormField();
                $rsElements = $cFormField->GetList($this->ID, 'ALL', $by, $order, array(), $is_filtered);
                while ($arElement = $rsElements->Fetch()) {
                    $this->_cform_elements[$arElement['SID']] = $arElement;
                }
                $this->_loadAnswers();

                $this->_setCache($cacheId, $this->_cform_elements);
            }
        }

        return $this->_cform_elements;
    }

    /**
     * load answers for questions and add list to question data
     *
     * @return $this
     */
    private function _loadAnswers() {
        foreach ($this->_cform_elements as $ind => $element) {
            $cFormAnswer = new CFormAnswer();
            $rsAnswers = $cFormAnswer->GetList(
                $element['ID'],
                $by,
                $order,
                array('ACTIVE' => 'Y'),
                $is_filtered
            );

            $answers = array();
            while ($arAnswer = $rsAnswers->Fetch()) {
                $answers[] = $arAnswer;
            }

            $this->_cform_elements[$ind]['ANSWERS'] = $answers;
        }
        return $this;
    }

    /**
     * @param $key
     * @return mixed
     * @throws Zend_Exception
     */
    public function getElement($key) {
        if (!isset($this->_cform_elements[$key])) {
            throw new Zend_Exception('webform element [' . $key . '] not found');
        }

        return $this->_cform_elements[$key];
    }

    /**
     * validate request params on webform
     * returning array validate errors
     *
     * @param $data
     * @return array
     */
    public function check($data) {
        $cForm = new CForm();
        return $cForm->Check($this->ID, $data, false, 'Y', 'Y');
    }

    /**
     * save new result webform
     *
     * @param $data
     * @return int|bool
     */
    public function addResult($data) {
        global $strError;
        $strError = '';
        $this->clearErrors();

        $cFormResult = new CFormResult();
        $result = $cFormResult->Add($this->ID, $data);

        if ($result !== false) {
            CFormCRM::onResultAdded($this->ID, $result);
            $cFormResult->SetEvent($result);
            $cFormResult->Mail($result);
        }

        if ($result === false && empty($strError)) {
            $strError = 'Ошибка сохранения формы';
        }

        $this->setLastErrors($strError);

        return $result;
    }

    protected function clearErrors() {
        $this->_lastErrors = array();
    }

    protected function setLastErrors($message) {
        $messages = preg_split('/<br\s*\/?>/', $message, null, PREG_SPLIT_NO_EMPTY);
        $this->_lastErrors = $messages;
    }

    public function getLastErrors(){
        return $this->_lastErrors;
    }
}