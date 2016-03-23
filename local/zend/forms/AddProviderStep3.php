<?
/**
 * Class Sibirix_Form_AddDesignStep3
 */
class Sibirix_Form_AddProviderStep3 extends Sibirix_Form_Default {

    protected $_roomList;
    protected $_designId;

    public function init() {
        parent::init();

        $this->setAttribs(['class' => 'js-add-design-step3-form']);
        $this->setAction($this->getView()->url());
        $this->setMethod('post');
        $this->setDecorators([
            'FormElements',
            ['Form', []]
        ]);

     
}