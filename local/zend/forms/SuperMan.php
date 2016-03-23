<?
/**
 * Class Sibirix_Form_Feedback
 */
class Sibirix_Form_SuperMan extends Sibirix_Form_Default {

    public function init() {
        parent::init();

        $userModel   = new Sibirix_Model_User();
		$cityModel    = new Sibirix_Model_City();
        $populateValues = [];
        if($userModel->isAuthorized()) {
            $currentUser = $userModel->getCurrent();
            $populateValues = [
                "NAME"  => $currentUser->NAME,
                "PERSONAL_PHONE" => $currentUser->PERSONAL_PHONE,
				"ADDRESS" => $currentUser->PERSONAL_STREET,
            ];
        }
		
		$title = $this->createElement('note', 'title')
            ->setValue('Вызвать замерщика')
            ->setDecorators([
                'ViewHelper',
                [
                    ['formTitle' => 'htmlTag'],
                    [
                        'tag'   => 'h2',
						'class' => 'win_title'
                    ]
                ]
        ]);
		
        $this->setAttribs(['class' => 'js-super-man-form', 'data-title' => Settings::getOption('feedbackMessage'), 'data-text' => Settings::getOptionText('feedbackMessage')]);
        $this->setAction('/project/super-man');
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
            ->setLabel('Ваше Имя')
            ->setAttribs(['class' => 'required', 'maxlength' => 100, 'placeholder' => ''])
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
            ->setLabel('Телефон')
            ->setAttribs(['class' => 'required', 'maxlength' => 100, 'placeholder' => ''])
            ->setRequired(true)
            ->setValue($populateValues['PERSONAL_PHONE'])
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
			
		$address = $this->createElement('text','address')
            ->setLabel('Адрес')
            ->setAttribs(['class' => 'required', 'maxlength' => 100, 'placeholder' => ''])
            ->setRequired(true)
            ->setValue($populateValues['ADDRESS'])
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



        $antiBot = $this->createElement("hidden", "protect")
            ->setAttrib("class", "js-protect")
            ->setDecorators([
                'ViewHelper',
                []
            ]);
	
		$select2 = '<label for="city" class="required">Город</label>';
		$select2 .= '<select class="js-select-city" name="city">';
		$cityList = $cityModel->getElements();
		foreach ($cityList as $city){
			$select2 .= '<option value="'.$city->NAME .'">'.$city->NAME .'</option>';
		};
		$select2 .='</select>';
		$city = $this->createElement('note','city')
            ->setValue(
				$select2
			)
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'input-row'
                    ]
                ]
            ]);
		
        $submit = $this->createElement('note','send')
			->setLabel('Адрес')
            ->setValue('Вызвать')
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
		
		$this->addElement($title);
		$this->addElement($name);
		$this->addElement($contacts);
		$this->addElement($city);
		$this->addElement($address);
        $this->addElement($antiBot);
        $this->addElement($submit);
    }
}
