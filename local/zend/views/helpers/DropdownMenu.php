<?

/**
 * Class Zend_View_Helper_DropdownMenu
 */
class Zend_View_Helper_DropdownMenu extends Zend_View_Helper_Abstract {

    public function dropdownMenu() {
        $mainMenuModel = new Sibirix_Model_MainMenu();
        $menu = $mainMenuModel->where(['SECTION_CODE' => 'dropdown'])->getElements();

        return $this->view->partial('_partials/header/dropdown-menu.phtml', array("menu" => $menu));
    }
}