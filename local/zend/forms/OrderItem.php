<?
/**
 * Class Sibirix_Form_Feedback
 */
class Sibirix_Form_OrderItem extends Sibirix_Form_Default {
	protected $itemId;

    public function __construct($itemId) {
       // parent::init();
		$this->itemId = $itemId;
        $userModel   = new Sibirix_Model_User();
        $populateValues = [];
        if($userModel->isAuthorized()) {
            $currentUser = $userModel->getCurrent();
            $populateValues = [
                "NAME"  => $currentUser->NAME,
                "EMAIL" => $currentUser->EMAIL
            ];
        }

        $this->setAttribs(['class' => 'js-order-form', 'data-title' => Settings::getOption('feedbackMessage'), 'data-text' => Settings::getOptionText('feedbackMessage')]);
        $this->setAction($this->getView()->url([], 'order-add'));
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
		
		$goodsId = $this->createElement('hidden','goodsId')->setValue($this->itemId);
		$colorId = $this->createElement('hidden','colorId')->setValue();
		$sizeId = $this->createElement('hidden','sizeId')->setValue();
		$materialId = $this->createElement('hidden','materialId')->setValue();
		
		$colorName = $this->createElement('hidden','colorName')->setValue();
		$sizeName = $this->createElement('hidden','sizeName')->setValue();
		$materialName = $this->createElement('hidden','materialName')->setValue();
		
		$count = $this->createElement('hidden','count')->setValue(1);
        $first_name = $this->createElement('text','first-name')
            ->setLabel('Имя')
            ->setAttribs(['class' => 'required', 'maxlength' => 100, 'placeholder' => 'Ваше Имя'])
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
			
		$second_name = $this->createElement('text','second-name')
            ->setLabel('Фамилия')
            ->setAttribs(['class' => 'required', 'maxlength' => 100, 'placeholder' => 'Ваша Фамилия'])
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

        $phone = $this->createElement('text','phone')
            ->setLabel('Телефон')
            ->setAttribs(['class' => 'required', 'maxlength' => 100, 'placeholder' => 'Телефон'])
            ->setRequired(true)
            ->setValue($populateValues['PHONE'])
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
			
		$email = $this->createElement('text','email')
            ->setLabel('E-mail')
            ->setAttribs(['class' => 'required', 'maxlength' => 100, 'placeholder' => 'E-mail'])
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
		
		$this->addElement($goodsId);
		$this->addElement($colorId);
		$this->addElement($sizeId);
		$this->addElement($materialId);
		$this->addElement($colorName);
		$this->addElement($sizeName);
		$this->addElement($materialName);
		$this->addElement($count);
        $this->addElement($antiBot);
        $this->addElement($first_name);
		$this->addElement($second_name);
		$this->addElement($phone);
        $this->addElement($email);
        $this->addElement($submit);
    }
}


