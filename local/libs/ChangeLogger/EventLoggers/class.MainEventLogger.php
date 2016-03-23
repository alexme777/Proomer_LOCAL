<?php

/**
 * Class MainEventLogger
 */
class MainEventLogger extends ModuleEventLogger
{
    /**
     * @param $arFields
     */
    function OnAfterGroupAddHandler($arFields)
    {
        // Подготовка лога
        $logMessage  = "Добавлена новая группа пользователей. Поля:\n";
        $logMessage .= var_export($arFields, true);;

        // Добавление лога в свойство класса
        self::$logMessage = $logMessage;

        // Добавление лога в файл
        self::saveToLog();
    }

    /**
     * @param $idGroup
     */
    function OnAfterGroupUpdateHandler($idGroup)
    {
        // Предварительная подготовка переменных
        $arFields = CGroup::GetByID($idGroup)->Fetch();

        // Подготовка лога
        $logMessage  = "Изменена группа пользователей с ID = " . $idGroup . ". Актуальные поля:\n";
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
        $logMessage  = "Удалена группа пользователей с ID = " . $id . "\n";

        // Добавление лога в свойство класса
        self::$logMessage = $logMessage;

        // Добавление лога в файл
        self::saveToLog();
    }
    
    /**
     * @param $arFields
     */
    function OnAfterSetOption_reportsHandler($arFields)
    {
        // Подготовка лога
        $logMessage  = "Изменены печатные формы. Поля:\n";
        $logMessage .= var_export(unserialize($arFields), true);;

        // Добавление лога в свойство класса
        self::$logMessage = $logMessage;

        // Добавление лога в файл
        self::saveToLog();
    }

    /**
     *
     */
    function OnAfterEpilogHandler()
    {
        ChangeLogger::getInstance()->closeLogFile();
    }
}