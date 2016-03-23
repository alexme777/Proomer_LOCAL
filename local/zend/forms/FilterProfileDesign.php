<?
/**
 * Class Sibirix_Form_FilterProfileDesign
 */
class Sibirix_Form_FilterProfileDesign extends Sibirix_Form_Default {

    public function init() {
        parent::init();

        $populateValues = [];
        $status = EnumUtils::getPropertyValuesList(IB_DESIGN, 'STATUS');

        $statusArr = [0 => 'Бесплатный'];
        foreach ($status as $item) {
            if ($item['ID'] != DESIGN_STATUS_DELETED) {
                if ($item['ID'] == DESIGN_STATUS_ERROR) {
                    $statusArr[$item['ID']] = 'Доработка';
                } else {
                    $statusArr[$item['ID']] = $item['VALUE'];
                }
            }
        }

        $populateValues['status'] = $statusArr;

        $this->setAttribs(['class' => 'js-filter-form']);
        $this->setAction($this->getView()->url());
        $this->setMethod('post');
        $this->setDecorators([
            'FormElements',
            ['Form', []]
        ]);

        $clear = $this->createElement('note', 'clear')
            ->setValue('<a class="js-clear-filter js-tooltip form-clear" data-description="Сбросить фильтр"></a>')
            ->setDecorators([
                'ViewHelper',
                []
            ]);

        $status = $this->createElement('MultiCheckbox', 'status')
            ->setRequired(true)
            ->setMultiOptions($populateValues['status'])
            ->setLabel('Статус проекта')
            ->setAttribs(['wrapClass' => 'tag', 'type' => 'checkbox'])
            ->setDecorators([
                'ViewHelper',
                'Label',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'input-cell input-cell-2 input-tags'
                    ]
                ]
            ]);

        $status->helper = 'customRadio';

        $designModel = new Sibirix_Model_Design();

        $price = new Sibirix_Form_Element_RangeSlider('price');
        $price->setLabel('Цена')
            ->setAttribs($designModel->getExtremePrice(Sibirix_Model_User::getId()))
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

        $primaryColor = $this->createElement('Multiselect','primaryColor')
            ->setAttribs(['class' => 'js-select'])
            ->setMultiOptions(Sibirix_Model_Reference::getReference(HL_PRIMARY_COLORS, ["UF_NAME"], "UF_XML_ID"))
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
                        'class' => 'input-cell input-cell-50p js-form-block'
                    ]
                ]
            ]);

        $primaryColor->helper = 'customMultiSelect';



        $style = $this->createElement('Multiselect','style')
            ->setAttribs(['class' => 'js-select'])
            ->setMultiOptions(Sibirix_Model_Reference::getReference(HL_STYLE, array("UF_NAME"), "UF_XML_ID"))
            ->setFilters(['StringTrim', 'StripTags'])
            ->setAttribs(["placeholder" => 'Выберите названия стилей',
                          'wrapClass3' => 'values',
                          'label' => 'Стиль',
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
                        'class' => 'input-cell input-cell-50p js-select-row js-form-block'
                    ]
                ]
            ]);

        $style->helper = 'customMultiSelect';

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
                        'class'     => 'btn-wrapper',
                    ]
                ]
            ]);

        $this->addElement($clear);

        $this->addDisplayGroup([
            $price,
            $status
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

        return $this->setDefaults($values);
    }
}
