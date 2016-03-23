<?php

/**
 * Class IblockEventLogger
 */
class IblockEventLogger extends ModuleEventLogger
{
    /**
     * @param $arFields
     */
    function OnAfterIblockAddHandler($arFields)
    {
        // Предварительная подготовка переменных
        $arIBType = CIBlockType::GetByIDLang($arFields['IBLOCK_TYPE_ID'], LANGUAGE_ID);

        // Подготовка лога
        $logMessage  = "Добавлен инфоблок с ID=" . $arFields["ID"] . " в тип инфоблоков \"" . $arIBType["NAME"] . "\"\n";
        $logMessage .= "    Инфоблок:\n";
        $logMessage .= "        Активен: " . $arFields["ACTIVE"] . "\n";
        $logMessage .= "        Название: " . $arFields["NAME"] . "\n";
        $logMessage .= "        Символьные код: " . $arFields["CODE"] . "\n";
        $logMessage .= "        Сайты: " . implode(', ', $arFields["LID"]) . "\n";
        $logMessage .= "        Сортировка: " . $arFields["SORT"] . "\n";
        $logMessage .= "        URL страницы информационного блока: " . $arFields["LIST_PAGE_URL"] . "\n";
        $logMessage .= "        URL страницы детального просмотра: " . $arFields["DETAIL_PAGE_URL"] . "\n";
        $logMessage .= "    Поля:\n";
        $logMessage .= "        Привязка к разделам:\n";
        $logMessage .= "            Обязательное: " . $arFields["FIELDS"]['IBLOCK_SECTION']['IS_REQUIRED'] . "\n";
        $logMessage .= "        Активность:\n";
        $logMessage .= "            Значение по умолчанию: " . $arFields["FIELDS"]['ACTIVE']['DEFAULT_VALUE'] . "\n";
        $logMessage .= "        Начало активности:\n";
        $logMessage .= "            Обязательное: " . $arFields["FIELDS"]['ACTIVE_FROM']['IS_REQUIRED'] . "\n";
        $logMessage .= "            Значение по умолчанию: " . $arFields["FIELDS"]['ACTIVE_FROM']['DEFAULT_VALUE'] . "\n";
        $logMessage .= "        Окончание активности:\n";
        $logMessage .= "            Обязательное: " . $arFields["FIELDS"]['ACTIVE_FROM']['IS_REQUIRED'] . "\n";
        $logMessage .= "            Значение по умолчанию: " . $arFields["FIELDS"]['ACTIVE_FROM']['DEFAULT_VALUE'] . "\n";
        $logMessage .= "        Сортировка:\n";
        $logMessage .= "            Обязательное: " . $arFields["FIELDS"]['SORT']['IS_REQUIRED'] . "\n";
        $logMessage .= "        Название:\n";
        $logMessage .= "            Значение по умолчанию: " . $arFields["FIELDS"]['NAME']['DEFAULT_VALUE'] . "\n";
        $logMessage .= "        Картинка для анонса:\n";
        $logMessage .= "            Обязательное: " . $arFields["FIELDS"]['PREVIEW_PICTURE']['IS_REQUIRED'] . "\n";
        $logMessage .= "            Значение по умолчанию:\n";
        $logMessage .= "                Создавать картинку анонса из детальной (если не задана): " . $arFields["FIELDS"]['PREVIEW_PICTURE']['DEFAULT_VALUE']['FROM_DETAIL'] . "\n";
        $logMessage .= "                Удалять картинку анонса, если удаляется детальная: " . $arFields["FIELDS"]['PREVIEW_PICTURE']['DEFAULT_VALUE']['DELETE_WITH_DETAIL'] . "\n";
        $logMessage .= "                Создавать картинку анонса из детальной даже если задана: " . $arFields["FIELDS"]['PREVIEW_PICTURE']['DEFAULT_VALUE']['UPDATE_WITH_DETAIL'] . "\n";
        $logMessage .= "                Уменьшать если большая: " . $arFields["FIELDS"]['PREVIEW_PICTURE']['DEFAULT_VALUE']['SCALE'] . "\n";
        $logMessage .= "                    Максимальная ширина: " . $arFields["FIELDS"]['PREVIEW_PICTURE']['DEFAULT_VALUE']['WIDTH'] . "\n";
        $logMessage .= "                    Максимальная высота: " . $arFields["FIELDS"]['PREVIEW_PICTURE']['DEFAULT_VALUE']['HEIGHT'] . "\n";
        $logMessage .= "                Наносить авторский знак в виде изображения: " . $arFields["FIELDS"]['PREVIEW_PICTURE']['DEFAULT_VALUE']['USE_WATERMARK_FILE'] . "\n";
        $logMessage .= "                Наносить авторский знак в виде текста: " . $arFields["FIELDS"]['PREVIEW_PICTURE']['DEFAULT_VALUE']['USE_WATERMARK_TEXT'] . "\n";
        $logMessage .= "        Тип описания для анонса:\n";
        $logMessage .= "            Значение по умолчанию: " . $arFields["FIELDS"]['PREVIEW_TEXT_TYPE']['DEFAULT_VALUE'] . "\n";
        $logMessage .= "        Описание для анонса:\n";
        $logMessage .= "            Обязательное: " . $arFields["FIELDS"]['PREVIEW_TEXT']['IS_REQUIRED'] . "\n";
        $logMessage .= "            Значение по умолчанию: " . $arFields["FIELDS"]['PREVIEW_TEXT']['DEFAULT_VALUE'] . "\n";
        $logMessage .= "        Детальная картинка:\n";
        $logMessage .= "            Обязательное: " . $arFields["FIELDS"]['DETAIL_PICTURE']['IS_REQUIRED'] . "\n";
        $logMessage .= "            Значение по умолчанию:\n";
        $logMessage .= "                Уменьшать если большая: " . $arFields["FIELDS"]['DETAIL_PICTURE']['DEFAULT_VALUE']['SCALE'] . "\n";
        $logMessage .= "                    Максимальная ширина: " . $arFields["FIELDS"]['DETAIL_PICTURE']['DEFAULT_VALUE']['WIDTH'] . "\n";
        $logMessage .= "                    Максимальная высота: " . $arFields["FIELDS"]['DETAIL_PICTURE']['DEFAULT_VALUE']['HEIGHT'] . "\n";
        $logMessage .= "                Наносить авторский знак в виде изображения: " . $arFields["FIELDS"]['DETAIL_PICTURE']['DEFAULT_VALUE']['USE_WATERMARK_FILE'] . "\n";
        $logMessage .= "                Наносить авторский знак в виде текста: " . $arFields["FIELDS"]['DETAIL_PICTURE']['DEFAULT_VALUE']['USE_WATERMARK_TEXT'] . "\n";
        $logMessage .= "        Тип детального описания:\n";
        $logMessage .= "            Значение по умолчанию: " . $arFields["FIELDS"]['DETAIL_TEXT_TYPE']['DEFAULT_VALUE'] . "\n";
        $logMessage .= "        Детальное описание:\n";
        $logMessage .= "            Обязательное: " . $arFields["FIELDS"]['DETAIL_TEXT']['IS_REQUIRED'] . "\n";
        $logMessage .= "            Значение по умолчанию: " . $arFields["FIELDS"]['DETAIL_TEXT']['DEFAULT_VALUE'] . "\n";
        $logMessage .= "        Внешний код:\n";
        $logMessage .= "            Обязательное: " . $arFields["FIELDS"]['XML_ID']['IS_REQUIRED'] . "\n";
        $logMessage .= "        Символьный код:\n";
        $logMessage .= "            Обязательное: " . $arFields["FIELDS"]['CODE']['IS_REQUIRED'] . "\n";
        $logMessage .= "            Значение по умолчанию:\n";
        $logMessage .= "                Если код задан, то проверять на уникальность: " . $arFields["FIELDS"]['CODE']['DEFAULT_VALUE']['UNIQUE'] . "\n";
        $logMessage .= "                Транслитерировать из названия при добавлении элемента: " . $arFields["FIELDS"]['CODE']['DEFAULT_VALUE']['TRANSLITERATION'] . "\n";
        $logMessage .= "                Максимальная длина результата транслитерации: " . $arFields["FIELDS"]['CODE']['DEFAULT_VALUE']['TRANS_LEN'] . "\n";
        $logMessage .= "                Приведение к регистру: " . $arFields["FIELDS"]['CODE']['DEFAULT_VALUE']['TRANS_CASE'] . "\n";
        $logMessage .= "                Замена для символа пробела: " . $arFields["FIELDS"]['CODE']['DEFAULT_VALUE']['TRANS_SPACE'] . "\n";
        $logMessage .= "                Замена для прочих символов: " . $arFields["FIELDS"]['CODE']['DEFAULT_VALUE']['TRANS_OTHER'] . "\n";
        $logMessage .= "                Удалять лишние символы замены: " . $arFields["FIELDS"]['CODE']['DEFAULT_VALUE']['TRANS_EAT'] . "\n";
        $logMessage .= "                Использовать внешний сервис для перевода: " . $arFields["FIELDS"]['CODE']['DEFAULT_VALUE']['USE_GOOGLE'] . "\n";
        $logMessage .= "        Теги:\n";
        $logMessage .= "            Обязательное: " . $arFields["FIELDS"]['TAGS']['IS_REQUIRED'] . "\n";

        // Добавление лога в свойство класса
        self::$logMessage = $logMessage;

        // Добавление лога в файл
        self::saveToLog();
    }

