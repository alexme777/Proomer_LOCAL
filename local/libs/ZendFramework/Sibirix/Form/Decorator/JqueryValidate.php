<?
/**
 * Class Sibirix_Form_Decorator_JqueryValidate
 */
class Sibirix_Form_Decorator_JqueryValidate extends Zend_Form_Decorator_Abstract {
    /**
     * @param  string $content
     * @return string
     */
    public function render($content) {
        $element = $this->getElement();
        $validatorNameList = array_keys($element->getValidators());
        foreach ($validatorNameList as $validatorFullName) {
            $validatorName = array_pop(explode('_', $validatorFullName));
            $validator = $element->getValidator($validatorName);

            foreach ($validator->getMessageTemplates() as $key => $message) {
                $tmplKey = lcfirst(ucfirst($validatorName) . str_replace($validatorName, '', ucfirst($key)));

                $attribs = array();
                foreach ($validator->getMessageVariables() as $varName) {
                    $message = str_replace("%" . $varName . "%", $validator->{$varName}, $message);
                    $attribs['data-rule-' . $tmplKey . '-' . $varName] = $validator->{$varName};
                }

                $message = str_replace("%value%", "{value}", $message);

                $attribs['data-rule-' . $tmplKey] = 'true';
                $attribs['data-msg-' . $tmplKey] = $message;

                $element->setAttribs($attribs);
            }
        }

        return $content;
    }
}
