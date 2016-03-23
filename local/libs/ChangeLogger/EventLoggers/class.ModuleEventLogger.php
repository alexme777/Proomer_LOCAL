<?php

/**
 * Class ModuleEventLogger
 */
class ModuleEventLogger
{
    /**
     * @var array
     */
    protected static $tmpArr = array();

    /**
     * @var
     */
    protected static $logMessage;

    /**
     *
     */
    protected static function saveToLog()
    {
        ChangeLogger::getInstance()->saveToLog(self::$logMessage);
        self::$logMessage = '';
    }

    /**
     * @param $arr1
     * @param $arr2
     * @return array
     */
    protected static function deepArrayDiff($arr1, $arr2)
    {
        $diff = array_diff($arr1, $arr2);

        foreach($arr1 as $key => $value) {
            if (is_array($arr1[$key]))
                $diff[$key] = self::deepArrayDiff($arr1[$key], $arr2[$key]);

            if (empty($diff[$key]))
                unset($diff[$key]);
        }

        return $diff;
    }

    /**
     * @param $code
     * @return string
     */
    protected static function getPropertyTypeByCode($code)
    {
        switch ($code) {
            case 'S': return 'Строка'; break;
            case 'N': return 'Число'; break;
            case 'L': return 'Список'; break;
            case 'F': return 'Файл'; break;
            case 'G': return 'Привязка к разделам'; break;
            case 'E': return 'Привязка к элементам'; break;
            case 'S:DateTime': return 'Дата/Время'; break;
            case 'S:ElementXmlID': return 'Привязка к элементам по XML_ID'; break;
            case 'S:FileMan': return 'Привязка к файлу (на сервере)'; break;
            case 'S:HTML': return 'HTML/текст'; break;
            case 'E:EList': return 'Привязка к элементам в виде списка'; break;
            case 'N:Sequence': return 'Счетчик'; break;
            case 'E:EAutocomplete': return 'Привязка к элементам с автозаполнением'; break;
            case 'E:SKU': return 'Привязка к товарам (SKU)'; break;
            case 'S:UserID': return 'Привязка к пользователю'; break;
            case 'S:map_google': return 'Привязка к карте Google Maps'; break;
            case 'S:map_yandex': return 'Привязка к Яндекс.Карте'; break;
            case 'S:video': return 'Видео'; break;
            case 'S:TopicID': return 'Привязка к теме форума'; break;
        }
    }
}