<?

/**
 * Class Sibirix_Form_Registration
 */
class Sibirix_Form_Registration extends Sibirix_Form_Default {

    /**
     *
     */
    public function init() {
        parent::init();

        $this->setMethod('post');
        $this->setAttribs(['class' => 'js-registration-form', 'data-title' => Settings::getOption('registerMessage'), 'data-text' => Settings::getOptionText('registerMessage')]);
        $this->setAction($this->getView()->url([], 'user-registration'));
        $this->setDecorators([
            'FormElements',
            ['Form', []],
        ]);

        $title = $this->createElement('note', 'title')
            ->setValue('Регистрация')
            ->setDecorators([
                'ViewHelper',
                [
                    ['formTitle' => 'htmlTag'],
                    [
                        'tag'   => 'h2'
                    ]
                ]
            ]);

        $type = $this->createElement('radio', 'type')
            ->setRequired(true)
            ->setMultiOptions([CLIENT_TYPE_ID =>"Клиент", DESIGNER_TYPE_ID => "Дизайнер", PROVIDER_TYPE_ID => "Поставщик", SELLER_TYPE_ID => "Продавец"])
            ->setAttribs(['class' => 'required'])
            ->setLabel('Ваш статус:')
            ->setValidators([
                ['NotEmpty',true,['messages' => [Zend_Validate_NotEmpty::INVALID => "Выберите тип профиля"]]],
            ])
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
                        'class' => 'popup-title form input-row'
                    ]
                ]
            ]);

        $type->helper = 'customRadio';

        $name = $this->createElement('text','name')
            ->setLabel('Имя')
            ->setAttribs(['class' => 'required', 'autocomplete' => 'off', 'placeholder' => 'Как вас зовут?'])
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
            ->setAttribs(['class' => 'required password', 'autocomplete' => 'off', 'placeholder' => 'Придумайте и не забудьте пароль' ,'minlength' => 6])
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
            ->setValue('Зарегистрироваться')
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

        $authLink = $this->createElement('note','authLink')
            ->setValue('Уже зарегистрированы?')
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
        $this->addElement($type);
        $this->addDisplayGroup([
            $name,
            $email,
            $password,
            $submit,
            $authLink
        ], 'mainFields', ['decorators' => [
            'FormElements',
            ['htmlTag',['tag' => 'div', 'class' => 'form-content']]
        ]
        ]);
    }
}
