<?
/**
 * Class RoomController
 */
class RoomController extends Sibirix_Controller {

    /**
     * @var Sibirix_Model_Room
     */
    protected $_model;

    public function init() {
        $this->_model  = new Sibirix_Model_Room();
    }
    /**
     * Добавление комнаты к дизайну
     */
    public function addRoomAction() {
        $designId = $this->getParam("designId");
        $designModel = new Sibirix_Model_Design();
        if (!$designModel->checkDesignAccess($designId)) {
            Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'not-found-page');
            throw new Zend_Exception('Not found', 404);
        }

        $step2Form = new Sibirix_Form_AddDesignStep2();
        $step2Form->populate($this->getAllParams());
        $validValues = $step2Form->getValues();

        $addResult = $this->_model->addRoom($designId, $validValues["addRoomName"], $validValues["addRoomSquare"]);

        $this->_helper->json(['result' => (bool)$addResult, 'newId' => $addResult]);
    }

    /**
     * Удаление комнаты к дизайну
     */
    public function deleteRoomAction() {
        $roomId = $this->getParam("roomId");

        if (!$this->_model->checkRoomAccess($roomId)) {
            Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'not-found-page');
            throw new Zend_Exception('Not found', 404);
        }

        $delResult = $this->_model->deleteRoom($roomId);

        $this->_helper->json(['result' => (bool)$delResult]);
    }

    /**
     * Получаение html с формой на 3 шаге с актуальными комнатами
     */
    public function roomFormAction() {
        $designId    = $this->getParam("designId");
        if (!($designId > 0)) return $this;

        $designModel = new Sibirix_Model_Design();

        if (!$designModel->checkDesignAccess($designId)) {
            Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'not-found-page');
            throw new Zend_Exception('Not found', 404);
        }

        $roomPhotoData = array();
        $roomPhotoData["planImage"] = array();

        //Получаем план квартиры
        $planFlat = $designModel->getPlanImage($designId);

        foreach ($planFlat as $key => $plan) {
            if (!empty($plan)) {
                $roomPhotoData["planImage"][$key] = Resizer::resizeImage($plan, "DROPZONE_ROOMS_PHOTO");
            }
        }

        //Получаем комнаты
        $roomList = $this->_model->cache(0)->select(["ID", "NAME", "PROPERTY_IMAGES"], true)->where(["PROPERTY_DESIGN" => $designId])->getElements();
        $roomListForm  = array();

        if (!empty($roomList)) {
            foreach ($roomList as $room) {
                $roomListForm[$room->ID] = $room->NAME;
            }

            foreach ($roomList as $roomPhoto) {
                foreach ($roomPhoto->PROPERTY_IMAGES_VALUE as $key => $image) {
                    $roomPhotoData["room".$roomPhoto->ID][$roomPhoto->PROPERTY_IMAGES_PROPERTY_VALUE_ID[$key]] = Resizer::resizeImage($image, "DROPZONE_ROOMS_PHOTO");
                }
            }
        }

        $step3Form = new Sibirix_Form_AddDesignStep3(['roomList' => $roomListForm, 'designId' => $designId]);
        $step3Form->populate($roomPhotoData);

        $this->view->step3Form = $step3Form;
        $this->_response->stopBitrix(true);
    }

    /**
     * Загрузка изображений комнаты
     */
    public function uploadImageAction() {
        $upload = new Zend_File_Transfer();
        $roomId = $this->getParam("roomId");
        $fileModel = new Sibirix_Model_Files();

        if (!$this->_model->checkRoomAccess($roomId)) {
            Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'not-found-page');
            throw new Zend_Exception('Not found', 404);
        }

        $imageFile = $upload->getFileInfo()["file"];

        if (!$fileModel->checkFile($upload->getFileInfo()["file"], "jpg,png,jpeg,gif,bmp")) {
            $this->_helper->json(['result' => false, "response" => "Invalid file type"]);
        }
        $resultImages = $this->_model->addImage($roomId, $imageFile);

        $this->_helper->json(['result' => true, 'response' => $resultImages]);
    }

    /**
     * Удаление изображения комнаты
     */
    public function deleteImageAction() {
        $roomId = $this->getParam("roomId");
        $imageId = $this->getParam("imageId");

        if (!$this->_model->checkRoomAccess($roomId)) {
            Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-class', 'not-found-page');
            throw new Zend_Exception('Not found', 404);
        }

        $resultImages = $this->_model->deleteImageItem($roomId, $imageId);

        $this->_helper->json(['result' => true, 'response' => $resultImages]);
    }
}
