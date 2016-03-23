<?

/**
 * Контроллер страницы контактов
 * Class ContactsController
 */
class TestController extends Sibirix_Controller {

    public function indexAction() {
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('page-type', 'contacts');
        Zend_Registry::get('BX_APPLICATION')->SetTitle('Контакты');

        \Bitrix\Main\Page\Asset::getInstance()->addJs(GOOGLE_MAPS);

        EHelper::jsAppAdditional([
            "pinCoords" => Settings::getOption("pinCoords", CONTACT_MAP_PIN),
            "centerCoords" => Settings::getOption("centerCoords", CONTACT_MAP_CENTER)
        ]);

        $cacheTime = 60 * 60 * 24;
        $obCache = new CPHPCache();
        if ($obCache->InitCache($cacheTime, [], '/contacts/')) {
            $vars = $obCache->GetVars();
            $contacts = $vars;
        } else {
            $sections = IBlockSectionSelect::instance(IB_SETTINGS)
                ->where(array('CODE' => 'REQUISITES'))
                ->fetch();
            $section = $sections[0];

            $contacts['requisites'] = IBlockWrap::instance(IB_SETTINGS)
                ->cache(0)
                ->orderBy(array('SORT' => 'ASC'))
                ->select(array('CODE', 'PROPERTY_VALUE'))
                ->where(array('SECTION_ID' => $section['ID']))
                ->fetch();
			
            $contacts['address'] = Settings::getOption('address');
            $contacts['email'] = Settings::getOption('email');
            $contacts['phone'] = Settings::getOption('phone');

            if ($obCache->StartDataCache()) {
                $obCache->EndDataCache($contacts);
            }
        }

        $this->view->assign($contacts);
    }
}