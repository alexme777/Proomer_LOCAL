<?
/**
 * Class Sibirix_Form_SearchService_StepPlan
 */
class Sibirix_Form_SearchService_StepPlan extends Sibirix_Form_Default {

    public function init() {
        parent::init();

        $this->setAttribs(['class' => 'js-search-service-plan-form']);
        $this->setAction($this->getView()->url());
        $this->setMethod('get');
        $this->setDecorators([
            'FormElements',
            ['Form', []],
            []
        ]);

        $planName = $this->createElement('text','planName')
            ->setAttribs(['class' => 'js-plan-name', 'maxlength' => 100, 'placeholder' => 'Название планировки'])
            ->setRequired(true)
            ->setValidators([
                ['StringLength', false, ['max' => 100, 'encoding' => 'utf-8',]],
            ])
            ->setFilters(['StringTrim', 'StripTags'])
            ->setDecorators([
                'ViewHelper',
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

        $planModel = new Sibirix_Model_Plan();
        $planListDefault = $planModel->select(array("ID", "NAME"), true)
            ->where(array(
                "!PROPERTY_SHOW_STEP_1" => false,
            ))
            ->limit(40)
            ->getElements();

        $planListDefault = EHelper::prepareForForm($planListDefault, "none");

        $planList = $this->createElement('Radio','planId')
            ->setMultiOptions($planListDefault)
            ->setSeparator('')
            ->setDecorators([
                'ViewHelper',
                [["wrap1" => "htmlTag"], ["tag" => "div", "class" => "js-ajax-content row"]],
                [["wrap2" => "htmlTag"], ["tag" => "div", "class" => "radio-list"]],
                [["wrap3" => "htmlTag"], ["tag" => "div", "class" => "js-scroller js-plan-list", "style" => "height: 300px"]]
            ]);
        $planList->helper = "complexChecker";

        $nextAddStep = $this->createElement("note", "nextAddStep")
            ->setValue("Следующий шаг")
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'       => 'a',
                        'class'     => 'js-next-add-step btn blue waves-effect',
                        'data-step' => 1,
                        'href'      => "javascript:void(0);",
                    ]
                ]
            ]);

        $this->addElement($planName);
        $this->addElement($note);
        $this->addElement($planList);
        $this->addDisplayGroup([
            $nextAddStep,
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