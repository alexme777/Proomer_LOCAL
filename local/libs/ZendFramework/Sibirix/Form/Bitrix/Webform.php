<?php
/**
 * Class Sibirix_Form_Bitrix_Webform
 */
class Sibirix_Form_Bitrix_Webform extends Sibirix_Form {
    /**
     * @var Sibirix_Model_Bitrix_Webform $_model
     */
    private $_model;

    /**
     * @var int $_web_form_id
     */
    protected $_web_form_id;

    /**
     * @var array
     */
    protected $_web_form_options = array(
        'autoLoad' => true,
        'addSubmit' => true
    );

    /**
     * @param array|Zend_Config|null $options
     * @throws Zend_Exception
     */
    public function __construct($options = array()) {
        parent::__construct($options);
    }

    /**
     * Set form state from options array
     *
     * @param  array $options
     * @throws Zend_Exception
     * @return Zend_Form
     */
    public function setOptions(array $options){
        parent::setOptions($options);

        if ($this->_web_form_options['autoLoad']) {
            // Prevent double load
            $this->_web_form_options['autoLoad'] = false;

            if (!$this->loadWebForm()) {
                throw new Zend_Exception('not load webform');
            }
        }

        return $this;
    }

    /**
     * set webform id
     * @param $id
     */
    public function setWebformId($id) {
        $this->_web_form_id = $id;
    }

    /**
     * load webform model with element
     *
     * @return bool
     */
    public function loadWebForm() {
        if (!$this->_model) {
            if ($this->_web_form_id) {
                $this->_model = new Sibirix_Model_Bitrix_Webform($this->_web_form_id);
                foreach ($this->_model->getElements() as $question) {
                    $this->buildElement($question);
                }

                if ($this->_web_form_options['addSubmit']) {
                    $submit = new Zend_Form_Element_Submit('web_form_submit', array(
                        'label' => $this->_model->BUTTON,
                    ));
                    $this->addElement($submit);
                }

                $this->setDescription($this->_model->NAME);
            }
        }

        return !!$this->_model;
    }

    /**
     * @param $options
     * @return $this
     * @throws Zend_Exception
     */
    public function buildElement($options) {
        $method = 'build' . ucfirst( strtolower($options['ANSWERS'][0]['FIELD_TYPE']) ) . 'Element';
        if (method_exists($this, $method)) {
            $this->$method($options);
        } else {
            throw new Zend_Exception("add new method '$method' to class '".__CLASS__."' for convert current field type to Zend element");
        }

        return $this;
    }

    /**
     * @param $options
     * @return $this
     */
    public function buildHiddenElement($options) {
        $elementClass = 'Zend_Form_Element_Hidden';
        $elementOptions = array(
            'label' => $options['TITLE'],
            'required' => $options['REQUIRED'] === 'Y'
        );
        $name = 'form_hidden_'.$options['ANSWERS'][0]['ID'];
        $this->addElement(new $elementClass($name, $elementOptions));

        return $this;
    }

    /**
     * @param $options
     * @return $this
     */
    public function buildFileElement($options) {
        $elementClass = 'Zend_Form_Element_File';
        $elementOptions = array(
            'label' => $options['TITLE'],
            'required' => $options['REQUIRED'] === 'Y'
        );
        $name = 'form_file_' . $options['ANSWERS'][0]['ID'];
        $this->addElement(new $elementClass($name, $elementOptions));

        return $this;
    }
    
    /**
     * @param $options
     * @return $this
     */
    public function buildTextElement($options) {
        $elementClass = 'Zend_Form_Element_Text';

        $elementOptions = array(
            'label' => $options['TITLE'],
            'required' => $options['REQUIRED'] === 'Y'
        );

        $name = 'form_text_'.$options['ANSWERS'][0]['ID'];
        $this->addElement(new $elementClass($name, $elementOptions));

        return $this;
    }

