<?
/**
 * Class Sibirix_Form_SearchService_Step2
 */
class Sibirix_Form_SearchService_Step2 extends Sibirix_Form_Default {

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
                    'class' => 'form-content step2'
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

        $house = $this->createElement('select', 'house')
            ->setLabel("Улица, дом/корпус")
            ->setAttrib("class", "js-select js-house-select")
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

        $entrance = $this->createElement('select', 'entrance')
            ->setLabel("Подъезд")
            ->setAttrib("class", "js-select js-entrance-select")
            ->setMultiOptions(["" => "Любой"])
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

        $floor = $this->createElement('select', 'floor')
            ->setLabel("Этаж")
            ->setAttrib("class", "js-select js-floor-select")
            ->setMultiOptions(["" => "Любой"])
            ->setDecorators([
                'ViewHelper',
                'Label',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'input-row js-floor-row'
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
            ->setValue("Следующий этап")
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'a',
                        'class' => 'js-next-step btn blue waves-effect',
                        'href'  => "javascript:void(0);",
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

        $this->addElement($house);
        $this->addDisplayGroup([
            $entrance,
            $floor,
        ], 'entranceAndFloor', [
            'decorators' => [
                'FormElements',
                [
                    'htmlTag',
                    [
                        'tag' => 'div',
                        'class' => 'js-entrance-floor'
                    ]
                ]
            ]
        ]);
        $this->addDisplayGroup([
            $nextStep,
            $showProject,
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
    }
}