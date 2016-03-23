<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

class Settings {

    protected static $_instance;
    protected static $_options;

    const VALUE_KEY = 'PROPERTY_VALUE_VALUE';
    const TEXT_KEY  = 'PREVIEW_TEXT';
    const FILE_KEY  = 'PREVIEW_PICTURE';
    const DATE_KEY  = 'ACTIVE_FROM';

    private function __construct() {
        $items = IBlockWrap::instance(IB_SETTINGS)
            ->select(array('CODE', 'PROPERTY_VALUE', 'PREVIEW_TEXT', 'DETAIL_TEXT', 'PREVIEW_PICTURE', 'ACTIVE_FROM'))
            ->fetch();

        self::$_options = array();
        foreach ($items as $item) {
            self::$_options[$item['CODE']] = $item;
        }
    }

    private function __clone() {
    }

    private function __wakeup() {
    }

    /**
     * получаем опцию
     * @param $attr string название аттрибута
     * @param $defaultVal string значение по умолчанию
     * @return mixed
     * @throws
     */
    public static function getOption($attr, $defaultVal = '') {
        if (self::emptyOption($attr)) {
            return $defaultVal;
        }

        return self::getOptionParam($attr, self::VALUE_KEY);
    }

    public static function getOptionFile($attr, $defaultVal = '') {
        if (self::emptyOptionFile($attr)) {
            return $defaultVal;
        }

        return self::getOptionParam($attr, self::FILE_KEY);
    }

    public static function getOptionDate($attr, $defaultVal = '') {
        if (self::emptyOptionDate($attr)) {
            return $defaultVal;
        }

        return self::getOptionParam($attr, self::DATE_KEY);
    }

    /**
     * получаем текст-описание опции
     * @param $attr string название аттрибута
     * @param $defaultVal string значение по умолчанию
     * @return mixed
     * @throws
     */
    public static function getOptionText($attr, $defaultVal = '') {
        if (self::emptyOptionText($attr)) {
            return $defaultVal;
        }

        return self::getOptionParam($attr, self::TEXT_KEY);
    }


    /**
     * получаем один из параметров опции: значение или текст-описание
     * @param $attr string название опции
     * @param $key string ключ
     * @return mixed
     * @throws
     */
    protected static function getOptionParam($attr, $key) {
        if (empty($attr)) {
            throw new Exception('Не указано имя настройки');
        }

        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        if (array_key_exists($attr, self::$_options)
            && array_key_exists($key, self::$_options[$attr])) {
            return self::$_options[$attr][$key];
        }

        throw new Exception('Нет такой настройки');
    }

    /**
     * проверяем опцию на пустоту
     * @param $attr
     * @return bool
     */
    public static function emptyOption($attr) {
        return self::emptyOptionParam($attr, self::VALUE_KEY);
    }

    /**
     * проверяем текст опции на пустоту
     * @param $attr
     * @return bool
     */
    public static function emptyOptionText($attr) {
        return self::emptyOptionParam($attr, self::TEXT_KEY);
    }

    /**
     * проверяем file опции на пустоту
     * @param $attr
     * @return bool
     */
    public static function emptyOptionFile($attr) {
        return self::emptyOptionParam($attr, self::FILE_KEY);
    }

    /**
     * проверяем date опции на пустоту
     * @param $attr
     * @return bool
     */
    public static function emptyOptionDate($attr) {
        return self::emptyOptionParam($attr, self::DATE_KEY);
    }

    /**
     * проверяем на пустоту один из параметров опции: значение или текст-описание
     * @param $attr string название опции
     * @param $key string ключ
     * @return bool
     */
    public static function emptyOptionParam($attr, $key) {
        try {
            $option = self::getOptionParam($attr, $key);
        } catch (Exception $e) {
            return true;
        }

        return '' == $option;
    }
}