    /**
     * @param $options
     * @return $this
     */
    public function buildEmailElement($options) {
        $elementClass = 'Zend_Form_Element_Text';

        $elementOptions = array(
            'label' => $options['TITLE'],
            'required' => $options['REQUIRED'] === 'Y'
        );

        $name = 'form_email_'.$options['ANSWERS'][0]['ID'];
        $this->addElement(new $elementClass($name, $elementOptions));

        return $this;
    }

    /**
     * @param $options
     * @return $this
     */
    public function buildTextareaElement($options) {
        $elementClass = 'Zend_Form_Element_Textarea';

        $elementOptions = array(
            'label' => $options['TITLE'],
            'required' => $options['REQUIRED'] === 'Y'
        );

        $name = 'form_textarea_'.$options['ANSWERS'][0]['ID'];
        $this->addElement(new $elementClass($name, $elementOptions));

        return $this;
    }

    /**
     * @param $options
     * @return $this
     */
    public function buildCheckboxElement($options) {
        $elementClass = 'Zend_Form_Element_MultiCheckbox';

        $elementOptions = array(
            'label' => $options['TITLE'],
            'required' => $options['REQUIRED'] === 'Y'
        );

        $elementOptions['multiOptions'] = array();
        foreach ($options['ANSWERS'] as $answer) {
            $elementOptions['multiOptions'][$answer['ID']] = $answer['MESSAGE'];
        }

        $name = 'form_checkbox_'.$options['SID'];
        $this->addElement(new $elementClass($name, $elementOptions));

        return $this;
    }

    /**
     * @param $options
     * @return $this
     */
    public function buildDropdownElement($options) {
        $elementClass = 'Zend_Form_Element_Select';

        $elementOptions = array(
            'label' => $options['TITLE'],
            'required' => $options['REQUIRED'] === 'Y'
        );

        $elementOptions['multiOptions'] = array();
        foreach ($options['ANSWERS'] as $answer) {
            $elementOptions['multiOptions'][$answer['ID']] = $answer['MESSAGE'];
        }

        $name = 'form_dropdown_'.$options['SID'];
        $this->addElement(new $elementClass($name, $elementOptions));

        return $this;
    }

    /**
     * @param $options
     * @return $this
     */
    public function buildMultiselectElement($options) {
        $elementClass = 'Zend_Form_Element_Multiselect';

        $elementOptions = array(
            'label' => $options['TITLE'],
            'required' => $options['REQUIRED'] === 'Y'
        );

        $elementOptions['multiOptions'] = array();
        foreach ($options['ANSWERS'] as $answer) {
            $elementOptions['multiOptions'][$answer['ID']] = $answer['MESSAGE'];
        }

        $name = 'form_multiselect_'.$options['SID'];
        $this->addElement(new $elementClass($name, $elementOptions));

        return $this;
    }

    /**
     * @param $sid
     * @return bool|string
     */
    public function getFieldNameBySid($sid) {
        foreach ($this->_model->getElements() as $element) {
            if ($element['SID'] == $sid) {
                $answer = $element['ANSWERS'][0];
                switch ($answer['FIELD_TYPE']) {
                    case 'multiselect':
                    case 'dropdown':
                    case 'checkbox': {
                        return "form_" . $answer['FIELD_TYPE'] . "_" . $element['SID'];
                    }
                    default: {
                        return "form_" . $answer['FIELD_TYPE'] . "_" . $answer['ID'];
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function isValid($data) {
        parent::isValid($data);

        if ($this->isArray()) {
            $eBelongTo = $this->getElementsBelongTo();
            $data = $this->_dissolveArrayValue($data, $eBelongTo);
        }

        $result = $this->_model->check($data);
        foreach ($result as $sid => $message) {
            $this->getElement( $this->getFieldNameBySid($sid) )->setErrors(array($message));
        }

        if (!empty($result)) {
            $this->markAsError();
        }

        return !$this->hasErrors();
    }

    /**
     * @return int
     */
    public function getWebFormId() {
        return $this->_web_form_id;
    }

    /**
     * @return Sibirix_Model_Bitrix_Webform
     */
    public function getModel() {
        return $this->_model;
    }
}