    /**
     * @param $arFields
     */
    function OnBeforeIblockUpdateHandler($arFields)
    {
        // Сохранение полей до изменения
        $iblock = CIBlock::GetFields($arFields["ID"]);
        self::$tmpArr['iblock'] = $iblock;
    }

    /**
     * @param $arFields
     * @return bool
     */
    function OnAfterIblockUpdateHandler($arFields)
    {
        // Предварительная подготовка переменных
        $iblock = CIBlock::GetFields($arFields["ID"]);
        $arChanges = self::deepArrayDiff($iblock, self::$tmpArr['iblock']);
        $arIBType = CIBlockType::GetByIDLang($arFields['IBLOCK_TYPE_ID'], LANGUAGE_ID);

        // Если реально не было изменений полей инфоблока, не пишем в лог
        if (! count($arChanges)) {
            return true;
        }

        // Подготовка лога
        $logMessage  = "Имзенен инфоблок с ID=" . $arFields["ID"] . " в типе инфоблоков \"" . $arIBType["NAME"] . "\"\n";
        $logMessage .= "    Ниже приведен массив изменений полей инфоблока: \n";
        $logMessage .= var_export($arChanges, true);

        // Добавление лога в свойство класса
        self::$logMessage = $logMessage;

        // Добавление лога в файл
        self::saveToLog();
    }

