<?
/**
 * Class Sibirix_Form_SearchService_Step1
 */
class Sibirix_Form_SearchService_Step1 extends Sibirix_Form_Default {

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
                    'class' => 'form-content step1',
                ]
            ]
        ]);

        $complexName = $this->createElement('text','complexName')
            ->setAttribs(['class' => 'js-complex-name', 'maxlength' => 100, 'placeholder' => 'Укажите жилой комплекс или улицу'])
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
                ],
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'input-row'
                    ]
                ]
            ]);

        $note = $this->createElement("note", "subTitle")
            ->setValue('Или выберите из списка')
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'js-form-label form-label'
                    ]
                ]
            ]);

        $complexModel = new Sibirix_Model_Complex();
        $complexListDefault = $complexModel->select(array("ID", "NAME", "PROPERTY_CITY"), true)
            ->where(array(
                "!PROPERTY_SHOW_STEP_1" => false,
                "PROPERTY_CITY"         => Sibirix_Model_User::getUserLocation()
            ))->limit(24)
            ->getElements();

        $complexListDefault = EHelper::prepareForForm($complexListDefault, "none");

        $complexList = $this->createElement('Radio','complexId')
            ->setMultiOptions($complexListDefault)
            ->setSeparator('')
            ->setDecorators([
                'ViewHelper',
                [["wrap1" => "htmlTag"], ["tag" => "div", "class" => "js-ajax-content row"]],
                [["wrap2" => "htmlTag"], ["tag" => "div", "class" => "js-scroller js-complex-list complex-list"]]
            ]);
        $complexList->helper = "complexChecker";

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

        $this->addElement($complexName);
        $this->addElement($note);
        $this->addElement($complexList);
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
    }
}