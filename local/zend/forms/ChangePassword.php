<?
/**
 * Class Sibirix_Form_ChangePassword
 */
class Sibirix_Form_ChangePassword extends Sibirix_Form_Default {

    /**
     *
     */
    public function init() {
        $this->setAttribs(['class' => 'js-change-form', 'data-title' => Settings::getOption('changePassMessage'), 'data-text' => Settings::getOptionText('changePassMessage')]);
        $this->setAction($this->getView()->url(array(), 'user-password-change', true))
            ->setMethod('post');
        $this->setDecorators([
            'FormElements',
            ['Form', []],
        ]);

        $title = $this->createElement('note', 'title')
            ->setValue('Восстановление пароля')
            ->setDecorators([
                'ViewHelper',
                [
                    ['formTitle' => 'htmlTag'],
                    [
                        'tag'   => 'h2'
                    ]
                ]
            ]);

        $checkword = $this->createElement('hidden','checkword')
            ->setRequired(true)
            ->setValidators([
                ['NotEmpty',true,['messages' => [Zend_Validate_NotEmpty::INVALID => "Поле обязательно для заполнения!"]]],
            ])
            ->setDecorators([
                'ViewHelper',
                []
            ]);

        $login = $this->createElement('hidden','login')
            ->setRequired(true)
            ->setValidators([
                ['NotEmpty',true,['messages' => [Zend_Validate_NotEmpty::INVALID => "Поле обязательно для заполнения!"]]],
            ])
            ->setDecorators([
                'ViewHelper',
                []
            ]);

        $password = $this->createElement('password','password')
            ->setLabel('Новый пароль')
            ->setAttribs(['class' => 'required', 'autocomplete' => 'off', 'placeholder' => 'Введите новый пароль', 'minlength' => 6])
            ->setRequired(true)
            ->setValidators([
                ['NotEmpty',true,['messages' => [Zend_Validate_NotEmpty::INVALID => "Поле обязательно для заполнения!"]]],
            ])
            ->setDecorators([
                'ViewHelper',
                'Label',
                [
                    ['wrap2' => 'htmlTag'],
                    [
                        'tag'   => 'a',
                        'class' => 'show-password js-show-password',
                        'placement' => 'append',
                        'href' => 'javascript:void(0);',
                        'title' => 'Показать пароль'
                    ]
                ],
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
                ],
            ]);

        $submit = $this->createElement('note','submit')
            ->setValue('Сменить пароль')
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'       => 'a',
                        'class'     => 'btn blue waves-effect',
                    ]
                ]
            ]);

        $this->addElement($title);
        $this->addDisplayGroup([
                $checkword,
                $login,
                $password,
                $submit
            ], 'mainFields', ['decorators' => [
                'FormElements',
                ['htmlTag',['tag' => 'div', 'class' => 'form-content']]
            ]
        ]);
    }
}
