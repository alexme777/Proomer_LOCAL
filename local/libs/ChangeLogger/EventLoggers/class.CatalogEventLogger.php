<?php

/**
 * Class CatalogEventLogger
 */
class CatalogEventLogger extends ModuleEventLogger
{
    /**
     * @param $arFields
     */
    function OnBeforeGroupAddHandler($arFields)
    {
        // Подготовка лога
        $logMessage  = "Добавлен тип цен. Поля:\n";
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
    function OnGroupUpdateHandler($id ,$arFields)
    {
        // Подготовка лога
        $logMessage  = "Изменен тип цен с ID = " . $id . ". Актуальные поля:\n";
        $logMessage .= var_export($arFields, true);

        // Добавление лога в свойство класса
        self::$logMessage = $logMessage;

        // Добавление лога в файл
        self::saveToLog();
    }

    /**
     * @param $id
     */
    function OnGroupDeleteHandler($id)
    {
        // Подготовка лога
        $logMessage  = "Удален тип цен с ID = " . $id . "\n";

        // Добавление лога в свойство класса
        self::$logMessage = $logMessage;

        // Добавление лога в файл
        self::saveToLog();
    }
}