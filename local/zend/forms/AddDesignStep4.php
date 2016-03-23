<?
/**
 * Class Sibirix_Form_AddDesignStep4
 */
class Sibirix_Form_AddDesignStep4 extends Sibirix_Form_Default {

    public function init() {
        parent::init();

        $this->setAttribs(['class' => 'js-add-design-step4-form']);
        $this->setAction($this->getView()->url());
        $this->setMethod('post');
        $this->setDecorators([
            'FormElements',
            ['Form', []]
        ]);

        $docs = new Sibirix_Form_Element_Dropzone('docs');
        $docs->setDescription(Settings::getOption("DOCS_UPLOAD_DESCRIPTION"))
            ->setAttribs(["classZone" => "js-docs-dropzone inline", "files" => true, 'typeTitle' => 'документы'])
            ->setDecorators([
                'ViewHelper',
                [
                    'description',
                    [
                        'tag'   => 'div',
                        'class' => 'info',
                        'placement' => 'prepend'
                    ]
                ],
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'input-row'
                    ]
                ],
            ]);

        $finishStep = $this->createElement("note", "nextAddStep")
            ->setValue("Добавить проект")
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'       => 'a',
                        'class'     => 'js-finish-step btn blue waves-effect',
                        'data-step' => 4,
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
        $this->addElement($docs);
        $this->addElement($finishStep);

    }
}