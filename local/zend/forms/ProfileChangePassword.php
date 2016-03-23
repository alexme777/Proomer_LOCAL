<?

/**
 * Class Sibirix_Form_ProfileChangePassword
 */
class Sibirix_Form_ProfileChangePassword extends Sibirix_Form_Default {

    /**
     *
     */
    public function init() {
        parent::init();

        $userModel   = new Sibirix_Model_User();
        $populateValues = [];
        if($userModel->isAuthorized()) {
            $currentUser = $userModel->getCurrent();
            $populateValues = [
                "ID" => $currentUser->ID,
            ];
        }

        $this->setMethod('post');
        $this->setAttribs(['class' => 'js-profile-form', 'data-title' => Settings::getOption('changePassProfileMessage'), 'data-text' => Settings::getOptionText('changePassProfileMessage')]);
        $this->setAction($this->getView()->url([], 'profile-change-password'));
        $this->setDecorators([
            'FormElements',
            [
                ['wrap1' => 'htmlTag'],
                [
                    'tag' => 'div',
                    'class' => 'form-block js-form-block',
                ]
            ],
            'Form'
        ]);

        $userId = $this->createElement('hidden', 'userId')
            ->setFilters(['StringTrim', 'StripTags'])
            ->setValue($populateValues['ID'])
            ->setDecorators([
                'ViewHelper',
                [],
            ]);

        $title = $this->createElement('note', 'title')
            ->setValue('Изменение пароля')
            ->setDecorators([
                'ViewHelper',
                [
                    ['formTitle' => 'htmlTag'],
                    [
                        'tag'   => 'h2'
                    ]
                ]
            ]);

        $password = $this->createElement('password','password')
            ->setLabel('Старый Пароль')
            ->setAttribs(['class' => 'required', 'autocomplete' => 'off'])
            ->setRequired(true)
            ->setValidators([
                ['NotEmpty',true,['messages' => [Zend_Validate_NotEmpty::INVALID => "Поле обязательно для заполнения!"]]],
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
                    ['wrap2' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'input-row'
                    ]
                ],
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'col'
                    ]
                ]
            ]);

        $newPassword = $this->createElement('password','newPassword')
            ->setLabel('Новый Пароль')
            ->setAttribs(['class' => 'required', 'autocomplete' => 'off','minlength' => 6])
            ->setRequired(true)
            ->setValidators([
                ['NotEmpty',true,['messages' => [Zend_Validate_NotEmpty::INVALID => "Поле обязательно для заполнения!"]]],
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
                    ['wrap2' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'input-row'
                    ]
                ],
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'col'
                    ]
                ]
            ]);

        $newPasswordConfirm = $this->createElement('password','newPasswordConfirm')
            ->setLabel('Повторите Пароль')
            ->setAttribs(['class' => 'required', 'autocomplete' => 'off', 'equalTo' => "#newPassword"])
            ->setRequired(true)
            ->setValidators([
                ['NotEmpty',true,['messages' => [Zend_Validate_NotEmpty::INVALID => "Поле обязательно для заполнения!"]]],
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
                        'class' => 'input-row'
                    ]
                ],
                [
                    ['wrap2' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'col'
                    ]
                ]
            ]);

        $submit = $this->createElement('note','submit')
            ->setValue('Сохранить изменения')
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'       => 'a',
                        'class'     => 'btn blue waves-effect',
                    ]
                ],
                [
                    ['wrap2' => 'htmlTag'],
                    [
                        'tag'       => 'div',
                        'class'     => 'row-submit',
                    ]
                ]
            ]);

        $this->addElement($title);
        $this->addElement($userId);
        $this->addDisplayGroup([
            $password,
            $newPassword,
            $newPasswordConfirm
        ], 'mainFields', ['decorators' => [
            'FormElements',
            ['htmlTag',['tag' => 'div', 'class' => 'row']]
        ]
        ]);
        $this->addElement($submit);
    }
}
