<?
/**
 * Class Sibirix_Form_AddDesignStep3
 */
class Sibirix_Form_AddDesignStep3 extends Sibirix_Form_Default {

    protected $_roomList;
    protected $_designId;

    public function init() {
        parent::init();
		\Bitrix\Main\Page\Asset::getInstance()->addJs(P_JS . 'libs/jquery.js');
		\Bitrix\Main\Page\Asset::getInstance()->addJs(P_JS . 'libs/jquery.fancybox.js');
		\Bitrix\Main\Page\Asset::getInstance()->addJs(P_JS . 'admin/slider-pins-design-setter.js');

        $this->setAttribs(['class' => 'js-add-design-step3-form']);
        $this->setAction($this->getView()->url());
        $this->setMethod('post');
        $this->setDecorators([
            'FormElements',
            ['Form', []]
        ]);

        $planImage = new Sibirix_Form_Element_Dropzone('planImage');
        $planImage->setLabel('Планировка проекта')
            ->setAttribs(["classZone" => "js-plan-dropzone js-photo-dropzone plan-dropzone inline", "elementId" => $this->_designId])
            ->setDecorators([
                'ViewHelper',
                [
                    ['tooltip' => 'htmlTag'],
                    [
                        'tag'              => 'span',
                        'class'            => 'help js-tooltip mainTooltip',
                        'data-description' => Settings::getOption("TOOLTIP_PLAN_DESIGN"),
                        'placement'        => 'prepend'
                    ]
                ],
                'Label',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'   => 'div',
                        'class' => 'input-row'
                    ]
                ],
            ]);
        $this->addElement($planImage);

        $nextAddStep = $this->createElement("note", "nextAddStep")
            ->setValue("Следующий шаг")
            ->setDecorators([
                'ViewHelper',
                [
                    ['wrap1' => 'htmlTag'],
                    [
                        'tag'       => 'a',
                        'class'     => 'js-next-add-step btn blue waves-effect',
                        'data-step' => 3,
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

        $roomFormList = array();
        if (!empty($this->_roomList)) {
            foreach ($this->_roomList as $roomId => $room) {
                $roomDropzone = new Sibirix_Form_Element_Dropzone('room' . $roomId);
                $roomDropzone->setLabel($room)
                    ->setAttribs(["classZone" => "js-room-photo-dropzone inline js-photo-dropzone", "elementId" => $roomId])
                    ->setDecorators([
                        'ViewHelper',
                        'Label',
                        [
                            ['wrap1' => 'htmlTag'],
                            [
                                'tag'   => 'div',
                                'class' => 'input-row'
                            ]
                        ],
                    ]);

                $roomFormList[] = $roomDropzone;
            }
            $this->addDisplayGroup($roomFormList, 'roomListDropzone', [
                'decorators' => [
                    'FormElements',
                    [
                        ['wrap1' => 'htmlTag'],
                        [
                            'tag' => 'div',
                            'class' => 'js-room-list-form'
                        ]
                    ]
                ]
            ]);
        }

        $this->addElement($nextAddStep);
    }

    public function setRoomList($roomList) {
        if (is_array($roomList) && !empty($roomList)) {
            $this->_roomList = $roomList;
        } else {
            $this->_roomList = array();
        }

        return $this;
    }

    public function setDesignId($designId) {
        if (is_array($designId) && !empty($designId)) {
            $this->_designId = $designId;
        } else {
            $this->_designId = 0;
        }

        return $this;
    }
}