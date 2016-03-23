<?

/**
 * Class Sibirix_Form_ProfileType
 */
class Sibirix_Form_ProfileType extends Sibirix_Form_Default {

    /**
     *
     */
    public function init() {
        parent::init();

        $this->setMethod('post');
        $this->setAttribs(['class' => 'js-type-form']);
        $this->setAction($this->getView()->url([], 'profile-type'));
        $this->setDecorators([
            'FormElements',
            ['Form', []],
        ]);

        $title = $this->createElement('note', 'title')
            ->setValue('Выберите тип профиля')
            ->setDecorators([
                'ViewHelper',
                [
                    ['formTitle' => 'htmlTag'],
                    [
                        'tag'   => 'h2'
                    ]
                ]
            ]);

        $type = $this->createElement('radio', 'profileType')
            ->setRequired(true)
            ->setMultiOptions([CLIENT_TYPE_ID =>"Клиент", DESIGNER_TYPE_ID => "Дизайнер", PROVIDER_TYPE_ID => "Поставщик", SELLER_TYPE_ID => "Продавец"])
            ->setValue(CLIENT_TYPE_ID)
            ->setLabel('Ваш статус:')
            ->setDecorators([
                'ViewHelper',
                'Label',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'popup-title form'
                    ]
                ]
            ]);

        $type->helper = 'customRadio';


        $submit = $this->createElement('note','submit')
            ->setValue('Сохранить')
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'       => 'a',
                        'class'     => 'btn blue waves-effect',
                    ]
                ],
                [
                    ['wrap2' => 'htmlTag'],
                    [
                        'tag'       => 'div',
                        'class'     => 'form-content',
                    ]
                ]
            ]);

        $this->addElement($title);
        $this->addElement($type);
        $this->addElement($submit);
    }
}
