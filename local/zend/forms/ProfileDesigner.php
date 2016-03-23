<?

/**
 * Class Sibirix_Form_ProfileDesigner
 */
class Sibirix_Form_ProfileDesigner extends Sibirix_Form_Default {

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
                "styles" => Sibirix_Model_Reference::getReference(HL_STYLE, array("UF_NAME"), "ID"),
                "selectedStyles" => $currentUser->UF_STYLES
				//"address" => $currentUser->PERSONAL_STREET
				//"city" => $currentUser->PERSONAL_CITY,
				//"kompany" => $currentUser->WORK_COMPANY
            ];
        }

        $this->setMethod('post');
        $this->setAttribs(['class' => 'js-profile-form']);
        $this->setAction($this->getView()->url([], 'profile-designer-update'));
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
                        'class' => 'col'
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
                        'class' => 'col'
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
                        'class' => 'col'
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
                        'class' => 'col'
                    ]
                ]
            ]);

        $link = $this->createElement('note','link')
            ->setLabel('Профиль от PROOMER')
            ->setValue('<a class="new-window under-link" target="_blank" href="'. EZendManager::url(['elementId' => $populateValues["ID"]],'designer-detail') .'">designer'.$populateValues["ID"].'</a>')
            ->setDecorators([
                'ViewHelper',
                'Label',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'input-row',
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

        $portfolio = $this->createElement('text','portfolio')
            ->setLabel('Портфолио')
            ->setValue($populateValues['portfolio'])
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
                        'class' => 'col'
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
                        'class' => 'col'
                    ]
                ]
            ]);
		
		$city = $this->createElement('textarea','city')
            ->setLabel('Город')
            ->setValue($populateValues['city'])
            ->setAttribs(['class' => 'materialize-textarea', 'autocomplete' => 'off', 'rows' => '1', 'maxlength' => Settings::getOption('maxLengthCity')])
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
                        'class' => 'col-3'
                    ]
                ]
            ]);
		
		$address = $this->createElement('textarea','address')
            ->setLabel('Улица, дом')
            ->setValue($populateValues['address'])
            ->setAttribs(['class' => 'materialize-textarea', 'autocomplete' => 'off', 'rows' => '1', 'maxlength' => Settings::getOption('maxLengthAddress')])
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
                        'class' => 'col-3'
                    ]
                ]
            ]);
		
        $about = $this->createElement('textarea','about')
            ->setLabel('О себе')
            ->setValue($populateValues['about'])
            ->setAttribs(['class' => 'materialize-textarea', 'autocomplete' => 'off', 'rows' => '1', 'maxlength' => Settings::getOption('maxLengthAbout')])
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
                        'class' => 'col-3'
                    ]
                ]
            ]);

        $styles = $this->createElement('Multiselect', 'styles')
            ->setMultiOptions($populateValues['styles'])
            ->setAttribs(["placeholder" => 'Введите название стиля',
                "wrapClass1" => 'col',
                'wrapClass2' => 'input-row',
                'wrapClass3' => 'col-2',
                'label' => 'Основные стили',
                'data-limit' => 3,
                'multiple' => 'multiple'])
            ->setValue($populateValues['selectedStyles'])
            ->setDecorators([
                'ViewHelper',
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
                        'class' => 'row'
                    ]
                ],
            ]);

        $styles->helper = 'customMultiSelect';

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
            $lastName,
            $link,
        ], 'row1', ['decorators' => [
            'FormElements',
            ['htmlTag',['tag' => 'div', 'class' => 'row']]
        ]
        ]);

        $this->addDisplayGroup([
            $email,
            $phone,
            $portfolio,
			$kompany,
        ], 'row3', ['decorators' => [
            'FormElements',
            ['htmlTag',['tag' => 'div', 'class' => 'row']]
        ]
        ]);
		
		$this->addElement($city);
		$this->addElement($address);
        $this->addElement($about);
        $this->addElement($styles);
        $this->addElement($submit);
    }
}
