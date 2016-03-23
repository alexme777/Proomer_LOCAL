<?
/**
 * Class Sibirix_Form_AddDesignStep2
 */
class Sibirix_Form_AddProviderStep2 extends Sibirix_Form_Default {

    public function init() {
        parent::init();

        $this->setAttribs(['class' => 'form-block js-form-block']);
        $this->setAction($this->getView()->url());
        $this->setMethod('post');
        $this->setDecorators([
            'FormElements',
            ['Form', []]
        ]);

		
		$designId = $this->createElement('hidden','designId');
      
		
		$designName = $this->createElement('text','designName')
            ->setLabel('Название')
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
			
		$matherial = $this->createElement('text','matherial')
            ->setLabel("Материал")
            ->setAttribs(['class' => 'required', 'maxlength' => 50])
            ->setRequired(true)
            ->setValidators([
                ['StringLength', false, ['max' => 50, 'encoding' => 'utf-8',]],
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
			
			
					
		$article = $this->createElement('text','article')
            ->setLabel("Артикул")
            ->setAttribs(['class' => 'required', 'maxlength' => 50])
            ->setRequired(true)
            ->setValidators([
                ['StringLength', false, ['max' => 50, 'encoding' => 'utf-8',]],
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
			

			
		$width = $this->createElement('text','width')
            ->setLabel('Ширина')
            ->setAttribs(['class' => 'required'])
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
			
		$length = $this->createElement('text','length')
            ->setLabel('Длина')
            ->setAttribs(['class' => 'required'])
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
			
		$height = $this->createElement('text','height')
            ->setLabel('Высота')
            ->setAttribs(['class' => 'required'])
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
			
        $shortDescription = $this->createElement('textarea','shortDescription')
            ->setLabel("Краткое описание")
            ->setAttribs(['class' => 'required materialize-textarea', "rows" => 4,  'maxlength' => 255])
            ->setRequired(true)
            ->setValidators([
                ['StringLength', false, ['max' => 255, 'encoding' => 'utf-8',]],
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

        $designDescription = $this->createElement('textarea','designDescription')
            ->setLabel("Описание")
            ->setAttribs(['class' => 'required materialize-textarea', "rows" => 8,  'maxlength' => 1000])
            ->setRequired(true)
            ->setValidators([
                ['StringLength', false, ['max' => 1000, 'encoding' => 'utf-8',]],
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
		
		/*$mainPhoto = new Sibirix_Form_Element_Dropzone('mainPhoto');
        $mainPhoto->setLabel('Главное изображение')
            ->setAttribs(["classZone" => "js-main-photo-dropzone"])
            ->setDecorators([
                'ViewHelper',
                'Label',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'input-cell'
                    ]
                ],
            ]);
		*/
		/*$anonsPhoto = new Sibirix_Form_Element_Dropzone('anonsPhoto');
        $anonsPhoto->setLabel('Анонс изображение')
            ->setAttribs(["classZone" => "js-anons-photo-dropzone"])
            ->setDecorators([
                'ViewHelper',
                'Label',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'input-cell'
                    ]
                ],
            ]);*/
		$planImage = new Sibirix_Form_Element_Dropzone('planImage');
		$planImage->setLabel('Изображения')
			->setAttribs(["classZone" => "js-plan-dropzone js-photo-dropzone plan-dropzone inline", "elementId" => $this->_designId])
			->setDecorators([
				'ViewHelper',
				[
					['tooltip' => 'htmlTag'],
					[
						'tag'              => 'span',
						'class'            => 'help js-tooltip mainTooltip',
						'data-description' => Settings::getOption("TOOLTIP_PLAN_DESIGN"),
						'placement'        => 'prepend'
					]
				],
				'Label',
				[
					['wrap1' => 'htmlTag'],
					[
						'tag'   => 'div',
						'class' => 'input-row'
					]
				],
			]);
		//$this->addElement($mainPhoto);
		//$this->addElement($anonsPhoto);

		$this->addElement($planImage);
		$category = $this->createElement('Multiselect', 'category')
            ->setMultiOptions(Sibirix_Model_Reference::getReference(HL_CATEGORY, ["UF_NAME"], "UF_XML_ID"))
            ->setAttribs([
                "placeholder" => 'Выберите категории',
                'help'        => Settings::getOption("TOOLTIP_COLORS_DESIGN"),
                'wrapClass3'  => 'values',
                'label'       => 'Категория',
                'data-limit'  => 1,
                'multiple'    => 'multiple'
            ])
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'input-row-inner js-form-block'
                    ]
                ],
                [
                    ['wrap2' => 'htmlTag'],
                    [
                        'tag'      => 'div',
                        'class'    => 'input-select',
                        'openOnly' => true
                    ]
                ]
            ]);
			
			$category->helper = 'customMultiSelect';
			
		$madein = $this->createElement('Multiselect', 'madein')
            ->setMultiOptions(Sibirix_Model_Reference::getReference(HL_MADEIN, ["UF_NAME"], "UF_XML_ID"))
            ->setAttribs([
                "placeholder" => 'Выберите производителя',
                'help'        => Settings::getOption("TOOLTIP_COLORS_DESIGN"),
                'wrapClass3'  => 'values',
                'label'       => 'Производитель',
                'data-limit'  => 1,
                'multiple'    => 'multiple'
            ])
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'input-row-inner js-form-block'
                    ]
                ],
                [
                    ['wrap2' => 'htmlTag'],
                    [
                        'tag'      => 'div',
                        'class'    => 'input-select',
                        'openOnly' => true
                    ]
                ]
            ]);
			
						
        $madein->helper = 'customMultiSelect';
		
        $style = $this->createElement('Multiselect', 'style')
            ->setMultiOptions(Sibirix_Model_Reference::getReference(HL_STYLE, ["UF_NAME"], "UF_XML_ID"))
            ->setAttribs([
                "placeholder" => 'Выберите стили',
                'help'        => Settings::getOption("TOOLTIP_STYLE_DESIGN"),
                'wrapClass3'  => 'values',
                'label'       => 'Стиль дизайна',
                'data-limit'  => Settings::getOption("MAX_STYLE_CNT"),
                'multiple'    => 'multiple'
            ])
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'input-row-inner js-form-block'
                    ]
                ],
                [
                    ['wrap2' => 'htmlTag'],
                    [
                        'tag'      => 'div',
                        'class'    => 'input-select',
                        'openOnly' => true
                    ]
                ]
            ]);
        $style->helper = 'customMultiSelect';

        $color = $this->createElement('Multiselect', 'color')
            ->setMultiOptions(Sibirix_Model_Reference::getReference(HL_PRIMARY_COLORS, ["UF_NAME"], "UF_XML_ID"))
            ->setAttribs([
                "placeholder" => 'Выберите цвета',
                'help'        => Settings::getOption("TOOLTIP_COLORS_DESIGN"),
                'wrapClass3'  => 'values',
                'label'       => 'Основные цвета',
                'data-limit'  => Settings::getOption("MAX_COLOR_CNT"),
                'multiple'    => 'multiple'
            ])
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'input-row-inner js-form-block'
                    ]
                ],
                [
                    ['wrap2' => 'htmlTag'],
                    [
                        'tag'       => 'div',
                        'class'     => 'input-select',
                        'closeOnly' => true
                    ]
                ]
            ]);

			
        $color->helper = 'customMultiSelect';

        $designPrice = $this->createElement('text','designPrice')
            ->setLabel("Цена")
            ->setAttribs(['class' => 'required price-input js-design-price js-is-numeric', 'maxlength' => 100])
            ->setValidators([
                ['StringLength', false, ['max' => 100, 'encoding' => 'utf-8',]],
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
                    'label',
                    [
                        'class' => 'block'
                    ]
                ]
            ]);


        $nextAddStep = $this->createElement("note", "nextAddStep")
            ->setValue("Следующий шаг")
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'       => 'a',
                        'class'     => 'js-next-add-step btn blue waves-effect',
                        'data-step' => 2,
                        'href'      => "javascript:void(0);",
                    ]
                ],
                [
                    ['wrap2' => 'htmlTag'],
                    [
                        'tag'       => 'div',
                        'class'     => 'btn-wrapper',
                    ]
                ]
            ]);
			
		$this->addElement($designId);
        $this->addElement($designName);
		$this->addDisplayGroup([
            $designPrice,
           // $designFree,
        ], 'price', [
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
		
        $this->addElement($shortDescription);
        $this->addElement($designDescription);
		$this->addElement($article);
		$this->addElement($matherial);
		
        $this->addDisplayGroup([
			$category,
			$madein,
            $style,
            $color,
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
		$this->addElement($width);
		$this->addElement($length);
		$this->addElement($height);
	
	

        $nextAddStep = $this->createElement("note", "nextAddStep")
            ->setValue("Добавить")
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'       => 'a',
                        'class'     => 'js-next-add-step btn blue waves-effect',
                        'data-step' => 3,
                        'href'      => "javascript:void(0);",
                    ]
                ],
                [
                    ['wrap2' => 'htmlTag'],
                    [
                        'tag'       => 'div',
                        'class'     => 'btn-wrapper',
                    ]
                ]
            ]);


        $this->addElement($nextAddStep);
    


    }
}