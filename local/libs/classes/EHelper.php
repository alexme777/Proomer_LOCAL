<?
/**
 * Class EHelper
 * Набор общих хелперов
 */
class EHelper {

    protected static $siteData;
    protected static $_jsApp = array();

    /**
     * Проверяет, находимся ли мы на главной странице
     * @return bool
     */
    public static function isMain() {
        return ($GLOBALS['APPLICATION']->GetCurPage(false) == '/');
    }

    /**
     * Проверяет, находимся ли мы на странице корзины
     * @return bool
     */
    public static function isBasket() {
        return ($GLOBALS['APPLICATION']->GetCurPage(false) == EZendManager::url([], 'basket'));
    }

    /**
     * форматирует цену, разбивает на разряды
     * @param int $price
     * @return string
     */
    public static function price($price) {
        return number_format((float)$price, 0, '.', ' ');
    }

    /**
     * Получает все настройки сайта
     */
    protected static function getData() {
        if (self::$siteData === null) {
            $cSite   = new CSite();
            $rsSites = CSite::GetByID($cSite->GetDefSite(SITE_ID));
            self::$siteData = $rsSites->Fetch();
        }
        return self::$siteData;
    }


    /**
     * Получает значение параметра сайта по ключу
     * @param string $key
     * @return string
     */
    public static function get($key) {
        $data = self::getData();
        if (isset($data[$key])) {
            return $data[$key];
        }

        switch ($key) {
            case 'HOST':
                return $_SERVER['HTTP_HOST'];
        }

        return null;
    }

    /**
     * JS константы для добавления в шапку
     * @return string
     */
    public static function jsApp() {
       
        $jsApp = array(
            'sessid' => bitrix_sessid(),
            'USER' => array(
                'IS_LOGGED' => $GLOBALS['USER']->IsAuthorized()
            )
        );

        $jsApp = self::jsAppAdditional($jsApp);
        return '<script type="text/javascript">var APP = ' . json_encode(array_merge_recursive(self::$_jsApp, $jsApp)) . "; </script>\n";
    }

    /**
     * @param array $array
     * @param bool $recursive
     */
    public static function extendJsApp(array $array, $recursive = false) {
        self::$_jsApp = call_user_func_array($recursive ? 'array_merge_recursive' : 'array_merge', array($array, self::$_jsApp));
    }


    /**
     * @param array $additional
     * @return array|mixed
     */
    public static function jsAppAdditional(array $additional = array()) {
        $jsApp = Zend_Registry::isRegistered('jsApp') ? Zend_Registry::get('jsApp') : array();
        if (!empty($additional)) {
            $jsApp = array_merge_recursive($jsApp, $additional);
            Zend_Registry::set('jsApp', $jsApp);
        }

        return $jsApp;
    }

    /**
     * Возвращает информацию о файле
     * @param int|array $fid ID файла, либо массив ID файлов
     * @return bool|array - данные информация о файле
     */
    public static function getFileData($fid) {
        if (!isset($fid)) return false;

        $cFile = new CFile();
        if (is_array($fid)) {
            $rsFile = $cFile->GetList(array(), array("@ID" => implode(",", $fid)));
        } else {
            $rsFile = $cFile->GetByID($fid);
        }

        $ret = array();

        while ($ifile = $rsFile->Fetch()) {
            if (array_key_exists("~src", $ifile)) {
                if($ifile["~src"]) {
                    $ifile["SRC"] = $ifile["~src"];
                } else {
                    $ifile["SRC"] = $cFile->GetFileSRC($ifile, false, false);
                }
            } else {
                $ifile["SRC"] = $cFile->GetFileSRC($ifile, false);
            }

            $ret[$ifile['ID']] = $ifile;
        }

        if (is_array($fid)) {
            return $ret;
        } else {
            return $ret[$fid];
        }
    }
    
