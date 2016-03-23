<?
/**
 * Class Sibirix_Form
 */
class Sibirix_Form extends Zend_Form {

    /**
     * @var Zend_Session_Namespace
     */
    private $_storage;

    public function __construct($options = null) {
        $this->addElementPrefixPath('Sibirix_Validate', 'Sibirix/Validate/', 'validate');
        $this->addElementPrefixPath('App_Validate', APPLICATION_PATH . '/validators', 'validate');

        parent::__construct($options);
    }
    
    /**
     * @param string $fieldName
     * @param string $validatorName
     * @param array|string $messages
     * @return Zend_Validate
     */
    public function setValidatorMessages($fieldName, $validatorName, $messages){
        $validator = $this->getElement($fieldName)->getValidator($validatorName);
        $translator = $this->getTranslator();

        $templates = $validator->getMessageTemplates();
        if (is_array($templates)) {

            foreach ($templates as $messageId=>$messageText) {
                if (is_array($messages)) {
                    if (isset($messages[$messageId])) {
                        $templates[$messageId] = $translator->translate($messages[$messageId]);
                    } else {
                        $templates[$messageId] = $translator->translate($templates[$messageId]);
                    }
                } else {
                    $templates[$messageId] = $translator->translate((string) $messages);
                }
            }

            $validator->setTranslator(
                new Zend_Translate(
                    'Array',
                    $templates
                )
            );
        }

        return $validator;
    }

    /**
     * @return null|Zend_Translate|Zend_Translate_Adapter
     */
    public function getTranslator() {
        if ($this->_translator) {
            return $this->_translator;
        }

        $this->_translator = Zend_Validate_Abstract::getDefaultTranslator();
        return $this->_translator;
    }

    /**
     * @return array
     */
    public function toJson() {
        return $this->getMessages();
    }

    /**
     * @return $this
     */
    public function storeContext(){
        return $this->_storeContext();
    }

    /**
     * private store context
     * @return $this
     */
    protected function _storeContext() {
        if (!$this->hasErrors()) {
            return $this->_clearContext();
        }

        $this->_getStorage()->data = $this->getValues();
        $this->_getStorage()->errors = $this->getMessages();

        return $this;
    }

    /**
     * @return $this
     */
    public function restoreContext() {
        return $this->_restoreContext();
    }

    /**
     * @return $this
     */
    protected function _restoreContext() {
        //debug_print_backtrace();
        if (isset($this->_getStorage()->data)) {
            foreach ($this->_getStorage()->data as $field => $value) {
                if ($this->getElement($field)) {
                    $this->getElement($field)->setValue($value);
                }
            }
        }

        if (!empty($this->_getStorage()->errors)) {
            foreach ($this->_getStorage()->errors as $field => $messageList) {
                if ($this->getElement($field)) {
                    foreach ($messageList as $message) {
                        $this->getElement($field)->addError($message);
                    }
                }
            }

            $this->markAsError();
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function clearContext() {
        return $this->_clearContext();
    }

    /**
     * @return $this
     */
    protected function _clearContext() {
        unset($this->_getStorage()->data);
        unset($this->_getStorage()->errors);

        return $this;
    }

    /**
     * @return Zend_Session_Namespace
     */
    protected function _getStorage() {
        if (!$this->_storage) {
            $this->_storage = new Zend_Session_Namespace($this->_getStorageKey());
        }

        return $this->_storage;
    }

    /**
     * @return string
     */
    private function _getStorageKey() {
        return 'FORM_STORAGE_'. strtoupper(get_class($this));
    }
}