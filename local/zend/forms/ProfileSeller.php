<?

/**
 * Class Sibirix_Form_ProfileClient
 */
class Sibirix_Form_ProfileSeller extends Sibirix_Form_Default {

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
                "name"  => $currentUser->NAME,
                "phone" => $currentUser->PERSONAL_PHONE,
                "email" => $currentUser->EMAIL,
                "lastName" => $currentUser->LAST_NAME,
                "portfolio" => $currentUser->WORK_WWW,
                "about" => $currentUser->WORK_PROFILE,
				"address" => $currentUser->PERSONAL_STREET,
				"city" => $currentUser->PERSONAL_CITY,
				"kompany" => $currentUser->WORK_COMPANY
            ];
        }

        $this->setMethod('post');
        $this->setAttribs(['class' => 'js-profile-form']);
        $this->setAction($this->getView()->url([], 'profile-provider-update'));
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

        $title = $this->createElement('note', 'title')
            ->setValue('Личные данные')
            ->setDecorators([
                'ViewHelper',
                [
                    ['formTitle' => 'htmlTag'],
                    [
                        'tag'   => 'h2'
                    ]
                ]
            ]);

        $userId = $this->createElement('hidden', 'userId')
            ->setFilters(['StringTrim', 'StripTags'])
            ->setValue($populateValues['ID'])
            ->setDecorators([
                'ViewHelper',
                [],
            ]);

        $name = $this->createElement('text','name')
            ->setLabel('Имя')
            ->setValue($populateValues['name'])
            ->setAttribs(['class' => 'required', 'autocomplete' => 'off', 'maxlength' => 100])
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
                ],
                [
                    ['wrap2' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'col-50p'
                    ]
                ]
            ]);

        $email = $this->createElement('text','email')
            ->setLabel('E-mail')
            ->setAttribs(['class' => 'required is-email', 'autocomplete' => 'off', 'maxlength' => 100])
            ->setValue($populateValues['email'])
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
                ],
                [
                    ['wrap2' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'col-50p'
                    ]
                ]
            ]);

        $lastName = $this->createElement('text','lastName')
            ->setLabel('Фамилия')
            ->setValue($populateValues['lastName'])
            ->setAttribs(['class' => 'required', 'autocomplete' => 'off', 'maxlength' => 100])
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
                ],
                [
                    ['wrap2' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'col-50p'
                    ]
                ]
            ]);

        $phone = $this->createElement('text','phone')
            ->setLabel('Телефон')
            ->setValue($populateValues['phone'])
            ->setAttribs(['class' => 'is-phone', 'autocomplete' => 'off', 'maxlength' => 20])
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
                ],
                [
                    ['wrap2' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'col-50p'
                    ]
                ]
            ]);
			
		$kompany = $this->createElement('text','kompany')
            ->setLabel('Название компании')
            ->setValue($populateValues['kompany'])
            ->setAttribs(['class' => '', 'autocomplete' => 'off', 'maxlength' => 200])
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
                ],
                [
                    ['wrap2' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'col-50p'
                    ]
                ]
            ]);
		
		$city = $this->createElement('text','city')
            ->setLabel('Город')
            ->setValue($populateValues['city'])
            ->setAttribs(['class' => 'is-city', 'autocomplete' => 'off', 'maxlength' => 20])
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
                ],
                [
                    ['wrap2' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'col-50p'
                    ]
                ]
            ]);
		
		$address = $this->createElement('text','address')
            ->setLabel('Улица, дом')
            ->setValue($populateValues['address'])
            ->setAttribs(['class' => 'is-address', 'autocomplete' => 'off', 'maxlength' => 20])
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
                ],
                [
                    ['wrap2' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'col-50p'
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
                ],
                [
                    ['hr' => 'htmlTag'],
                    [
                        'tag'       => 'hr',
                        'placement' => 'prepend'
                    ]
                ]
            ]);

        $this->addElement($title);
        $this->addElement($userId);
        $this->addDisplayGroup([
            $name,
            $email,
			$city,
			$address
        ], 'row1', ['decorators' => [
            'FormElements',
            ['htmlTag',['tag' => 'div', 'class' => 'row']]
        ]
        ]);

        $this->addDisplayGroup([
            $lastName,
            $phone,
			$kompany
        ], 'row3', ['decorators' => [
            'FormElements',
            ['htmlTag',['tag' => 'div', 'class' => 'row']]
        ]
        ]);

        $this->addElement($submit);
    }
}
