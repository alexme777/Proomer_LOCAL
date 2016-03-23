<?
/**
 * Class EEventHandlers
 */
class EEventHandlers {

    /**
     * Инициализировать базовые события
     */
    public static function init() {
        // Старт зенда
        AddEventHandler("main", "OnBeforeProlog", array("EZendManager", "Bootstrap"));
        // Обработка 404
        AddEventHandler("main", "OnEpilog", array("EEventHandlers", "Redirect404"));
        
        // Шаблонизация писем
        AddEventHandler("main", "OnBeforeEventSend", array("EHelper", "wrapMailTemplate"));

        //Хук, который не позволяет сделать больше одного города по умолчанию
        AddEventHandler("iblock", "OnAfterIBlockElementAdd", array("Sibirix_Model_City", "setDefaultCity"));
        AddEventHandler("iblock", "OnAfterIBlockElementUpdate", array("Sibirix_Model_City", "setDefaultCity"));

        //Кеширование средней стоимости и количество дизайнов в ЖК
        AddEventHandler("iblock", "OnAfterIBlockElementAdd", array("Sibirix_Model_Complex", "cacheComplexData"));
        AddEventHandler("iblock", "OnAfterIBlockElementUpdate", array("Sibirix_Model_Complex", "cacheComplexData"));

        //Отправка уведомлений при смене статуса
        AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", array("Sibirix_Model_Design", "changingStatus"));
		
		//Отправка уведомлений при получении заказа
        AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", array("Sibirix_Model_Order", "addOrder"));
    }

    /**
     *
     */
    public static function Redirect404() {
        if (
            !defined('ADMIN_SECTION') &&
            (defined("ERROR_404") || (function_exists("http_response_code") && http_response_code() == 404)) &&
            file_exists($_SERVER["DOCUMENT_ROOT"] . "/404.php")
        ) {
            global $APPLICATION;
            $APPLICATION->RestartBuffer();
            if (!defined('ERROR_404')) define("ERROR_404", "Y");
            $SITE_TEMPLATE_ID = Bitrix\Main\SiteTemplateTable::getCurrentTemplateId(SITE_ID);
            $SITE_TEMPLATE_PATH = BX_PERSONAL_ROOT.'/templates/'.$SITE_TEMPLATE_ID;

            include($_SERVER["DOCUMENT_ROOT"] . $SITE_TEMPLATE_PATH . '/header.php');
            include($_SERVER["DOCUMENT_ROOT"] . "/404.php");
            include($_SERVER["DOCUMENT_ROOT"] . $SITE_TEMPLATE_PATH . '/footer.php');
        }
    }
}
