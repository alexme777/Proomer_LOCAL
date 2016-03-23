<?php
/**
 * Этот файл должен подключаться в начале каждого CRON-обработчика.
 * Здесь задаются серверные переменные и подключается prolog_before.php Битрикса
 */
if(!$_SERVER['DOCUMENT_ROOT']){
    $_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__) . "/../../");
    $_SERVER['BX_PERSONAL_ROOT'] = "/local/bitrix";
}


$host = $_SERVER['HTTP_HOST'];
$addr = $_SERVER['SERVER_ADDR'];
if (!empty($host) && !empty($addr)) {
    die('<h1>А-та-та!</h1><h3>Сработала защита от дурака</h3><p>Тестировать выполнение крон-обработчиков ни в коем случае нельзя, запуская обработчик из браузера. Проверять нужно из консоли под тем пользователем, под которым будет запускаться крон, или нормальным образом настраивать кронтаб (для теста можно поставить малый интервал запуска).</p>');
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if(!CModule::IncludeModule("iblock")) {
    die('Ошибка подключения модуля iblock');
}