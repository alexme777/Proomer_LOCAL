<?

/**
 * Class Sibirix_Form_Order
 */
class Sibirix_Form_Order extends Sibirix_Form_Default {

 
    public function init() {
        parent::init();

        $userModel   = new Sibirix_Model_User();
        $populateValues = [];
        if($userModel->isAuthorized()) {
            $currentUser = $userModel->getCurrent();
            $populateValues = [
                "NAME"  => $currentUser->NAME,
                "LAST_NAME" => $currentUser->LAST_NAME,
                "PHONE" => $currentUser->PERSONAL_PHONE,
                "EMAIL" => $currentUser->EMAIL
            ];
        }

        $this->setAction($this->getView()->url([], 'order-process'));
        $this->setMethod('post');
        $this->setAttrib('class', 'js-order-form');
        $this->setDecorators([
            'FormElements',
            ['Form', []],
        ]);

        /**
         * Данные покупателя
         */
        $title = $this->createElement('note', 'title')
            ->setValue('Личные данные')
            ->setDecorators(['ViewHelper',
                [
                    ['formTitle' => 'htmlTag'],
                    [
                        'tag'   => 'h4'
                    ]
                ]
            ]);

        $name = $this->createElement('text','NAME')
            ->setLabel('Имя')
            ->setAttribs(['class' => 'required', 'autocomplete' => 'off', 'maxlength' => 100])
            ->setRequired(true)
            ->setValue($populateValues['NAME'])
            ->setValidators([
                ['NotEmpty',true,['messages' => [Zend_Validate_NotEmpty::INVALID => "Не может быть пустым"]]],
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

        $lastName = $this->createElement('text','LAST_NAME')
            ->setLabel('Фамилия')
            ->setAttribs(['class' => 'required', 'autocomplete' => 'off', 'maxlength' => 100])
            ->setRequired(true)
            ->setValue($populateValues['LAST_NAME'])
            ->setValidators([
                ['NotEmpty',true,['messages' => [Zend_Validate_NotEmpty::INVALID => "Не может быть пустым"]]],
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

        $phone = $this->createElement('text','PHONE')
            ->setLabel('Телефон')
            ->setAttribs(['class' => 'required is-phone', 'autocomplete' => 'off', 'maxlength' => 20])
            ->setRequired(true)
            ->setValue($populateValues['PHONE'])
            ->setValidators([
                ['NotEmpty',true,['messages' => [Zend_Validate_NotEmpty::INVALID => "Не может быть пустым"]]],
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

        $email = $this->createElement('text','EMAIL')
            ->setLabel('Email')
            ->setAttribs(['class' => 'required is-email', 'autocomplete' => 'off', 'maxlength' => 100])
            ->setRequired(true)
            ->setValue($populateValues['EMAIL'])
            ->setValidators([
                ['NotEmpty',true,['messages' => [Zend_Validate_NotEmpty::INVALID => "Не может быть пустым"]]],
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

        $submit = $this->createElement('note','submit')
            ->setValue('Оформить заказ')
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

        $this->addDisplayGroup([
            $name,
            $lastName,
        ], 'row1', ['decorators' => [
            'FormElements',
            ['htmlTag',['tag' => 'div', 'class' => 'row']]
        ]
        ]);

        $this->addDisplayGroup([
            $phone,
            $email
        ], 'row3', ['decorators' => [
            'FormElements',
            ['htmlTag',['tag' => 'div', 'class' => 'row']]
        ]
        ]);

        $this->addElement($submit);
    }
}
