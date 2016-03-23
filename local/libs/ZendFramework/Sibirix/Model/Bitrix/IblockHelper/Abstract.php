<?php
/**
 * Обертка над *::GetList
 *
 * Обертка над *::GetList для формирования массива параметров с заданной
 * сортировкой, фильтрацией и навигацией. Реализует кэширование.
 * Недопускается повторная выборка с использованием одного экземпляра класса.
 * Допустимо наследование для работы с другими *::GetList
 *
 * @package files
 * @subpackage classes
 */
abstract class Sibirix_Model_Bitrix_IblockHelper_Abstract
{
    /**
     * Сгенерированный пагинатор
     * @var bool|string
     */
    protected $_pager = false;

    /**
     * Шаблон пагинатора
     * @var bool|string
     */
    protected $_pagerTemplate = false;

    /**
     * Общее количество элементов подходящее под выборку (возвращает GetList)
     * @var bool|int
     */
    protected $_totalCount = false;

    /**
     * @var bool флаг использования кэша
     */
    private $_use_cache = false;

    /**
     * @var bool время кэширования. используется только если включен $_use_cache
     */
    private $_cache_time = false;

    /**
     * @var bool флаг, для контроля единичного использования экземпляра класса
     */
    private $_second_use = false;

    /**
     * @var string Папка для кэша
     */
    protected $_cacheDir = '/';

    /**
     * @var array функция над которой оборачивается класс
     */
    protected $_getlist_function = array("CIBlockElement", "GetList");

    protected $_fetch_function = "Fetch";

    /**
     * @var array параметры для передачи в getList
     */
    protected $_getlist = array(
        "arOrder"           => array("SORT" => "ASC"),
        "arFilter"          => array("ACTIVE" => "Y"),
        "arGroupBy"         => false,
        "arNavStartParams"  => false,
        "arSelectFields"    => array()
    );

    /**
     * Конструктор
     * Подключение обязательного битриксового модуля Инфоблоки
     */
    private function __construct() {
        CModule::IncludeModule("iblock");
    }

    /**
     * Создание экземпляра класса выборки
     *
     * @param bool|int $iBlockId ID инфоблока над которым будет производится выборка
     *
     * @return self
     */
    public static function instance($iBlockId=false) {
        $class_name = get_called_class();
        /* @var $instance self */
        $instance   = new $class_name();

        if ($iBlockId) {
            $instance->where(array('IBLOCK_ID' => $iBlockId));
            $instance->_cacheDir = 'iblock-id-' . $iBlockId;
        }
        $instance->configureCache();

        return $instance;
    }

    /**
     * Настройки кэширования
     *
     * Берет настройки кэширования из application.ini. Если их там нет cacheType = N
     */
    protected function configureCache() {
        $options = Sibirix_Helper::getOption('iblockhelper');
        switch ($options['cacheType']) {
            case 'A':
                $options['cacheType'] = COption::getOptionString('main', 'component_cache_on', 'Y');
                break;
            case 'Y':
                break;
            default:
                $options['cacheType'] = 'N';
                break;
        }

        $options['cacheTime'] = (isset($options['cacheTime']) && intval($options['cacheTime']) > 0
            ? intval($options['cacheTime'])
            : 60 * 10
        );

        if ($options['cacheTime'] && $options['cacheType'] == 'Y') {
            $this->cache($options['cacheTime']);
        }
    }

    /**
     * Включает использование кэша на $time секунд
     *
     * @param $time
     * @return self
     */
    public function cache($time) {
        $this->_use_cache = $time > 0;
        if ($time) {
            $this->_cache_time = $time;
        }
        return $this;
    }

    /**
     * Переключает использование Fetch/GetNext
     *
     * @param bool $useFetch
     * @return self
     */
    public function useRawFetch($useFetch = true) {
        $this->_fetch_function = $useFetch ? 'Fetch' : 'GetNext';
        return $this;
    }

    /**
     * Добавление полей и свойств по которым идет выборка
     *
     * важно! в итоговом списке полей обязательно должен остаться IBLOCK_ID. см.документацию getList
     * примечание. допустимо указывать свойства вида PROPERTY_<PROPERTY_CODE>, где PROPERTY_CODE - мнемонический код
     * @link https://dev.1c-bitrix.ru/api_help/iblock/fields.php#felement примечание. список полей элементов инфоблока
     *
     * @param $fields array поля которые нужно выбирать
     * @param bool $clear удалить текущий список полей перед добавлением новых
     * @return self
     */
    public function select($fields, $clear = false) {
        if ($clear) {
            $this->_getlist['arSelectFields'] = array();
        }

        $this->_getlist['arSelectFields'] = array_merge(
            $this->_getlist['arSelectFields'],
            $fields
        );
        return $this;
    }

    /**
     * Добавление параметров для фильтрации элементов
     *
     * @link https://dev.1c-bitrix.ru/api_help/iblock/filter.php смотреть при формировании фильтра
     *
     * @param $fields array поля фильтрации. в простейшем случае вида: array("фильтруемое поле"=>"значения фильтра" [, ...])
     * @param bool $clear очистить текущий фильтр перед добавлением
     * @return self
     */
    public function where($fields, $clear = false) {
        if ($clear) {
            $this->_getlist['arFilter'] = array();
        }

        $this->_getlist['arFilter'] = array_merge(
            $this->_getlist['arFilter'],
            $fields
        );
        return $this;
    }

