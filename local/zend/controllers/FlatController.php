<?

/**
 * Class FlatController
 */
class FlatController extends Sibirix_Controller {
    /**
     * @var Sibirix_Model_Flat
     */
    protected $_model;

    public function init() {
        $this->_model = new Sibirix_Model_Flat();
    }

    public function copyAction() {
        $flatId = $this->getParam("elementId", 0);

        if (!Sibirix_Model_User::isAdmin()) {
            $this->_helper->json(['success' => false, 'result' => 'У вас недостаточно прав на копирование']);
        }

        $flat = $this->_model->where(["ID" => $flatId])->getElement();
        if (!$flat) {
            $this->_helper->json(['success' => false, 'result' => 'Элемент не найден']);
        }

        $newFlatId = $this->_model->copyElement($flat);

        $this->_helper->json(['success' => true, 'id' => $newFlatId]);
    }
}