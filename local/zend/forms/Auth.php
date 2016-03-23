<?

/**
 * Class Sibirix_Form_Auth
 */
class Sibirix_Form_Auth extends Sibirix_Form_Default {

    /**
     *
     */
    public function init() {
        parent::init();

        $this->setMethod('post');
        $this->setAttrib('class', 'js-auth-form');
        $this->setAction($this->getView()->url([], 'user-auth'));
        $this->setDecorators([
            'FormElements',
            ['Form', []],
        ]);

        $title = $this->createElement('note', 'title')
            ->setValue('Вход')
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

        $password = $this->createElement('password','password')
            ->setLabel('Пароль')
            ->setAttribs(['class' => 'required password', 'autocomplete' => 'off', 'placeholder' => 'Введите пароль'])
            ->setRequired(true)
            ->setValidators([
                ['NotEmpty',true,['messages' => [Zend_Validate_NotEmpty::INVALID => "Поле обязательно для заполнения!"]]],
            ])
            ->setDecorators([
                'ViewHelper',
                'Label',
                [
                    ['wrap2' => 'htmlTag'],
                    [
                        'tag'   => 'a',
                        'class' => 'show-password js-show-password',
                        'placement' => 'append',
                        'href' => 'javascript:void(0);',
                        'title' => 'Показать пароль'
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
                ],
            ]);

        $submit = $this->createElement('note','submit')
            ->setValue('Войти')
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

        $sendPassLink = $this->createElement('note','sendPassLink')
            ->setValue('Забыли пароль?')
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'a',
                        'class' => 'dotted-link js-fancybox',
                        'href'  => '#send-pass-popup'
                    ]
                ],
                [
                    ['separator' => 'htmlTag'],
                    [
                        'tag'       => 'span',
                        'class'     => 'separator',
                        'placement' => 'append'
                    ]
                ]
            ]);

        $registrationLink = $this->createElement('note','registrationLink')
            ->setValue('Зарегистрироваться')
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'a',
                        'class' => 'dotted-link js-fancybox',
                        'href'  => '#registration-popup'
                    ]
                ]
            ]);

        $this->addElement($title);
        $this->addDisplayGroup([
            $email,
            $password,
            $submit,
            $sendPassLink,
            $registrationLink
        ], 'mainFields', ['decorators' => [
            'FormElements',
            ['htmlTag',['tag' => 'div', 'class' => 'form-content']]
        ]
        ]);
    }
}
