<?

/**
 * Class Zend_View_Helper_ProfileSidebar
 */
class Zend_View_Helper_ProfileSidebar extends Zend_View_Helper_Abstract {

    public function profileSidebar() {
        $userModel    = new Sibirix_Model_User();
        $user = $userModel->getCurrent();

        if ($user) {
            $userModel->getImageData($user, 'PERSONAL_PHOTO');
            $type = $user->getTextType()['class'];

            $modelMenu = new Sibirix_Model_MainMenu();
            $menu = $modelMenu->where(['SECTION_CODE' => $type])->getElements();
			
        }

        return $this->view->partial('_partials/profile/sidebar.phtml', array("user" => $user, "menu" => $menu));
    }
}