<?

/**
 * Class FloorController
 */
class FloorController extends Sibirix_Controller {
    /**
     * @var Sibirix_Model_Floor
     */
    protected $_model;

    public function init() {
        $this->_model = new Sibirix_Model_Floor();
    }

    public function copyAction() {
        $floorId = $this->getParam("elementId", 0);

        if (!Sibirix_Model_User::isAdmin()) {
            $this->_helper->json(['success' => false, 'result' => 'У вас недостаточно прав на копирование']);
        }

        $floor = $this->_model->where(["ID" => $floorId])->getElement();
        if (!$floor) {
            $this->_helper->json(['success' => false, 'result' => 'Элемент не найден']);
        }

        $newFloorId = $this->_model->copyElement($floor);

        $this->_helper->json(['success' => true, 'id' => $newFloorId]);
    }
}