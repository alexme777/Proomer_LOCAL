<?
/**
 * Class Sibirix_Form_SearchService_Step3
 */
class Sibirix_Form_SearchService_Step3 extends Sibirix_Form_Default {

    public function init() {
        parent::init();

        $this->setAttribs(['class' => 'js-search-service-form']);
        $this->setAction($this->getView()->url([], 'design-list'));
        $this->setMethod('get');
        $this->setDecorators([
            'FormElements',
            ['Form', []],
            [
                ['wrap1' => 'htmlTag'],
                [
                    'tag'   => 'div',
                    'class' => 'form-content step3'
                ]
            ]
        ]);

        $caption = $this->createElement('note', 'caption')
            ->setValue("жилой комплекс")
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'title-label',
                    ]
                ]
            ]);

        $complexName = $this->createElement('note', 'complexName')
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'title',
                    ]
                ]
            ]);

        $complexId = $this->createElement('hidden', 'complexId')
            ->setDecorators([
                'ViewHelper',
                []
            ]);

        $houseId = $this->createElement('hidden', 'house')
            ->setDecorators([
                'ViewHelper',
                []
            ]);

        $entranceId = $this->createElement('hidden', 'entrance')
            ->setDecorators([
                'ViewHelper',
                []
            ]);

        $floorId = $this->createElement('hidden', 'floor')
            ->setDecorators([
                'ViewHelper',
                []
            ]);

        $house = $this->createElement('note', 'houseAddress')
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                    ]
                ]
            ]);

        $entrance = $this->createElement('note', 'entranceInfo')
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                    ]
                ]
            ]);

        $floor = $this->createElement('note', 'floorInfo')
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                    ]
                ]
            ]);

        $flat = $this->createElement('select', 'flat')
            ->setLabel("Квартира")
            ->setAttrib("class", "js-select js-flat-select")
            ->setMultiOptions(["Любая"])
            ->setDecorators([
                'ViewHelper',
                'Label',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'input-row'
                    ]
                ]
            ]);

        $square = $this->createElement('note', 'square')
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'    => 'div',
                        'class'  => 'js-flat-square square',
                        'escape' => false,
                    ]
                ]
            ]);

        $showProject = $this->createElement("note", "showProjectBtn")
            ->setValue("Смотреть проекты")
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'a',
                        'class' => 'js-show-project show-project-btn btn btn-transparent waves-effect',
                        'href'  => "javascript:void(0);",
                    ]
                ]
            ]);

        $nextStep = $this->createElement("note", "showNextStep")
            ->setValue("Следующий шаг")
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'       => 'a',
                        'class'     => 'js-next-add-step next-add-step-btn btn blue waves-effect',
                        'data-step' => 1,
                        'href'      => "javascript:void(0);",
                    ]
                ]
            ]);


        $this->addDisplayGroup([
            $caption,
            $complexName,
        ], 'complexCaption', [
            'decorators' => [
                'FormElements',
                [
                    'htmlTag',
                    [
                        'tag' => 'div',
                        'class' => 'complex-caption'
                    ]
                ]
            ]
        ]);

        $this->addDisplayGroup([
            $house,
            $entrance,
            $floor,
        ], 'houseInfo', [
            'decorators' => [
                'FormElements',
                [
                    'htmlTag',
                    [
                        'tag' => 'div',
                        'class' => 'values'
                    ]
                ]
            ]
        ]);
        $this->addElement($flat);
        $this->addElement($square);
        $this->addDisplayGroup([
            $showProject,
            $nextStep,
        ], 'buttons', [
            'decorators' => [
                'FormElements',
                [
                    'htmlTag',
                    [
                        'tag' => 'div',
                        'class' => 'btn-wrapper'
                    ]
                ]
            ]
        ]);

        $this->addElement($complexId);
        $this->addElement($houseId);
        $this->addElement($entranceId);
        $this->addElement($floorId);
    }
}