<?

/**
 * Class Zend_View_Helper_MenuButtons
 */
class Zend_View_Helper_MenuButtons extends Zend_View_Helper_Abstract {

    public function menuButtons() {
        $userModel = new Sibirix_Model_User();
        $user = $userModel->getCurrent();

        if ($user) {
            $userModel->getImageData($user, 'PERSONAL_PHOTO');
            $type = $user->getTextType()['class'];

            $modelMenu = new Sibirix_Model_MainMenu();
            $menu = $modelMenu->where(['SECTION_CODE' => $type])->getElements();
        }

        $basketModel = new Sibirix_Model_Basket();
        $basketCount = $basketModel->getBasketCount();


        return $this->view->partial('_partials/header/menu-buttons.phtml', array("user" => $user, "menu" => $menu, "basketCnt" => $basketCount));
    }
}