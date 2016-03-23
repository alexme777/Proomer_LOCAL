<?

/**
 * Class Sibirix_Form_Registration
 */
class Sibirix_Form_Planirovka extends Sibirix_Form_Default {

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
            ->setValue('Не нашли нужную планировку?')
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
    
			
		$text_left = $this->createElement('note','text_left')
            ->setValue('
				<div class="text_right">
				<p>Или вызовите БЕСПЛАТНОГО замерщика:</p>
				<div><a class="js-call-super-man btn blue waves-effect js-fancybox fancyboxLink" href="#call-super-man">
						ВЫЗВАТЬ ЗАМЕРЩИКА</a></div>
				</p>
				</div>'
			)
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'message text_left col-xs-6 col-sm-6 col-md-6'
                    ]
                ]
            ]);
			
		$text_right = $this->createElement('note','text_right')
            ->setValue('
				<div class="text_right">
				<p>Нужна консультация? Звоните:<br/>
				<span class="phone">+7(975)856-14-56</span><br/>
				Работаем с 08:00 до 22:00
				</p>
				</div>'
			)
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'message col-xs-6 col-sm-6 col-md-6'
                    ]
                ]
            ]);
	
	
        $this->addElement($title);
		$this->addElement($text_left);
		$this->addElement($text_right);
		/*$this->addDisplayGroup([
            $text_left,
			$text_right,
            //$submit,
            $authLink
        ], 'mainFields', ['decorators' => [
            'FormElements',
            ['htmlTag',['tag' => 'div', 'class' => 'inline-bn']]
        ]
        ]);*/
     
    }
}
