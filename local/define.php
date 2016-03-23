<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @param $className
 * @return bool
 */
function customAutoload($className) {
    if (!CModule::RequireAutoloadClass($className)) {

        $path = P_CLASSES . $className . '.php';

        if (file_exists($path)) {
            require_once $path;
            return true;
        }
        return false;
    }
    return true;
}

// Режим работы "Разработка"
defined('APPLICATION_ENV') || define('APPLICATION_ENV', 'production');

// Здесь лежат все константы для путей к файлам
define("P_APP",       "/local/");
define("P_CSS",       P_APP . "css/");
define("P_JS",        P_APP . "js/");
define("P_IMAGES",    P_APP . "images/");
define("P_PARTIALS",  P_APP . "partials/");
define("P_LAYOUT",    P_APP . "layout/");
define("P_PICTURES",  P_APP . "pictures/");

define("P_AJAX",      P_APP . "ajax/");
define("P_UPLOAD",    "/" . COption::GetOptionString("main", "upload_dir", "upload") . "/");

define("P_DR",        $_SERVER["DOCUMENT_ROOT"]);
define("P_APP_PATH",  P_DR . P_APP);

define("P_INCLUDES",  P_APP_PATH . "includes/");
define("P_LIBRARY",   P_APP_PATH . "libs/");
define("P_CLASSES",   P_LIBRARY . "classes/");

define("P_LOG_DIR",   P_APP_PATH . "logs/");
define("P_LOG_FILE",  P_LOG_DIR . "app.log");


define('GOOGLE_MAPS', '//maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true');

// максимальное количество слайдов (если не задано в настройках)
define('MAX_SLIDES_COUNT', 5);
define('CONTACT_MAP_PIN', '55.322842,86.12861');
define('CONTACT_MAP_CENTER', '55.329818,86.145707');
define('MAX_HOUSE_IN_SLIDE', 6); //максимальное число домов на слайде
define('DEFAULT_SLIDES_COUNT', 20); //максимальное число слайдов
define('MAX_FILE_SIZE',  5242880); //максимальный размер загружаемых файлов - 5Мб

define('DESIGN_STATUS_MODERATION',   5);   // id статуса дизайна "На модерации"
define('DESIGN_STATUS_ERROR',   6);   // id статуса дизайна "На доработку"
define('DESIGN_STATUS_PUBLISHED', 7);   // id статуса дизайна "Опубликован"
define('DESIGN_STATUS_DELETED',   8);   // id статуса дизайна "Удален"
define('DESIGN_STATUS_DRAFT',   5);   // id статуса дизайна "Черновик"

define('PLAN_STATUS_MODERATION',   61);   // id статуса дизайна "На модерации"
define('PLAN_STATUS_ERROR',   60);   // id статуса дизайна "На доработку"
define('PLAN_STATUS_PUBLISHED', 59);   // id статуса дизайна "Опубликован"
define('PLAN_STATUS_DELETED',   63);   // id статуса дизайна "Удален"
define('PLAN_STATUS_DRAFT',   62);   // id статуса дизайна "Черновик"

define('PROJECT_STATUS_MODERATION',   68);   // id статуса дизайна "На модерации"
define('PROJECT_STATUS_ERROR',   67);   // id статуса дизайна "На доработку"
define('PROJECT_STATUS_PUBLISHED', 66);   // id статуса дизайна "Опубликован"
define('PROJECT_STATUS_DELETED',   70);   // id статуса дизайна "Удален"
define('PROJECT_STATUS_DRAFT',   69);   // id статуса дизайна "Черновик"
define('PROJECT_STATUS_ADDED',   71);   // id статуса дизайна "Создание"

define('ORDER_PLAN_STATUS_MODERATION',   80);   // id статуса дизайна "На модерации"
define('ORDER_PLAN_STATUS_ERROR',   79);   // id статуса дизайна "На доработку"
define('ORDER_PLAN_STATUS_PUBLISHED', 78);   // id статуса дизайна "Опубликован"
define('ORDER_PLAN_STATUS_DELETED',   82);   // id статуса дизайна "Удален"
define('ORDER_PLAN_STATUS_DRAFT',   81);   // id статуса дизайна "Черновик"
define('ORDER_PLAN_STATUS_ADDED',   83);   // id статуса дизайна "Создание"

define('GOODS_STATUS_PUBLISHED',   15);   // id статуса товара "Опубликован"
define('GOODS_STATUS_MODERATION',   16);   // id статуса товара "На модерации"
define('GOODS_STATUS_CANCEL', 17);   // id статуса товара "Отменен"
define('GOODS_STATUS_BLACK',   52);   // id статуса дизайна "Черновик"