    /**
     * @param $id
     */
    function OnBeforeIblockDeleteHandler($id)
    {
        // Предварительная подготовка переменных
        $arIblock = CIBlock::GetByID($id)->Fetch();
        $arIBType = CIBlockType::GetByIDLang($arIblock['IBLOCK_TYPE_ID'], LANGUAGE_ID);

        // Подготовка лога
        $logMessage  = "Удален инфоблок \"" . $arIblock["NAME"] . "\" с ID=" . $id . " из типа инфоблоков \"" . $arIBType["NAME"] . "\"";

        // Добавление лога в свойство класса
        self::$logMessage = $logMessage;

        // Добавление лога в файл
        self::saveToLog();
    }

    /**
     * @param $arFields
     * @return bool
     */
    function OnAfterIBlockPropertyAddHandler($arFields)
    {
        // Если свойство пытается сохраниться до применения изменений, не пишем в лог
        if ($_REQUEST["bxsender"] == "core_window_cadmindialog") {
            return true;
        }

        // Предварительная подготовка переменных
        $iblock = CIBlock::GetByID($arFields['IBLOCK_ID'])->Fetch();
        $propertyType = $arFields["USER_TYPE"]
            ? $arFields["PROPERTY_TYPE"] . ':' . $arFields["USER_TYPE"]
            : $arFields["PROPERTY_TYPE"];

        // Подготовка лога
        $logMessage  = "Добавлено новое свойство инфоблока " . $iblock["NAME"] . " \n";
        $logMessage .= "    Название: " . $arFields["NAME"] . " \n";
        $logMessage .= "    Тип: " . self::getPropertyTypeByCode($propertyType) . " \n";
        $logMessage .= "    Активно: " . $arFields["ACTIVE"] . " \n";
        $logMessage .= "    Множественное: " . $arFields["MULTIPLE"] . " \n";
        $logMessage .= "    Обязательно: " . $arFields["IS_REQUIRED"] . " \n";
        $logMessage .= "    Сортировка: " . $arFields["SORT"] . " \n";
        $logMessage .= "    Код: " . $arFields["CODE"] . " \n";
        $logMessage .= "    Дополнительно: \n";
        $logMessage .= "        Значения свойства участвуют в поиске: " . $arFields["SEARCHABLE"] . " \n";
        $logMessage .= "        Выводить на странице списка элементов поле для фильтрации по этому свойству: " . $arFields["FILTRABLE"] . " \n";
        $logMessage .= "        Подсказка: " . $arFields["HINT"] . " \n";
        $logMessage .= "        Показывать в умном фильтре: " . $arFields["SMART_FILTER"] . " \n";
        $logMessage .= "        Значения списка: \n";
        foreach ($arFields["VALUES"] as $val) {
            $logMessage .= "            ID: " . $val["ID"] . ", XML_ID: " . $val["XML_ID"] .
                ", Значение: " . $val["VALUE"] . ", Сортировка: " . $val["SORT"] . ", По умолчанию: " . $val["DEF"] . " \n";
        }

        // Добавление лога в свойство класса
        self::$logMessage = $logMessage;

        // Добавление лога в файл
        self::saveToLog();
    }

