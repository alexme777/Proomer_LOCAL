<?

/**
 * Class Sibirix_Form_Default
 */
class Sibirix_Form_Default extends Sibirix_Form {

    public $formErrors = array();

    public function issetError() {
        return (bool) count($this->formErrors);
    }

    public function getFieldsErrors() {
        $elements = $this->getElements();
        $errors   = array();

        foreach ($elements as $name => $element) {
            $eMessages = $element->getMessages();

            if (empty($eMessages)) {
                continue;
            }

            $errors[] = [
                'fieldId' => $name,
                'message' => implode("<br />", $eMessages)
            ];
        }

        $this->formErrors = $errors;
    }

    public function setFieldErrors($fieldName, $errorMessage) {
        $this->formErrors[] = [
            'fieldId' => $fieldName,
            'message' => $errorMessage
        ];
    }

    public function antiBotCheck() {
        $antiBotVal = $this->getValue("protect");
        return ($antiBotVal == "proomer-i-am-not-bot");
    }

    /**
     * Returns element messages
     *
     * @return array
     */
    public function getElementMessages() {
        $messages = [];
        $elements = $this->getElements();

        /**
         * @var $element Zend_Form_Element
         */
        foreach ($elements as $name => $element) {
            $eMessages = $element->getMessages();

            if (!empty($eMessages)) {
                $messages[$name] = $eMessages;
            }
        }

        return $messages;
    }

    /**
     * Обработка ошибок валидации зенда
     * @param $arZendErrors
     * @return array
     */
    public function formatZendValidator($arZendErrors) {
        $arErrorFields = array();
        foreach($arZendErrors as $fieldId => $errorArray) {
            $errorString = implode("<br />", $errorArray);
            if(empty($errorString)) {
                continue;
            }
            $arErrorFields[] = [
                "fieldId" => $fieldId,
                "message" => $errorString
            ];
        }

        return $arErrorFields;
    }
}
