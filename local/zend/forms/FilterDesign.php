<?
/**
 * Class Sibirix_Form_FilterDesign
 */
class Sibirix_Form_FilterDesign extends Sibirix_Form_Default {

    public function init() {
        parent::init();

        $this->setAttribs(['class' => 'js-filter-form']);
        $this->setAction($this->getView()->url());
        $this->setMethod('post');
        $this->setDecorators([
            'FormElements',
            ['Form', []],
            [
                ['wrap1' => 'htmlTag'],
                [
                    'tag'   => 'div',
                    'class' => 'search-form'
                ]
            ]
        ]);

        $clear = $this->createElement('note', 'clear')
            ->setValue('<a class="js-clear-filter form-clear js-tooltip" data-description="Сбросить фильтр"></a>')
            ->setDecorators([
                'ViewHelper',
                []
            ]);

        $designModel = new Sibirix_Model_Design();

        $price = new Sibirix_Form_Element_RangeSlider('price');
        $price->setLabel('Цена дизайна')
            ->setAttribs($designModel->getExtremePrice())
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
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'input-cell js-range-slider'
                    ]
                ]
            ]);

        $budget = new Sibirix_Form_Element_RangeSlider('budget');
        $budget->setLabel('Проект за М<sup>2</sup>')
            ->setAttribs($designModel->getExtremeBudget())
            ->setFilters(['StringTrim', 'StripTags'])
            ->setDecorators([
                'ViewHelper',
                [
                    'Label',
                    [
                        'placement' => 'prepend',
                        'escape'    => false
                    ]
                ],
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'input-cell js-range-slider'
                    ]
                ]
            ]);

        $primaryColor = $this->createElement('Multiselect','primaryColor')
            ->setAttribs(['class' => 'js-select'])
            ->setMultiOptions(Sibirix_Model_Reference::getReference(HL_PRIMARY_COLORS, array("UF_NAME"), "UF_XML_ID"))
            ->setFilters(['StringTrim', 'StripTags'])
            ->setAttribs(["placeholder" => 'Выберите названия цветов',
                          'wrapClass3' => 'values',
                          'label' => 'Цвет',
                          'multiple' => 'multiple'])
            ->setDecorators([
                'ViewHelper',
                [
                    'Label',
                    [
                        'placement' => 'prepend'
                    ]
                ],
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'input-cell js-form-block'
                    ]
                ]
            ]);

        $primaryColor->helper = 'customMultiSelect';

        $style = $this->createElement('Multiselect','style')
            ->setAttribs(['class' => 'js-select'])
            ->setAttribs(["placeholder" => 'Выберите названия стилей',
                          'wrapClass3' => 'values',
                          'label' => 'Стиль',
                          'multiple' => 'multiple'])
            ->setMultiOptions(Sibirix_Model_Reference::getReference(HL_STYLE, array("UF_NAME"), "UF_XML_ID"))
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
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'input-cell js-select-row js-form-block'
                    ]
                ]
            ]);

        $style->helper = 'customMultiSelect';

        //Доп поля для фильтрации с сервиса поиска дизайнов
        $complexId = $this->createElement('hidden', 'complexId')
            ->setDecorators(['ViewHelper',[]]);
        $houseId = $this->createElement('hidden', 'house')
            ->setDecorators(['ViewHelper',[]]);
        $entranceId = $this->createElement('hidden', 'entrance')
            ->setDecorators(['ViewHelper',[]]);
        $floorId = $this->createElement('hidden', 'floor')
            ->setDecorators(['ViewHelper',[]]);
        $flatId = $this->createElement('hidden', 'flat')
            ->setDecorators(['ViewHelper',[]]);

        $this->addElement($complexId);
        $this->addElement($houseId);
        $this->addElement($entranceId);
        $this->addElement($floorId);
        $this->addElement($flatId);

        $submit = $this->createElement('note','send')
            ->setValue('Подобрать')
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'       => 'a',
                        'class'     => 'js-submit btn blue waves-effect',
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

        $this->addElement($clear);
        $this->addDisplayGroup([
            $price,
            $budget,
        ], 'row1', [
            'decorators' => [
                'FormElements',
                [
                    'htmlTag',
                    [
                        'tag' => 'div',
                        'class' => 'row'
                    ]
                ]
            ]
        ]);
        $this->addDisplayGroup([
            $primaryColor,
            $style,
        ], 'row2', [
            'decorators' => [
                'FormElements',
                [
                    'htmlTag',
                    [
                        'tag' => 'div',
                        'class' => 'row'
                    ]
                ]
            ]
        ]);

        $this->addElement($submit);
    }

    public function populate(array $values) {
        if (!empty($values["price"])) {
            $values["price"] = implode(":", $values["price"]);
        }

        if (!empty($values["budget"])) {
            $values["budget"] = implode(":", $values["budget"]);
        }

        return $this->setDefaults($values);
    }
}