    /**
     * @param $arFields
     */
    function OnBeforeIBlockPropertyUpdateHandler($arFields)
    {
        $prop = CIBlockProperty::GetByID($arFields["ID"])->Fetch();
        self::$tmpArr['prop'] = $prop;
    }

    /**
     * @param $arFields
     * @return bool
     */
    function OnAfterIBlockPropertyUpdateHandler($arFields)
    {
        // Предварительная подготовка переменных
        $prop = CIBlockProperty::GetByID($arFields["ID"])->Fetch();
        $arChanges = self::deepArrayDiff($prop, self::$tmpArr['prop']);
        $iblock = CIBlock::GetByID($prop['IBLOCK_ID'])->Fetch();

        // Если изменений по свойству не произошло, не пишем в лог
        if (! count($arChanges)) {
            return true;
        }

        // Подготовка лога
        $logMessage  = "Изменено свойство \"" . $prop["NAME"] . "\" с ID=" . $arFields["ID"] . " инфоблока " . $iblock["NAME"] . " \n";
        $logMessage .= "    Ниже приведен массив изменений свойства: \n";
        $logMessage .= var_export($arChanges, true);

        // Добавление лога в свойство класса
        self::$logMessage = $logMessage;

        // Добавление лога в файл
        self::saveToLog();
    }

    /**
     * @param $id
     */
    function OnBeforeIBlockPropertyDeleteHandler($id)
    {
        // Предварительная подготовка переменных
        $prop = CIBlockProperty::GetByID($id)->Fetch();
        $iblock = CIBlock::GetByID($prop['IBLOCK_ID'])->Fetch();

        // Подготовка лога
        $logMessage  = "Удалено свойство \"" . $prop["NAME"] . "\" с ID=" . $id . " инфоблока " . $iblock["NAME"] . " \n";

        // Добавление лога в свойство класса
        self::$logMessage = $logMessage;

        // Добавление лога в файл
        self::saveToLog();
    }
}