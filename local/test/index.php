<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "а тут, кейворды, разные");
$APPLICATION->SetPageProperty("description", "Тут дескрипшн страницы");
$APPLICATION->SetPageProperty("title", "Контакты");
/* @var $APPLICATION */
$APPLICATION->SetTitle("Контакты");
echo $APPLICATION->GetViewContent('ZEND_OUTPUT');
?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>