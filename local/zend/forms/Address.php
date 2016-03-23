<?

/**
 * Class Sibirix_Form_Registration
 */
class Sibirix_Form_Address extends Sibirix_Form_Default {

    /**
     *
     */
    public function init() {
        parent::init();

        $this->setMethod('post');
        $this->setAttribs(['class' => 'js-address-form', 'data-title' => Settings::getOption('registerMessage'), 'data-text' => Settings::getOptionText('registerMessage')]);
        $this->setAction($this->getView()->url([], 'user-registration'));
        $this->setDecorators([
            'FormElements',
            ['Form', []],
        ]);

        $title = $this->createElement('note', 'title')
            ->setValue('Ваш адрес?')
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
		
		$address = $this->createElement('text','address')
           
            ->setAttribs(['class' => 'required', 'autocomplete' => 'off', 'placeholder' => ''])
            ->setRequired(true)
            ->setValidators([
                ['NotEmpty',true,['messages' => [Zend_Validate_NotEmpty::INVALID => ""]]],
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
		
		$submit = $this->createElement('note','submit')
            ->setValue('Искать мой дом')
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
		$this->addElement($address);
		$this->addElement($submit);

     
    }
}
