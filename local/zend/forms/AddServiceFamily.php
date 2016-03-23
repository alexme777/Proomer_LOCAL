<?
/**
 * Class Sibirix_Form_AddDesignStep2
 */
class Sibirix_Form_AddServiceFamily extends Sibirix_Form_Default {

    public function init() {
        parent::init();

        $this->setAttribs(['class' => 'js-add-family-form family-form']);
        $this->setAction($this->getView()->url());
        $this->setMethod('post');
        $this->setDecorators([
            'FormElements',
            ['Form', []]
        ]);

      
		$people1 = $this->createElement('hidden','people1')->setLabel("Взрослые");
        $style = $this->createElement('Multiselect', 'style')
            ->setMultiOptions(Sibirix_Model_Reference::getReference(HL_STYLE, ["UF_NAME"], "UF_XML_ID"))
            ->setAttribs([
                "placeholder" => '',
                'wrapClass3'  => 'values',
                'label'       => 'Возраст',
                'data-limit'  => Settings::getOption("MAX_STYLE_CNT"),
             
            ])
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'input-row-inner js-form-block people'
                    ]
                ],
                [
                    ['wrap2' => 'htmlTag'],
                    [
                        'tag'      => 'div',
                        'class'    => 'input-cell',
                        'openOnly' => true
                    ]
                ]
            ]);
			
		$people2 = $this->createElement('hidden','people2')->setLabel("Взрослые");
        $age2 = $this->createElement('Multiselect', 'style')
            ->setMultiOptions(Sibirix_Model_Reference::getReference(HL_STYLE, ["UF_NAME"], "UF_XML_ID"))
            ->setAttribs([
                "placeholder" => '',
                'wrapClass3'  => 'values',
                'label'       => 'Возраст',
                'data-limit'  => Settings::getOption("MAX_STYLE_CNT"),
               
            ])
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'input-row-inner js-form-block people'
                    ]
                ],
                [
                    ['wrap2' => 'htmlTag'],
                    [
                        'tag'      => 'div',
                        'class'    => 'input-cell',
                        'openOnly' => true
                    ]
                ]
            ]);
			

        $style->helper = 'customMultiSelect';

      

        $this->addDisplayGroup([
			$people1,
            $style,
			$people2,
            $age2,
  
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