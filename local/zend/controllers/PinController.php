<?

/**
 * Class PinController
 */
class PinController extends Sibirix_Controller {

    public function getImgAction() {
        $ibType = $this->getParam("type");

        switch ($ibType) {
            case "complex":
                $model = new Sibirix_Model_Complex();
                $property = "PROPERTY_COMPLEX_PLAN";
                break;
            case "floor":
                $model = new Sibirix_Model_Floor();
                $property = "PROPERTY_FLOOR_PLAN";
                break;
        }

        if (empty($model) || empty($property)) {
            return false;
        }

        $elementId = $this->getParam("id");
	
        if (is_numeric($elementId) && $elementId > 0) {
            $item = $model->select(["ID", $property], true)->getElement($this->getParam("id"));
        }

        if (!empty($item)) {
            $model->getImageData($item, $property."_VALUE");
            $propertyValue = $property . "_VALUE";
            $this->view->imageUrl = Resizer::resizeImage($item->property, 'SEARCH_SERVICE_PLAN');
			
        }
        $this->_response->stopBitrix(true);
    }
	
	public function getImgsAction() {
        $ibType = $this->getParam("type");
		$indx = $this->getParam("indx");
	
        switch ($ibType) {
            case "complex":
                $model = new Sibirix_Model_Complex();
                $property = "PROPERTY_COMPLEX_PLAN";
                break;
            case "floor":
                $model = new Sibirix_Model_Floor();
                $property = "PROPERTY_FLOOR_PLAN";
                break;
			case "room":
                $model = new Sibirix_Model_Room();
                $property = "PROPERTY_IMAGES";
			  // $property = "PROPERTY_ROOM_PLAN";
                break;
        }

        if (empty($model) || empty($property)) {
            return false;
        }

        $elementId = $this->getParam("id");
        if (is_numeric($elementId) && $elementId > 0) {
            $item = $model->select(["ID", $property], true)->getElement($this->getParam("id"));
        }

        if (!empty($item)) {
            $model->getImageData($item, $property."_VALUE");
            $propertyValue = $property . "_VALUE";
            $this->view->imageUrl = Resizer::resizeImage($item->PROPERTY_IMAGES_VALUE[$indx], 'SEARCH_SERVICE_PLAN');
			
        }
        $this->_response->stopBitrix(true);
    }
	
	public function getImgspAction() {
        $ibType = $this->getParam("type");
		
        switch ($ibType) {
            case "complex":
                $model = new Sibirix_Model_Complex();
                $property = "PROPERTY_COMPLEX_PLAN";
                break;
            case "floor":
                $model = new Sibirix_Model_Floor();
                $property = "PROPERTY_FLOOR_PLAN";
                break;
			case "room":
                $model = new Sibirix_Model_Room();
                $property = "PROPERTY_IMAGES";
			  // $property = "PROPERTY_ROOM_PLAN";
                break;
			case "plan":
                $model = new Sibirix_Model_Plan();
                $property = "PROPERTY_IMAGES";
			  // $property = "PROPERTY_ROOM_PLAN";
                break;
        }

        if (empty($model) || empty($property)) {
            return false;
        }

        $elementId = $this->getParam("id");

        if (is_numeric($elementId) && $elementId > 0) {
            $item = $model->select(["ID", $property], true)->getElement($this->getParam("id"));
        }

        if (!empty($item)) {
            $model->getImageData($item, $property."_VALUE");
            $propertyValue = $property . "_VALUE";
            $this->view->imageUrl = Resizer::resizeImage($item->PROPERTY_IMAGES_VALUE, 'SERVICE_PLAN');
			
        }
        $this->_response->stopBitrix(true);
    }
}