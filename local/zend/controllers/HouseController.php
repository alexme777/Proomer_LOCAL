<?

/**
 * Class HouseController
 */
class HouseController extends Sibirix_Controller {
    /**
     * @var Sibirix_Model_User
     */
    protected $_model;

    public function init() {
        $this->_model = new Sibirix_Model_House();
    }

    public function copyAction() {
        $houseId = $this->getParam("elementId", 0);

        if (!Sibirix_Model_User::isAdmin()) {
            $this->_helper->json(['success' => false, 'result' => 'У вас недостаточно прав на копирование']);
        }

        $house = $this->_model->where(["ID" => $houseId])->getElement();
        if (!$house) {
            $this->_helper->json(['success' => false, 'result' => 'Элемент не найден']);
        }

        $newHouseId = $this->_model->copyElement($house);

        $this->_helper->json(['success' => true, 'id' => $newHouseId]);
    }
}