    /**
     * Обернуть письмо в шаблон
     * @param $arFields
     * @param $dbMailResultArray
     */
    public static function wrapMailTemplate(&$arFields, &$dbMailResultArray) {
        if ($dbMailResultArray['BODY_TYPE'] != 'html') return;  // только для писем с типом HTML
        
        $isAlreadyWrapped = strpos(strToUpper($dbMailResultArray['MESSAGE']), '<!DOCTYPE') !== false;
        if ($isAlreadyWrapped) return;  // к письму уже примене шаблон в админке

        $template = file_get_contents(P_DR . P_LAYOUT . 'email-template.php');
        // Скопируем заголовок письма внутрь шаблона
        $template = str_replace('#EMAIL_TITLE#', $dbMailResultArray['SUBJECT'], $template);
        
        // Добавим домен к ссылкам на картинки
        $domain = 'http://' . COption::GetOptionString("main", "server_name", $GLOBALS["SERVER_NAME"]);
        $template = str_replace('/local/', $domain . '/local/', $template);

        // Обернем письмо половинками шаблона 
        $template = explode('#CONTENT#', $template);
        $header = $template[0];
        $footer = $template[1];
        $dbMailResultArray['MESSAGE'] = $header . $dbMailResultArray['MESSAGE'] . $footer;
    }


    public static function initScriptOrder() {
        if (static::IsJSOptimized()) {
            $list = array('common');

            $fake = '/bitrix/js/../../local/js/_fakemin.js';
            foreach ($list as $item) {
                \Bitrix\Main\Page\Asset::getInstance()->addJsKernelInfo("sibirix-$item", array($fake));
                \Bitrix\Main\Page\Asset::getInstance()->moveJs("sibirix-$item");
                \Bitrix\Main\Page\Asset::getInstance()->addJs($fake);
            }
        }
    }

    /**
     * @return bool
     */
    public static function isJsOptimized() {
        $option = new COption();
        return (!defined("ADMIN_SECTION") || ADMIN_SECTION !== true) && ($option->getOptionString('main', 'optimize_js_files', 'N') == 'Y');
    }

    /**
     * @param $src
     * @param $block
     */
    public static function addFootScript($src, $block = '') {
        static $orderInited = false;

        if (static::isJsOptimized()) {
            if (!$orderInited) {
                EHelper::initScriptOrder();
                $orderInited = true;
            }

            if ($block) $block = '-' . $block;
            $fakeSrc = '/bitrix/js/../..' . $src;
            \Bitrix\Main\Page\Asset::getInstance()->addJsKernelInfo("sibirix$block", array($fakeSrc));
            \Bitrix\Main\Page\Asset::getInstance()->moveJs("sibirix$block");
            \Bitrix\Main\Page\Asset::getInstance()->addJs($fakeSrc);
        } else {
            \Bitrix\Main\Page\Asset::getInstance()->addJs($src);
        }
    }


    /**
     *
     */
    public static function showFootScripts() {
        global $APPLICATION;
        $APPLICATION->ShowBodyScripts();
    }

    /**
     * Объявить константы ID инфоблоков
     */
    public static function defineIbConst() {
        if (!CModule::IncludeModule('iblock')) {
            return [];
        }

        $cIblock = new CIBlock();
        $res = $cIblock->GetList(['id' => 'asc'], ['CHECK_PERMISSIONS' => 'N']);

        $list = [];
        $iblocks = [];
        $maxCodeLength = 0;
        while ($arIblock = $res->Fetch()) {
            if (!empty($arIblock['CODE']) && !preg_match('/[^a-z0-9_]/iu', $arIblock['CODE'])) {
                $arIblock['CODE'] = preg_replace('/^IB_/', '', $arIblock['CODE']);
                $arIblock['CODE'] = 'IB_' . strtoupper($arIblock['CODE']);
                $maxCodeLength = mb_strlen($arIblock['CODE'] . $arIblock['ID']) > $maxCodeLength ? mb_strlen($arIblock['CODE'] . $arIblock['ID']) : $maxCodeLength;
                defined($arIblock['CODE']) || define($arIblock['CODE'], $arIblock['ID']);
                $iblocks[] = $arIblock;
            }
        }

        foreach ($iblocks as $arIblock) {
            $space = str_repeat(' ', $maxCodeLength - mb_strlen($arIblock['CODE'] . $arIblock['ID']));
            $list[] = "define(\"{$arIblock['CODE']}\", " . $space . $arIblock['ID'] . "); // " . $arIblock['NAME'];
        }

        return $list;
    }

