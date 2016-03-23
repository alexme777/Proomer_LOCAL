<?
/**
 * Class Sibirix_Form_Feedback
 */
class Sibirix_Form_SearchAddress extends Sibirix_Form_Default {

    public function init() {
        parent::init();

        $userModel   = new Sibirix_Model_User();
       
        $this->setAttribs(['class' => 'js-feedback-form search-form-design', 'data-title' => Settings::getOption('feedbackMessage'), 'data-text' => Settings::getOptionText('feedbackMessage')]);
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

        $address = $this->createElement('text','address')
            ->setAttribs(['class' => 'required input-search', 'maxlength' => 100, 'placeholder' => 'Ваш адрес?'])
            ->setRequired(true)
            ->setValidators([
                ['NotEmpty',true,['messages' => [Zend_Validate_NotEmpty::INVALID => "Введите адрес"]]],
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

        $submit = $this->createElement('note','send')
            ->setValue('Подобрать')
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
			
		$this->addDisplayGroup([
			$address,
			$submit,
        ], 'selects', [
            'decorators' => [
                'FormElements',
                [
                    'htmlTag',
                    [
                        'tag' => 'div',
                        'class' => 'input-row'
                    ]
                ]
            ]
        ]);

    }
}