    /**
     * Добавление полей сортировки
     *
     * @param $fields array поля сортировки вида: array(by1=>order1[, by2=>order2 [, ..]])
     * @param bool $clear очистить текущий список полей сортировки перед добавлением новых
     * @return self
     */
    public function order($fields, $clear = true) {
        if ($clear) {
            $this->_getlist['arOrder'] = array();
        }

        $this->_getlist['arOrder'] = array_merge(
            $this->_getlist['arOrder'],
            $fields
        );
        return $this;
    }

    /**
     * Вызов низкоуровневой функции битрикса для получения данных
     *
     * @return mixed
     */
    protected function callGetList() {
        // проверка повторного использования
        $this->checkReuse();

        // объект кэша должен быть доступен из двух скоупов второго уровня. чтобы все видели что используется,
        // объявляется на первом уровне
	// Если нужны случайные элементы - выключаем кэш
        if (array_key_exists('RAND', $this->_getlist['arOrder'])) {
            $this->cache(false);
        }
        $cache = $this->_use_cache ? new CPHPCache() : null;

        $cacheKey = serialize(array(
            $this->_getlist,
            $this->_getlist_function,
            $this->_fetch_function,
            $this->_pagerTemplate,
        ));

        // если включено кэширование, то попытка сразу вернуть данные
        if ($this->_use_cache) {
            if ($cache->InitCache($this->_cache_time, $cacheKey, $this->_cacheDir)) {
                $result = $cache->GetVars();

                $this->_pager = $result['pager'];
                $this->_totalCount = $result['totalCount'];

                return $result['data'];
            }
        }

        // вызов низкоуровневой функции битрикса для получения данных
        $callObject = new $this->_getlist_function[0];
        /* @var $results CIBlockResult */
        $results = call_user_func_array(
            array($callObject, $this->_getlist_function[1]),
            $this->_getlist
        );

        // итерирование результатов если нужно
        if (is_a($results, 'CIBlockResult')) {
            // есть возможность узнать и сохранить общее количество элементов и сгенерировать пагинатор
            $this->_totalCount = $results->NavRecordCount;
            if ($this->_pagerTemplate !== false) {
                $this->_pager = $results->GetPageNavString('', $this->_pagerTemplate);
            }

            // извлечение элементов
            $pageExist  = true;
            $pageSize   = $this->_getlist['arNavStartParams']['nPageSize'];
            $pageNumber = $this->_getlist['arNavStartParams']['iNumPage'];
            if (!empty($pageSize) && !empty($pageNumber)) {
                $pageExist = $this->_totalCount > $pageSize * ($pageNumber - 1);
            }

            $items = array();
            if ($pageExist) {

                /* Если активирован тэгированный кэш - стартуем его */
                if (defined("BX_COMP_MANAGED_CACHE")) {
                    Zend_Registry::get('BX_CACHE_MANAGER')->StartTagCache($this->_cacheDir);
                }

                while ($item = call_user_func_array(array($results, $this->_fetch_function), array())) {
                    $items[] = $item;
                }

                /* Если активирован тэгированный кэш - завершаем его */
                if (defined("BX_COMP_MANAGED_CACHE")) {
                    Zend_Registry::get('BX_CACHE_MANAGER')->EndTagCache();
                }
            }

            $results = $items;
        }

        // сохранение результатов в кэш
        if ($this->_use_cache) {
            if ($cache->StartDataCache()) {
                $cache->EndDataCache(array(
                    'data'       => $results,
                    'pager'      => $this->_pager,
                    'totalCount' => $this->_totalCount,
                ));
            }
        }

        return $results;
    }

    /**
     * Устанавливает шаблон пагинации
     *
     * @param $template
     * @return self
     */
    public function setPagerTemplate($template) {
        $this->_pagerTemplate = $template;
        return $this;
    }

    /**
     * Получение общего количество элементов подпадающих под выборку
     *
     * @return bool|int
     */
    public function getTotalCount() {
        $this->checkFetched();
        return $this->_totalCount;
    }

    /**
     * Возвращает пагинатор
     *
     * @return string
     * @throws Zend_Exception
     */
    public function getPager() {
        $this->checkFetched();
        if ($this->_pagerTemplate === false) {
            throw new Zend_Exception('Шаблон пагинации небыл установлен!');
        }
        return $this->_pager;
    }

    /**
     * Проверка что вызов GetList был осуществлен
     *
     * @throws Zend_Exception
     */
    protected function checkFetched() {
        if ($this->_second_use) {
            return;
        }
        throw new Zend_Exception('Не было вывоза GetList - нельзя получить требуемые данные!');
    }

    /**
     * Проверка что инстанс не был использован ранее
     *
     * @throws Zend_Exception
     */
    protected function checkReuse() {
        if ($this->_second_use) {
            throw new Zend_Exception('Every IBlockGetList instance must be used for query only one time!');
        }
        $this->_second_use = true;
    }
}