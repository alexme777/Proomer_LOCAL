<?

/**
 * Class EntranceController
 */
class EntranceController extends Sibirix_Controller {
    /**
     * @var Sibirix_Model_Entrance
     */
    protected $_model;

    public function init() {
        $this->_model = new Sibirix_Model_Entrance();
    }

    public function copyAction() {
        $entranceId = $this->getParam("elementId", 0);

        if (!Sibirix_Model_User::isAdmin()) {
            $this->_helper->json(['success' => false, 'result' => 'У вас недостаточно прав на копирование']);
        }

        $entrance = $this->_model->where(["ID" => $entranceId])->getElement();
        if (!$entrance) {
            $this->_helper->json(['success' => false, 'result' => 'Элемент не найден']);
        }

        $newEntranceId = $this->_model->copyElement($entrance);

        $this->_helper->json(['success' => true, 'id' => $newEntranceId]);
    }
}