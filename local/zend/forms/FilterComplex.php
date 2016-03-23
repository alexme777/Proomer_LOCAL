<?
/**
 * Class Sibirix_Form_FilterComplex
 */
class Sibirix_Form_FilterComplex extends Sibirix_Form_Default {

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

        $search = $this->createElement('text','search')
            ->setLabel('Поиск')
            ->setAttribs(['maxlength' => 100, 'placeholder' => 'Название жилого комплекса или адрес...'])
            ->setValidators([
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
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'input-cell search'
                    ]
                ]
            ]);

        $complexModel = new Sibirix_Model_Complex();

        $avgPrice = new Sibirix_Form_Element_RangeSlider('avgPrice');

        $avgPrice->setLabel('Средняя цена дизайна')
            ->setAttribs($complexModel->getExtremeAvgPrice())
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


        $popular = $this->createElement('select','popular')
            ->setLabel('Популярность')
            ->setAttribs(['class' => 'js-select'])
            ->setMultiOptions([
                ""       => "Любая",
                "-10"    => "Менее 10 проектов",
                "10-50"  => "От 10 до 50 проектов",
                "50-500" => "От 50 до 500 проектов",
                "1000-"  => "Более 1000 проектов"
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
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'input-cell'
                    ]
                ]
            ]);

        $complexSize = $this->createElement('select','complexSize')
            ->setLabel('Размер комплекса')
            ->setAttribs(['class' => 'js-select'])
            ->setMultiOptions([
                ""    => "Любой",
                "-5"  => "Небольшой комплекс",
                "5-9" => "Средний комплекс",
                "10-" => "Крупный комплекс",
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
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'input-cell js-select-row'
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
            $search,
            $avgPrice,
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
            $popular,
            $complexSize,
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
        if (!empty($values["avgPrice"])) {
            $values["avgPrice"] = implode(":", $values["avgPrice"]);
        }

        return $this->setDefaults($values);
    }
}
