<?
/**
 * Class Sibirix_Form_Feedback
 */
class Sibirix_Form_Feedback extends Sibirix_Form_Default {

    public function init() {
        parent::init();

        $userModel   = new Sibirix_Model_User();
        $populateValues = [];
        if($userModel->isAuthorized()) {
            $currentUser = $userModel->getCurrent();
            $populateValues = [
                "NAME"  => $currentUser->NAME,
                "EMAIL" => $currentUser->EMAIL
            ];
        }

        $this->setAttribs(['class' => 'js-feedback-form', 'data-title' => Settings::getOption('feedbackMessage'), 'data-text' => Settings::getOptionText('feedbackMessage')]);
        $this->setAction($this->getView()->url([], 'feedback-send'));
        $this->setMethod('post');
        $this->setDecorators([
            'FormElements',
            ['Form', []],
            [
                ['wrap1' => 'htmlTag'],
                [
                    'tag'   => 'div',
                    'class' => 'form-content'
                ]
            ]
        ]);

        $name = $this->createElement('text','name')
            ->setLabel('Имя')
            ->setAttribs(['class' => 'required', 'maxlength' => 100, 'placeholder' => 'Как вас зовут?'])
            ->setRequired(true)
            ->setValue($populateValues['NAME'])
            ->setValidators([
                ['NotEmpty',true,['messages' => [Zend_Validate_NotEmpty::INVALID => "Представьтесь, пожалуйста"]]],
                ['StringLength', false, ['max' => 100, 'encoding' => 'utf-8',]],
            ])
            ->setFilters(['StringTrim', 'StripTags'])
            ->setDecorators([
                'ViewHelper',
                [
                    'Label',
                    [
                        'placement' => 'prepend'
                    ]
                ],
                [
                    ['errorPlacement' => 'htmlTag'],
                    [
                        'tag'       => 'span',
                        'class'     => 'js-error error-message',
                        'placement' => 'append'
                    ]
                ],
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'input-row'
                    ]
                ]
            ]);

        $contacts = $this->createElement('text','contacts')
            ->setLabel('Как с вами связаться')
            ->setAttribs(['class' => 'required', 'maxlength' => 100, 'placeholder' => 'Телефон или электронная почта'])
            ->setRequired(true)
            ->setValue($populateValues['EMAIL'])
            ->setValidators([
                ['NotEmpty',true,['messages' => [Zend_Validate_NotEmpty::INVALID => "Не может быть пустым"]]],
                ['StringLength', false, ['max' => 5000, 'encoding' => 'utf-8',]],
            ])
            ->setFilters(['StringTrim', 'StripTags'])
            ->setDecorators([
                'ViewHelper',
                [
                    'Label',
                    [
                        'placement' => 'prepend'
                    ]
                ],
                [
                    ['errorPlacement' => 'htmlTag'],
                    [
                        'tag'       => 'span',
                        'class'     => 'js-error error-message',
                        'placement' => 'append'
                    ]
                ],
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'input-row'
                    ]
                ]
            ]);

        $message = $this->createElement('textarea','message')
            ->setLabel('Сообщение')
            ->setAttribs(['class' => 'required materialize-textarea', 'maxlength' => 5000, 'placeholder' => 'Ваш вопрос или предложение'])
            ->setRequired(true)
            ->setValidators([
                ['NotEmpty',true,['messages' => [Zend_Validate_NotEmpty::INVALID => "Не может быть пустым"]]],
            ])
            ->setFilters(['StringTrim', 'StripTags'])
            ->setDecorators([
                'ViewHelper',
                [
                    'Label',
                    [
                        'placement' => 'prepend'
                    ]
                ],
                [
                    ['errorPlacement' => 'htmlTag'],
                    [
                        'tag'       => 'span',
                        'class'     => 'js-error error-message',
                        'placement' => 'append'
                    ]
                ],
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'input-row input-field'
                    ]
                ]
            ]);

        $antiBot = $this->createElement("hidden", "protect")
            ->setAttrib("class", "js-protect")
            ->setDecorators([
                'ViewHelper',
                []
            ]);

        $submit = $this->createElement('note','send')
            ->setValue('Отправить')
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'       => 'a',
                        'class'     => 'btn blue waves-effect',
                    ]
                ]
            ]);

        $this->addElement($antiBot);
        $this->addElement($name);
        $this->addElement($contacts);
        $this->addElement($message);
        $this->addElement($submit);
    }
}
