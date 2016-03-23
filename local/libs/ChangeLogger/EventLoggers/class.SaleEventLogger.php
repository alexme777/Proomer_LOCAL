<?php

/**
 * Class SaleEventLogger
 */
class SaleEventLogger extends ModuleEventLogger
{
    /**
     * @param $id
     * @param $arFields
     */
    function OnPersonTypeAddHandler($id ,$arFields)
    {
        // Подготовка лога
        $logMessage  = "Добавлен новый тип плательщиков с ID = ".$id.". Поля:\n";
        $logMessage .= var_export($arFields, true);;

        // Добавление лога в свойство класса
        self::$logMessage = $logMessage;

        // Добавление лога в файл
        self::saveToLog();
    }

    /**
     * @param $id
     * @param $arFields
     */
    function OnPersonTypeUpdateHandler($id ,$arFields)
    {
        // Подготовка лога
        $logMessage  = "Изменен тип плательщика с ID = " . $id . ". Актуальные поля:\n";
        $logMessage .= var_export($arFields, true);

        // Добавление лога в свойство класса
        self::$logMessage = $logMessage;

        // Добавление лога в файл
        self::saveToLog();
    }

    /**
     * @param $id
     */
    function OnPersonTypeDeleteHandler($id)
    {
        // Подготовка лога
        $logMessage  = "Удален тип плательщика с ID = " . $id . "\n";

        // Добавление лога в свойство класса
        self::$logMessage = $logMessage;

        // Добавление лога в файл
        self::saveToLog();
    }

    /**
     * @param $id
     * @param $arFields
     */
    function OnStatusAddHandler($id ,$arFields)
    {
        // Подготовка лога
        $logMessage  = "Добавлен новый статус заказа с ID = ".$id.". Поля:\n";
        $logMessage .= var_export($arFields, true);;

        // Добавление лога в свойство класса
        self::$logMessage = $logMessage;

        // Добавление лога в файл
        self::saveToLog();
    }

    /**
     * @param $id
     * @param $arFields
     */
    function OnStatusUpdateHandler($id ,$arFields)
    {
        // Подготовка лога
        $logMessage  = "Изменен статус заказа с ID = " . $id . ". Актуальные поля:\n";
        $logMessage .= var_export($arFields, true);

        // Добавление лога в свойство класса
        self::$logMessage = $logMessage;

        // Добавление лога в файл
        self::saveToLog();
    }

    /**
     * @param $id
     */
    function OnStatusDeleteHandler($id)
    {
        // Подготовка лога
        $logMessage  = "Удален статус заказа с ID = " . $id . "\n";

        // Добавление лога в свойство класса
        self::$logMessage = $logMessage;

        // Добавление лога в файл
        self::saveToLog();
    }
}