    /**
     *
     */
    public static function defineConst() {
        $list = [' ' => "<?"];
        $list += self::defineIbConst();

        if (APPLICATION_ENV == 'development') {
            $cacheConst = P_APP_PATH . 'define.const.php';

            if (!file_exists($cacheConst)) {
                $h = fopen($cacheConst, 'w');
                fwrite($h, implode("\r\n", $list));
                fclose($h);
            }
        }
    }

    /**
     * Словоформы
     * @param $count
     * @param array $forms
     * @return string
     */
    public static function getWordForm($count, $forms = []) {
        $n100 = $count % 100;
        $n10  = $count % 10;
        if (($n100 > 10) && ($n100 < 21)) {
            return $forms[2];
        } else if ((!$n10) || ($n10 >= 5)) {
            return $forms[2];
        } else if ($n10 == 1) {
            return $forms[0];
        }
        return $forms[1];
    }

    /**
     * Вставка включаемой области
     * @param $partial
     * @throws Zend_Exception
     */
    public static function includeArea($partial) {
        $includeFile = P_PARTIALS . $partial . ".php";

        if(!file_exists(P_DR . $includeFile)) {
            $incFile = fopen(P_DR . $includeFile, "a");
            fclose($incFile);
        }

        Zend_Registry::get('BX_APPLICATION')
            ->IncludeComponent("bitrix:main.include", "", [
                "AREA_FILE_SHOW" => "file",
                "PATH"           => $includeFile,
            ], false);
    }

    /**
     * Returns Zend Paginator object
     *
     * @param      $totalCnt
     * @param      $curPage
     * @param      $itemsPerPage
     *
     * @return Zend_Paginator
     */
    public static function getPaginator($totalCnt, $curPage, $itemsPerPage) {
        $range = 3;
        $pages = [];
        for ($i = 0; $i < $totalCnt; $i++) {
            $pages[] = $i;
        }

        // paginator set
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('_partials/paginator.phtml');
        Zend_Paginator::setDefaultScrollingStyle('Sliding');

        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($pages));
        $paginator
            ->setCurrentPageNumber($curPage)
            ->setItemCountPerPage($itemsPerPage)
            ->setPageRange($range);

        return $paginator;
    }

    public static function replaceParamLink2($add = [], $remove = [], $search = false) {
        parse_str($_SERVER['QUERY_STRING'], $params);
        $params = array_diff_key($params, array_fill_keys($remove, 1));
        $params = array_merge($params, $add);

        if (!$search) {
            $search = EZendManager::url();
        }

        if (empty($params)) {
            return $search;
        }

        return $search . '?' . http_build_query($params);
    }

    /*
    * Вычисляет среднее значение массива
    * @param array $valueList
    * @param int $precision
    * @return float
    */
    public static function averageValue(Array $valueList, $precision = 0) {
        if (empty($valueList)) return 0;
        $average = round(array_sum($valueList) / count($valueList), $precision);

        return $average;
    }

    public static function showPageClass() {
        $pageType  = Zend_Registry::get('BX_APPLICATION')->GetPageProperty('page-type');
        $pageClass = Zend_Registry::get('BX_APPLICATION')->GetPageProperty('page-class');
	
        if (empty($pageClass)) {
            $pageClass = $pageType . '-page';
        }
		
        return $pageClass;
    }

    public static function prepareForForm($data, $firstVal = true, $field = "NAME", $js = false) {
        /** @var $item Sibirix_Model_House_Row **/
        if (!$js) {
            if ($firstVal === "none") {
                $result = array();
            } else {
                $result = array("" => ($firstVal ? "Любой" : "Любая"));
            }

            foreach ($data as $item) {
                if ($field == "ADDRESS") {
                    $result[$item->ID] = $item->getAddress();
                } else {
                    $result[$item->ID] = $item->$field;
                }
            }
        } else {
            if ($firstVal === "none") {
                $result = array();
            } else {
                $result[] = array(
                    "value" => "",
                    "html"  => ($firstVal ? "Любой" : "Любая")
                );
            }

            foreach ($data as $item) {
                if ($field == "ADDRESS") {
                    $result[] = array(
                        "value" => $item->ID,
                        "html"  => $item->getAddress()
                    );
                } else {
                    $result[] = array(
                        "value" => $item->ID,
                        "html"  => $item->$field
                    );
                }
            }
        }

        return $result;
    }
}
