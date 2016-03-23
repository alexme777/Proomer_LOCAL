<?
/**
 * Class Sibirix_Form_AddDesignStep2
 */
class Sibirix_Form_AddDesignStep2 extends Sibirix_Form_Default {

    public function init() {
        parent::init();

        $this->setAttribs(['class' => 'js-add-design-step2-form']);
        $this->setAction($this->getView()->url());
        $this->setMethod('post');
        $this->setDecorators([
            'FormElements',
            ['Form', []]
        ]);

        $designName = $this->createElement('text','designName')
            ->setLabel("Название")
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
        $shortDescription = $this->createElement('textarea','shortDescription')
            ->setLabel("Краткое описание")
            ->setAttribs(['class' => 'required materialize-textarea', "rows" => 2,  'maxlength' => 100])
            ->setRequired(true)
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
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'input-row'
                    ]
                ]
            ]);

        $designDescription = $this->createElement('textarea','designDescription')
            ->setLabel("Описание")
            ->setAttribs(['class' => 'required materialize-textarea', "rows" => 7,  'maxlength' => 255])
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

        $mainPhoto = new Sibirix_Form_Element_Dropzone('mainPhoto');
        $mainPhoto->setLabel('Главное изображение дизайн-проекта')
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


        $style = $this->createElement('Multiselect', 'style')
            ->setMultiOptions(Sibirix_Model_Reference::getReference(HL_STYLE, ["UF_NAME"], "UF_XML_ID"))
            ->setAttribs([
                "placeholder" => 'Введите название стиля',
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
                        'class'    => 'input-cell',
                        'openOnly' => true
                    ]
                ]
            ]);
        $style->helper = 'customMultiSelect';

        $color = $this->createElement('Multiselect', 'color')
            ->setMultiOptions(Sibirix_Model_Reference::getReference(HL_PRIMARY_COLORS, ["UF_NAME"], "UF_XML_ID"))
            ->setAttribs([
                "placeholder" => 'Введите название стиля',
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
                        'class'     => 'input-cell',
                        'closeOnly' => true
                    ]
                ]
            ]);
        $color->helper = 'customMultiSelect';

        $designPrice = $this->createElement('text','designPrice')
            ->setLabel("Цена дизайна")
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

        $designFree = $this->createElement('checkbox','designFree')
            ->setDescription("Бесплатный дизайн")
            ->setAttribs(["class" => "js-design-free"])
            ->setDecorators([
                'ViewHelper',
                'Label',
                [
                    ['icon' => 'htmlTag'],
                    [
                        'tag'   => 'i',
                        'placement' => "append"
                    ]
                ],
                [
                    'description',
                    [
                        'tag'   => 'span',
                    ]
                ],
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'label',
                        'class'   => 'free-design',
                    ]
                ]
            ]);


        $squareLabel = $this->createElement("note", "squareLabel")
            ->setValue('Планировка')
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'label',
                        'for'   => 'squareLabel',
                        'class'   => 'block'
                    ]
                ],
            ]);
        $squareBefore = $this->createElement("note", "squareBefore")
            ->setValue('Общая площадь')
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'span',
                        'class' => 'label'
                    ]
                ],
            ]);
        $square = $this->createElement('text','square')
            ->setAttribs(['class' => 'required square-input js-is-numeric', 'maxlength' => 100, "placeholder" => "число"])
            ->setRequired(true)
            ->setValidators([
                ['StringLength', false, ['max' => 100, 'encoding' => 'utf-8',]],
            ])
            ->setFilters(['StringTrim', 'StripTags'])
            ->setDecorators([
                'ViewHelper',
                [
                    ['errorPlacement' => 'htmlTag'],
                    [
                        'tag'       => 'span',
                        'class'     => 'js-error error-message',
                        'placement' => 'append'
                    ]
                ]
            ]);
        $squareAfter = $this->createElement("note", "squareAfter")
            ->setValue('М<sup>2</sup>')
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'span',
                        'class' => 'label'
                    ]
                ],
            ]);

        $rooms = $this->createElement("note", "roomList")
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'js-room-list input-row rooms'
                    ]
                ],
            ]);
        $rooms->helper = "roomList";

        $addRoomName = $this->createElement("text", "addRoomName")
            ->setAttribs(["placeholder" => "Введите название", "class" => "room-name-input",  'maxlength' => 70])
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
                        'tag'   => 'span',
                        'class' => 'room-input-wrap'
                    ]
                ]
            ]);

        $addRoomSquare = $this->createElement("text", "addRoomSquare")
            ->setAttribs(["placeholder" => "Площадь", "class" => "js-is-numeric room-square-input"])
            ->setDescription("М<sup>2</sup>")
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
                    'description',
                    [
                        'tag' => 'span',
                        'escape' => false
                    ]
                ],
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'span',
                        'class' => 'room-input-wrap'
                    ]
                ]
            ]);

        $addRoomButton = $this->createElement("note", "addRoomButton")
            ->setValue("Добавить помещение")
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'js-add-room-button button add-button waves-effect'
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

        $this->addElement($designName);
        $this->addElement($shortDescription);
        $this->addElement($designDescription);
        $this->addElement($mainPhoto);
        $this->addDisplayGroup([
            $mainPhoto,
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

        $this->addDisplayGroup([
            $designPrice,
            $designFree,
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

        $this->addDisplayGroup([
            $squareLabel,
            $squareBefore,
            $square,
            $squareAfter,
        ], 'squareGroup', [
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
        $this->addElement($rooms);
        $this->addDisplayGroup([
            $addRoomName,
            $addRoomSquare,
            $addRoomButton,
        ], 'addRoomPanel', [
            'decorators' => [
                'FormElements',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag' => 'div',
                        'class' => 'fields'
                    ]
                ],
                [
                    ['wrap2' => 'htmlTag'],
                    [
                        'tag' => 'div',
                        'class' => 'js-add-room-panel add-room'
                    ]
                ]
            ]
        ]);
        $this->addElement($nextAddStep);
    }
}