define('PROPERTY_STATUS_ID',   27);   // статус дизайна
define('PROPERTY_COMMENT',   28);   // комментарий дизайна

// Типы плательщика
define("INDIVIDUAL", 1); //Физическое лицо

define("DEFAULT_SITE_ID", 's1'); // Основной сайт
define("PAY_SYSTEM_ID", 1); // Платежная система

// Торговый каталог
define('BASE_PRICE', 1);

// Highload IB
define("HL_LIKES", 1);
define("HL_PRIMARY_COLORS", 2);
define("HL_STYLE", 3);
define("HL_CATEGORY", 6);
define("HL_MADEIN", 7);

//Пользователи
define("UNDEFINED_TYPE_ID", 0);
define("PROVIDER_TYPE_ID", 8);
define("SELLER_TYPE_ID", 9);
define("CLIENT_TYPE_ID", 6);
define("DESIGNER_TYPE_ID", 5);

//Дизайны
define("PATTERN_DESIGN_NAME", "Pattern Design");

//Дизайны
define("PATTERN_ROOM_NAME", "Pattern Room");

//Комиссия
define("COMISSION_PERCENT", 21);

//Цена за 1 кв.м
define("PRICE_SQUARE", 300);

// =========
/** @noinspection PhpIncludeInspection */
require_once P_CLASSES . 'IBlockHelper.php';
/** @noinspection PhpIncludeInspection */
require_once P_CLASSES . 'User.php';

spl_autoload_register('customAutoload', false);

$page = $APPLICATION->GetCurPage();
$skipInitZend = false;
if ((($page == '/bitrix/admin/perfmon_panel.php') && !empty($_GET['test'])) || ($page == '/bitrix/admin/perfmon_cluster_graph.php')) {
    $skipInitZend = true;
}

if (!$skipInitZend) {
    EEventHandlers::init();
}

// Логирование изменений
if ((!$skipInitZend) && defined('APPLICATION_ENV') && (APPLICATION_ENV === 'development')) {
    EDebug::init(E_ALL & ~E_NOTICE, array('/bitrix/', P_APP . 'libs/ChangeLogger/', P_APP . 'libs/ZendFramework/'));

    require_once(P_LIBRARY . 'ChangeLogger/class.ChangeLogger.php');
    ChangeLogger::getInstance();
}


// Константы - ID инфоблоков
// Формируются автоматически из кода информационного блока
EHelper::defineConst();

// Сжиматель админки
if (defined('ADMIN_SECTION') && ADMIN_SECTION) {
    $APPLICATION->SetAdditionalCSS(P_CSS . 'admin/admin-small.css');
}
if (defined('ADMIN_SECTION') && ADMIN_SECTION && $_SERVER["SCRIPT_NAME"] == "/bitrix/admin/iblock_element_edit.php" && ($_REQUEST["IBLOCK_ID"] == IB_PIN)) {
	\Bitrix\Main\Page\Asset::getInstance()->addJs(P_JS . 'libs/jquery.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(P_JS . 'libs/jquery.fancybox.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(P_JS . 'admin/slider-pins-setter.js');
}
if (defined('ADMIN_SECTION') && ADMIN_SECTION && $_SERVER["SCRIPT_NAME"] == "/bitrix/admin/iblock_element_edit.php" && ($_REQUEST["IBLOCK_ID"] == IB_PLAN)) {
	\Bitrix\Main\Page\Asset::getInstance()->addJs(P_JS . 'libs/jquery.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(P_JS . 'libs/jquery.fancybox.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(P_JS . 'admin/slider-mark-setter.js');
}
if (defined('ADMIN_SECTION') && ADMIN_SECTION && $_SERVER["SCRIPT_NAME"] == "/bitrix/admin/iblock_element_edit.php" && ($_REQUEST["IBLOCK_ID"] == IB_HOUSE || $_REQUEST["IBLOCK_ID"] == IB_FLAT)) {
    \Bitrix\Main\Page\Asset::getInstance()->addJs(P_JS . 'libs/jquery.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(P_JS . 'libs/jquery.fancybox.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(P_JS . 'admin/slider-pin-setter.js');
}

if (defined('ADMIN_SECTION') && ADMIN_SECTION && $_SERVER["SCRIPT_NAME"] == "/bitrix/admin/iblock_element_edit.php" && ($_REQUEST["IBLOCK_ID"] == IB_HOUSE ||
        $_REQUEST["IBLOCK_ID"] == IB_ENTRANCE || $_REQUEST["IBLOCK_ID"] == IB_FLOOR || $_REQUEST["IBLOCK_ID"] == IB_FLAT )) {
    \Bitrix\Main\Page\Asset::getInstance()->addJs(P_JS . 'libs/jquery.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(P_JS . 'admin/copy-elements.js');
}