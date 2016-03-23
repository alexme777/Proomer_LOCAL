<?

/**
 * Class Sibirix_Form_Remind
 */
class Sibirix_Form_Remind extends Sibirix_Form_Default {

    /**
     *
     */
    public function init() {
        parent::init();

        $this->setMethod('post');
        $this->setAttribs(['class' => 'js-remind-form', 'data-title' => Settings::getOption('sendPassMessage'), 'data-text' => Settings::getOptionText('sendPassMessage')]);
        $this->setAction($this->getView()->url([], 'user-password-remind'));
        $this->setDecorators([
            'FormElements',
            ['Form', []],
        ]);

        $title = $this->createElement('note', 'title')
            ->setValue('Восстановление пароля')
            ->setDecorators([
                'ViewHelper',
                [
                    ['formTitle' => 'htmlTag'],
                    [
                        'tag'   => 'h2'
                    ]
                ]
            ]);

        $email = $this->createElement('text','email')
            ->setLabel('E-mail')
            ->setAttribs(['class' => 'required is-email', 'autocomplete' => 'off', 'placeholder' => 'Укажите ваш электронный адрес'])
            ->setRequired(true)
            ->setValidators([
                ['NotEmpty',true,['messages' => [Zend_Validate_NotEmpty::INVALID => "Поле обязательно для заполнения!"]]],
            ])
            ->setFilters(['StringTrim', 'StripTags'])
            ->setDecorators([
                'ViewHelper',
                'Label',
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

        $message = $this->createElement('note','message')
            ->setValue(Settings::getOptionText('sendPassFormMessage'))
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'message'
                    ]
                ]
            ]);

        $submit = $this->createElement('note','submit')
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

        $authLink = $this->createElement('note','registrationLink')
            ->setValue('Я вспомнил')
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'a',
                        'class' => 'dotted-link js-fancybox',
                        'href'  => '#login-popup'
                    ]
                ]
            ]);

        $this->addElement($title);
        $this->addDisplayGroup([
            $email,
            $message,
            $submit,
            $authLink
        ], 'mainFields', ['decorators' => [
            'FormElements',
            ['htmlTag',['tag' => 'div', 'class' => 'form-content']]
        ]
        ]);
    }
}

