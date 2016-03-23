<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

CModule::IncludeModule('iblock');
CModule::IncludeModule("highloadblock");

use Bitrix\Highloadblock as HL;

/**
 * Класс исключения, порождаемый внутри модуля
 */
class IBlockException extends Exception {}


/**
 * Обертка над CIBlockElement::GetList для формирования массива параметров с заданной
 * сортировкой, фильтрацией и навигацией. Реализует кэширование.
 * Недопускается повторная выборка с использованием одного экземпляра класса.
 * Допустимо наследование для работы с другими *::GetList
 */
class IBlockGetListWrap
{

    /**
     * Данные для построения пагинации
     * @var bool|array
     */
    protected $_pageData = false;

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
     * @var array функция над которой оборачивается класс
     */
    protected $_getlist_function = array("CIBlockElement", "GetList");

    /**
     * @var string
     */
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
     * @var string
     */
    protected $_cacheDir = '/';

    protected $_iblockId = false;

    /**
     * Создание экземпляра класса выборки
     *
     * @param bool|int $iblock_id ID инфоблока над которым будет производится выборка
     * @return IBlockWrap|IBlockCount|IBlockSectionSelect
     */
    public static function instance($iblock_id = false) {
        $class_name = get_called_class();
        /**
         * @var $instance self
         */
        $instance = new $class_name();

        if ($iblock_id) {
            $instance->where(array('IBLOCK_ID' => $iblock_id));
            $instance->_cacheDir =  'iblock-id-' . $iblock_id;
            $instance->_iblockId = $iblock_id;
        }

        $instance->configureCache();

        return $instance;
    }

    /**
     * @param $dir
     * @return $this
     */
    public function setCacheDir($dir) {
        $this->_cacheDir = $dir;
        return $this;
    }

    /**
     * @return $this
     */
    public function clearCache() {
        BXClearCache(true, $this->_cacheDir);
        return $this;
    }

    protected function configureCache() {
        $options = array(
            'cacheType' => "A",
            'cacheTime' => 36000
        );

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

        $options['cacheTime'] = isset($options['cacheTime']) && intval($options['cacheTime']) > 0 ?
            intval($options['cacheTime']) :
            60 * 10;

        if ($options['cacheTime'] && $options['cacheType'] == 'Y') {
            $this->cache($options['cacheTime']);
        }
    }

    /**
     * Включает использование кэша на $time секунд
     *
     * @param $time
     * @return static
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
     * Добавление полей и свойств, по которым идет выборка
     *
     * важно! в итоговом списке полей обязательно должен остаться IBLOCK_ID. см.документацию getList
     * примечание. список полей элементов инфоблока https://dev.1c-bitrix.ru/api_help/iblock/fields.php#felement
     * примечание. допустимо указывать свойства вида PROPERTY_<PROPERTY_CODE>, где PROPERTY_CODE - мнемонический код
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
     * примечание. при формировании фильтра смотреть https://dev.1c-bitrix.ru/api_help/iblock/filter.php
     *
     * @param $fields array поля фильтрации. в простейшем случае вида: array("фильтруемое поле"=>"значения фильтра" [, ...])
     * @param bool $clear очистить текущий фильтр перед добавлением
     * @return self
     */
    public function where($fields, $clear=false)
    {
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
    public function orderBy($fields, $clear=true)
    {
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
        $results = array();
        // если включено кэширование, то попытка сразу вернуть данные
        if ($this->_use_cache) {
            if ($cache->InitCache($this->_cache_time, $cacheKey, $this->_cacheDir)) {
                $results = $cache->GetVars();
                $this->_pager = $results['pager'];
                $this->_pageData = $results['pageData'];
                $this->_totalCount = $results['totalCount'];
            }
        }

        if (!empty($results['data'])) {
            return $results['data'];
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
                    Zend_Registry::get('BX_CACHE_MANAGER')->registerTag('iblock-id-' . $this->_iblockId);
                }

                while ($item = call_user_func_array(array($results, $this->_fetch_function), array())) {
                    $items[] = $item;
                }

                /* Если активирован тэгированный кэш - завершаем его */
                if (defined("BX_COMP_MANAGED_CACHE")) {
                    Zend_Registry::get('BX_CACHE_MANAGER')->EndTagCache();
                }
            }

            $this->_pageData = array(
                'count'    => $results->NavPageCount,
                'size'     => $results->NavPageSize,
                'current'  => $results->NavPageNomer,
                'totalItemsCount' => $this->_totalCount
            );

            $results = $items;
        }

        // сохранение результатов в кэш
        if ($this->_use_cache) {
            if ($cache->StartDataCache()) {
                $cache->EndDataCache(array(
                    'data'       => $results,
                    'pager'      => $this->_pager,
                    'pageData'   => $this->_pageData,
                    'totalCount' => $this->_totalCount,
                ));
            }
        }

        return $results;
    }

    /**
     * Устанавливает шаблон пагинации
     * @param $template
     * @return self
     */
    public function setPagerTemplate($template) {
        $this->_pagerTemplate = $template;

        return $this;
    }

    /**
     * Получение общего количество элементов подпадающих под выборку
     * @return bool|int
     */
    public function getTotalCount() {
        $this->checkFetched();

        return $this->_totalCount;
    }

    /**
     * @return bool|int
     */
    public function getPageData() {
        $this->checkFetched();

        return $this->_pageData;
    }


    /**
     * Возвращает пагинатор
     * @return string
     * @throws Exception
     */
    public function getPager() {
        $this->checkFetched();

        if ($this->_pagerTemplate === false) {
            throw new Exception('Шаблон пагинации не был установлен!');
        }

        return $this->_pager;
    }

    /**
     * Проверка что вызов GetList был осуществлен
     * @throws Exception
     */
    protected function checkFetched() {

        if ($this->_second_use) {
            return;
        }

        throw new Exception('Не было вызова GetList - нельзя получить требуемые данные!');
    }

    /**
     * Проверка что инстанс не был использован ранее
     *
     * @throws IBlockException
     */
    protected function checkReuse() {
        if ($this->_second_use) {
            throw new IBlockException('Every IBlockGetList instance must be used for query only one time!');
        }

        $this->_second_use = true;
    }
}


/**
 * Класс для выборки элементов
 */
class IBlockWrap extends IBlockGetListWrap implements QueryInstance
{
    const PROPERTY_PREFIX = 'PROPERTY_';

    protected $_getlist = array(
        'arOrder' => array( // по-умолчанию сортируем по SORT и ID
            'SORT'  => 'ASC',
            'ID'    => 'DESC',
        ),
        'arFilter' => array(
            'ACTIVE' => 'Y' // по-умолчанию фильтруем активные элементы
        ),
        'arGroupBy'         => false,
        'arNavStartParams'  => false,
        'arSelectFields'    => array( // выбираемые по-умолчанию поля
            'ID',
            'NAME',
            'IBLOCK_ID', // в документации сказано, что без этого поля при указании полей выборки выборка неопределенна
            'IBLOCK_SECTION_ID'
        )
    );

    /**
     * @var string
     */
    protected $_subquery_function = "SubQuery";

    /**
     * {@inheritdoc}
     * @return IBlockWrap
     */
    public function select($fields, $clear = false) {return parent::select($fields, $clear);}

    /**
     * {@inheritdoc}
     * @return IBlockWrap
     */
    public function where($fields, $clear = false) {
        $fields = $this->_watchSubQuery($fields);
        return parent::where($fields, $clear);
    }

    /**
     * {@inheritdoc}
     * @return IBlockWrap
     */
    public function orderBy($fields, $clear = false) {return parent::orderBy($fields, $clear);}

    /**
     * @param $field
     * @return IBlockWrap
     */
    public function uniqueBy($field) {
        $this->_getlist['arGroupBy'] = array($field);
        return $this;
    }

    /**
     * Подсчет элементов по заданным полям
     * @return int
     */
    public function count() {
        $this->_getlist['arOrder'] = array();
        $this->_getlist['arGroupBy'] = array();
        return $this->fetch();
    }

    /**
     * Постраничная выборка страницы $pageIndex по $pageSize элементов
     *
     * @param $pageIndex
     * @param $pageSize
     * @return IBlockWrap
     */
    public function page($pageIndex = false, $pageSize = false) {
        $params = array(
            'bDescPageNumbering' => '',
        );

        if ($pageIndex !== false) {
            $params['iNumPage'] = $pageIndex;
        }

        if ($pageSize !== false) {
            $params['nPageSize'] = $pageSize;
        } else {
            /*БИТРИКСА ПАРАМЕТР*/
            $sizen = 'SIZEN_' . (1 + $GLOBALS['NavNum']);
            if (array_key_exists($sizen, $_REQUEST)) {
                $params['nPageSize'] = $_REQUEST[$sizen];
            }
        }

        $this->_getlist['arNavStartParams'] = $params;

        return $this;
    }

    /**
     * Ручное задание navParams
     * @param $navParams
     * @return IBlockWrap
     */
    public function navParams($navParams) {
        $this->_getlist['arNavStartParams'] = $navParams;
        return $this;
    }

    /**
     * Ограничивает общее количество элементов участвующих в выборке
     *
     * @param $limit
     * @return IBlockWrap
     */
    public function limit($limit) {
        $this->_getlist['arNavStartParams'] = array(
            'nTopCount' => $limit
        );
        return $this;
    }

    /**
     * Выборка $count элементов с каждой стороны от элемента $element_id
     *
     * @param $element_id int ID элемента относительно которого будут выбраны элементы
     * @param $count int количество элементов с каждой стороны
     * @return IBlockWrap
     */
    public function nearElement($element_id, $count = 1) {
        $this->_getlist['arNavStartParams'] = array(
            'nPageSize' => $count,
            'nElementID' => $element_id
        );
        return $this;
    }

    /**
     * Итоговая выборка элементов
     *
     * @param bool $collectProperties флаг, нужно ли собирать свойства как в компонентах
     *
     * @return array
     */
    public function fetch($collectProperties = false) {
        $result = parent::callGetList();

        if ($collectProperties && is_array($result)) {
            $this->collectProperties($result, false);
            $this->collectProperties($result, true);
        }

        return $result;
    }

    /**
     * Сборка свойств как в компонентах
     * @param $items
     * @param bool $displayProperties
     */
    protected function collectProperties(&$items, $displayProperties = false) {
        $prefix = self::PROPERTY_PREFIX;
        $collectArrayName = $displayProperties ? 'DISPLAY_PROPERTIES' : 'PROPERTIES';
        $displayPrefix = $displayProperties ? '~' : '';

        $propertiesNames = $this->_getlist['arSelectFields'];
        usort($propertiesNames, function ($a, $b) {
            return strlen($a) < strlen($b);
        });

        foreach ($propertiesNames as $fieldName) {
            // проверка что это свойство
            if (strncasecmp($fieldName, $prefix, strlen($prefix)) !== 0) {
                continue;
            }
            // обход всех элементов и сборка свойства в каждом элементе в отдельный массив
            foreach ($items as &$item) {
                $properties = array();
                foreach ($item as $key => $value) {
                    if (strncasecmp($displayPrefix . $fieldName, $key, strlen($fieldName)) !== 0) {
                        continue;
                    }

                    $shortFieldName = str_replace($prefix, '', strtoupper($fieldName));
                    $shortKey = str_replace($displayPrefix . $fieldName . '_', '', $key);

                    if (strcmp('ENUM_ID', $shortKey) === 0) {
                        $value = CIBlockPropertyEnum::GetList(
                            array(),
                            array('ID' => $value)
                        );
                        $value = $value->Fetch();
                        $value = $value['XML_ID'];
                    }

                    $properties[$shortFieldName][$shortKey] = $value;
                    unset ($item[$key]);
                }

                if (!array_key_exists($collectArrayName, $item)) {
                    $item[$collectArrayName] = array();
                }
                $item[$collectArrayName] = array_merge(
                    $item[$collectArrayName],
                    $properties
                );
            }
        }
    }

    /**
     * подзапрос
     *
     * @throws Exception
     * @return CIBlockElement
     */
    public function subQuery() {
        $this->checkReuse();

        $fieldList = $this->_getlist['arSelectFields'];
        if (count($fieldList) > 1) {
            throw new Exception('Для построения подзапроса должно быть указано только одно поле [select]!');
        }

        $field = array_shift($fieldList);

        $callObject = new $this->_getlist_function[0];

        /* @var $results CIBlockElement */
        return call_user_func_array(
            array($callObject, $this->_subquery_function),
            array(
                $field,
                $this->_getlist['arFilter']
            )
        );
    }

    /**
     * поиск экземпляров IBlockHelper в фильтре для построения из них подзапросов
     * @param $fields
     *
     * @return array
     * @throws Exception
     */
    private function _watchSubQuery($fields) {
        foreach ($fields as $key => $field) {
            if ($field instanceof IBlockWrap) {
                $fields[$key] = $field->subQuery();
            } else {
                if (is_array($field)) {
                    $fields[$key] = self::_watchSubQuery($field);
                }
            }
        }

        return $fields;
    }

    public function add($data = array()) {
        if (!array_key_exists('IBLOCK_ID', $data)) {
            $data['IBLOCK_ID'] = $this->_iblockId;
        }

        $ibe = new CIBlockElement();
        $result = $ibe->Add($data);
        Zend_Registry::get('BX_CACHE_MANAGER')->ClearByTag("iblock-id-" . $this->_iblockId);

        return $result;
    }

    public function update($id, $data = array()) {
        $ibe = new CIBlockElement();
        $result = $ibe->Update($id, $data);
        Zend_Registry::get('BX_CACHE_MANAGER')->ClearByTag("iblock-id-" . $this->_iblockId);

        return $result;
    }

    public function remove($id) {
        CIBlockElement::Delete($id);
        Zend_Registry::get('BX_CACHE_MANAGER')->ClearByTag("iblock-id-" . $this->_iblockId);
    }
}


/**
 * Класс для подсчета количества элементов
 */
class IBlockCount extends IBlockGetListWrap
{
    /**
     * {@inheritdoc}
     */
    public function where($fields, $clear=false) {return parent::where($fields, $clear);}

    /**
     * Подсчет элементов по заданным полям
     *
     * @param $fields
     * @return array
     */
    public function countBy($fields)
    {
        $this->_getlist['arGroupBy'] = $fields;

        return parent::callGetList();
    }

    /**
     * Подчет общего количества элементов по заданному фильтру
     *
     * @return int
     */
    public function countTotal()
    {
        $this->_getlist['arGroupBy'] = array();

        return parent::callGetList();
    }
}


/**
 * Класс для выборки разделов
 */
class IBlockSectionSelect extends IBlockGetListWrap
{
    // функция над которой оборачивается класс
    protected $_getlist_function = array("CIBlockSection", "GetList");

    // параметры для передачи в getList
    protected $_getlist = array(
        "arOrder"        => array("SORT" => "ASC"),
        "arFilter"       => array("ACTIVE" => "Y"),
        "bIncCnt"        => false,
        "arSelectFields" => array()
    );

    /**
     * Функция для добавления к стандартным выбираемым полям пользовательского поля
     *
     * предупреждение! используется не документированная официально возможность для проброса выбираемых полей
     * замечание. этот модификатор должен вызываться один раз с перечислением всех необходимых пользовательских полей.
     *            это нужно для большей устойчивости и предсказуемости работы выборки
     *
     * @param $fields array пользовательский полей
     * @param $clear
     * @return self
     */
    public function select($fields, $clear = false) {return parent::select($fields, true);}

    /**
     * @param array $fields
     * @param bool $clear
     * @return IBlockSectionSelect
     */
    public function where($fields, $clear=false) {return parent::where($fields, $clear);}

    /**
     * @param array $fields
     * @param bool $clear
     * @return IBlockSectionSelect
     */
    public function orderBy($fields, $clear=false) {return parent::orderBy($fields, $clear);}

    /**
     * Включает подсчет элементов в разделе которое будет возвращено в поле ELEMENT_CNT каждого раздела
     *
     * @param bool $withSubitems
     * @param bool $onlyActive
     *
     * @return IBlockSectionSelect
     */
    public function calcCount($withSubitems = false, $onlyActive = true) {
        $this->_getlist['bIncCnt'] = true;

        return $this->where(array(
            'ELEMENT_SUBSECTIONS' => $withSubitems ? 'Y' : 'N',
            'CNT_ACTIVE'          => $onlyActive ? 'Y' : 'N',
        ));
    }

    /**
     * Ограничивает общее количество элементов участвующих в выборке
     *
     * @param int|bool $limit количество
     * @return IBlockWrap
     */
    public function limit($limit = false) {
        if ($limit) {
            $this->_getlist['arNavStartParams'] = array(
                'iNumPage' => 1,
                'nPageSize' => $limit,
            );
        }

        return $this;
    }

    /**
     * Итоговая выборка разделов
     * @return array
     */
    public function fetch() {
        $result = parent::callGetList();

        return $result;
    }
}


/**
 * Класс для манипуляций над массивами элементов инфоблока
 */
class IBlockHelper
{
    const DEBUG = false;

    /**
     * Загрузка файлов
     * @param $ids
     * @return array
     */
    static function loadFilesArray(&$ids)
    {
        if (empty($ids)) {
            return array();
        }

        $filter = is_array($ids)
            ? array("@ID" => implode(",", $ids))
            : array("ID" => $ids);

        $cFile = new CFile();
        $iterator = $cFile->GetList(array(), $filter);

        $files = array();
        while ($file = $iterator->Fetch()) {
            $file['SRC'] = $cFile->GetFileSRC($file);
            $files[$file['ID']] = $file;
        }

        $ids = $files;
        return $ids;
    }


    /**
     * Загрузка файлов в массиве элементов
     *
     * @param array $elements массив элементов инфоблока
     * @param array $keys ключи в элементе где хранятся ID файла
     * @return array результирующий массив элементов с файлами в качестве значений по ключам
     */
    static function loadFiles(&$elements, $keys = array('PREVIEW_PICTURE', 'DETAIL_PICTURE')) {
        $cFile = new CFile();

        foreach ($keys as $key) {
            // Собираем ID файлов
            $ids = array();
            foreach ($elements as &$item) {
                if (!array_key_exists($key, $item)) {
                    continue;
                }
                $value = $item[$key];

                if (!is_array($value)) {
                    $value = array($value);
                }
                $ids = array_merge($ids, $value);
            }


            if (empty($ids)) {
                continue;
            }


            // получаем их данные
            $iterator = $cFile->GetList(array(), array("@ID" => implode(",", $ids)));

            $files = array();
            while ($file = $iterator->Fetch()) {
                $files[$file['ID']] = $file;
            }


            // размещаем файлы по ключам
            foreach ($elements as &$item) {
                if (!array_key_exists($key, $item)) {
                    continue;
                }

                $value = $item[$key];
                $isSingle = !is_array($value);
                if ($isSingle) {
                    $value = array($value);
                }

                foreach ($value as &$multItem) {
                    $fileId = $multItem;
                    if (!array_key_exists($fileId, $files)) {
                        continue;
                    }
                    $file = $files[$fileId];
                    if (empty($file)) {

                        continue;
                    }
                    $file['SRC'] = $cFile->GetFileSRC($file);
                    $multItem = $file;
                }

                if ($isSingle) {

                    $value = reset($value);
                }
                $item[$key] = $value;
            }
        }

        return $elements;
    }

    /**
     * Пережатие набора изображений
     *
     * предупреждение. стуктуры файлов должны быть загружены, как вариант, функцией IBlockHelper::loadFiles
     *
     * @param array $elements   элементы инфоблока
     * @param int $width        новая максимальная ширина
     * @param int $height       новая максимальная высота
     * @param bool $crop        если true, то изображение будет вписано в размеры, иначе будет масштабировано по
     *                          наименьшей стороне
     * @param bool $default     изображение по-умолчанию
     * @param array $keys       ключи в элементах массива где хранятся структуры файлов
     */
    static function resizeImages(&$elements,
                                 $width, $height, $crop=false, $default=false,
                                 $keys=array('PREVIEW_PICTURE', 'DETAIL_PICTURE')) {
        foreach ($keys as $key) {
            foreach ($elements as $index => $item) {
                if (!array_key_exists($key, $item) || empty($item[$key]) || !is_array($item[$key])) {
                    continue;
                }
                $new_image = IBlockHelper::resizeImage(
                    $item[$key],
                    $width, $height,
                    $crop,
                    $default
                );
                if ($new_image !== false) {
                    $elements[$index][$key]['SRC'] = $new_image;
                }
            }
        }
    }

    /**
     * Пережимает одиночную картинку
     *
     * @param array $file       структура файла битрикса
     * @param int $width        ширина изображения
     * @param int $height       высота изображения
     * @param bool $crop        если true, то изображение будет вписано в размеры, иначе будет масштабировано по
     *                          наименьшей стороне
     * @param bool $default     изображение по-умолчанию
     *
     * @return bool|string      URL пережатого изображения
     */
    static function resizeImage($file, $width, $height, $crop = false, $default = false) {
        $imageType = array(
            'WIDTH'         => $width,
            'HEIGHT'        => $height,
            'RESIZE_MODE'   => $crop ? 'STRONG' : 'CLASSIC'
        );
        $resultImg = false;

        if (!is_array($file)) {
            if (self::DEBUG) {
                // Debug::toDefaultLog('ERROR Попытка масштабировать пустую структуру');
            }
        } elseif (
            (array_key_exists('SRC', $file) && array_key_exists('CONTENT_TYPE', $file)) ||
            (array_key_exists('tmp_name', $file) && array_key_exists('type', $file))
        ) {
            if (!array_key_exists('SRC', $file)) {
                $file['SRC'] = $file['tmp_name'];
                $file['CONTENT_TYPE'] = $file['type'];
            }

            $src = $file['SRC'];
            if (strpos($src, P_DR) === 0) {
                $src = str_replace(P_DR, '', $src);
            }

            $imageType = array_merge(
                $imageType,
                array(
                    'INPUT_FILE'    => $src,
                    'TYPE'          => $file['CONTENT_TYPE']
                )
            );

            global $ucResizeImg;
            $resultImg = $ucResizeImg->GetResized($imageType);
            if (empty($resultImg) || $resultImg == 'error') {
                if (self::DEBUG) {
                    // Debug::toDefaultLog('ERROR Ошибка пережатия картинки');
                }
                $resultImg = false;
            }
        } else {
            if (self::DEBUG) {
                // Debug::toDefaultLog('WARNING Получаем структуру файла с пустым SRC или CONTENT_TYPE');
            }
        }

        if (($resultImg === false) && ($default !== false)) {
            $resultImg = $default;
        }

        return $resultImg;
    }

    /**
     * Извлекает массивы значений по ключам из массивов элементов
     *
     * <code>
     * $elms = array(
     *   array('year' => 2000, 'id' => 1),
     *   array('year' => 2000, 'id' => 2),
     *   array('year' => 2001, 'id' => 3),
     *   array('year' => 2001, 'id' => 4),
     * );
     *
     * IBlockHelper::extractFields($elms, array('year', 'id'), false);
     * array(
     *   'year' => array(2000, 2000, 2001, 2001), // дубли остаются
     *   'id' => array(1, 2, 3, 4)
     * );
     *
     * IBlockHelper::extractFields($elms, array('year', 'id'), true);
     * array(
     *   'year' => array(2000, 2001), // оставлены только уникальные значения
     *   'id' => array(1, 2, 3, 4)
     * );
     * </code>
     *
     * @param array $elements массив элементов
     * @param array $keys ключи по которым извлечь значения
     * @param bool $unique оставлять только уникальные значения по каждому ключу
     * @param bool $collect свалить значения по всем ключам в кучу
     * @return array
     */
    static function extractFields($elements, $keys = array('ID'), $unique = true, $collect = false) {
        if (empty($elements)) {
            return array();
        }

        $values = array();
        foreach ($keys as $key) {
            foreach ($elements as $element) {
                if (!array_key_exists($key, $element)) {
                    continue;
                }
                $values[$key][] = $element[$key];
            }
            if ($unique) {
                $values[$key] = array_unique($values[$key]);
            }
        }

        if ($collect) {
            $new_values = array();
            foreach ($keys as $key) {
                if (is_array($values[$key])) {
                    $new_values = array_merge($new_values, $values[$key]);
                }
            }
            $values = $unique ? array_unique($new_values) : $new_values;
        }

        return $values;
    }

    /**
     * Удаление пустых разделов
     *
     * замечание. использовать только для выборок разделов с подсчетом количества дочерних элементов (calcCount)
     *
     * @param array $sections разделы возвращенные через класс IBlockSectionSelect
     * @throws IBlockException
     */
    static function filterEmptySections(&$sections) {
        foreach ($sections as $index => $section) {
            if (!array_key_exists('ELEMENT_CNT', $section)) {
                throw new IBlockException('You should use IBlockSectionSelect::calcCount before fetching elements!');
            }

            if (!$section['ELEMENT_CNT']) {
                unset($sections[$index]);
            }
        }
    }

    /**
     * @param $elements
     */
    static function filterEmptyFields(&$elements) {
        foreach ($elements as $key => $value) {
            if (is_array($value)) {
                self::filterEmptyFields($elements[$key]);
            }
            if (empty($elements[$key])) {
                unset($elements[$key]);
            }
        }
    }

    /**
     * Группировка элементов по значениию поля
     *
     * <code>
     * $elms = array(
     *   array('year' => 2000, 'id' => 1),
     *   array('year' => 2000, 'id' => 2),
     *   array('year' => 2001, 'id' => 3),
     *   array('year' => 2001, 'id' => 4),
     * );
     * IBlockHelper::groupByField($elms);
     * array(
     *   '2000' => array(
     *      array('year' => 2000, 'id' => 1),
     *      array('year' => 2000, 'id' => 2),
     *   ),
     *   '2001' => array(
     *      array('year' => 2001, 'id' => 3),
     *      array('year' => 2001, 'id' => 4),
     *   )
     * );
     * </code>
     *
     * @param $items
     * @param $field
     * @return array
     */
    static function groupByField($items, $field) {
        $result = array();
        foreach ($items as $id => $item) {
            $key = is_object($item) ? $item->$field :$item[$field];
            $result[$key][$id] = $item;
        }

        return $result;
    }

    /**
     * Перенос значения поля в качестве ключа массива элементов
     *
     * <code>
     * $elms = array(
     *   0 => array('year' => 2001, 'id' => 1),
     *   1 => array('year' => 2002, 'id' => 2),
     *   2 => array('year' => 2003, 'id' => 3),
     *   3 => array('year' => 2004, 'id' => 4),
     * );
     * IBlockHelper::fieldToKey($elms);
     * array(
     *   2001 => array('year' => 2001, 'id' => 1),
     *   2002 => array('year' => 2002, 'id' => 2),
     *   2003 => array('year' => 2003, 'id' => 3),
     *   2004 => array('year' => 2004, 'id' => 4),
     * );
     * </code>
     *
     * @param array $items элементы инфоблока
     * @param string $field имя ключа
     */
    static function fieldToKey(&$items, $field) {
        $result = array();
        if (!empty($items)) {
            foreach ($items as $item) {
                $result[is_array($item) ? $item[$field] : $item->$field] = $item;
            }
        }

        $items = $result;
    }

    /**
     * Для каждого элемента сложного массива формирует ключ-значение
     *
     * <code>
     * $elms = array(
     *   0 => array('id' => 1, 'year' => 2001),
     *   1 => array('id' => 2, 'year' => 2002),
     *   2 => array('id' => 3, 'year' => 2003),
     *   3 => array('id' => 4, 'year' => 2004),
     * );
     * IBlockHelper::fieldByKey($elms, 'id', 'year');
     * array(
     *   1 => 2001,
     *   2 => 2002,
     *   3 => 2003,
     *   4 => 2004,
     * );
     * </code>
     *
     * @param array $items массив ассоциативных массивов
     * @param string $key ключ индекса
     * @param string $field ключ значения
     * @return array
     */
    static function fieldByKey($items, $key, $field) {
        $result = array();
        if (!empty($items)) {
            foreach ($items as $item) {
                $result[$item[$key]] = $item[$field];
            }
        }

        return $result;
    }

    /**
     * Оставляет только нужные ключи в каждом элементе массива
     *
     * <code>
     * $elms = array(
     *   array('year' => 2001, 'id' => 1),
     *   array('year' => 2002, 'id' => 2),
     *   array('year' => 2003, 'id' => 3),
     *   array('year' => 2004, 'id' => 4),
     * );
     * IBlockHelper::filterFields($elms, array('year'));
     * array(
     *   array('year' => 2001),
     *   array('year' => 2002),
     *   array('year' => 2003),
     *   array('year' => 2004),
     * );
     * </code>
     *
     * @param array $items элементы инфоблока
     * @param array $keys массив ключей которые необходимо оставить в каждом элементе
     */
    static function filterFields(&$items, $keys) {
        foreach ($items as &$item) {
            $new_item = array();
            foreach ($keys as $key) {
                if (!array_key_exists($key, $item)) {
                    continue;
                }
                $new_item[$key] = $item[$key];
            }
            $item = $new_item;
        }
    }

    /**
     * @param $items
     * @param $key
     * @return array
     */
    static function extractByKeyFields($items, $key) {
        $result = array();
        foreach ($items as $item) {
            if (!$item[$key]) {
                continue;
            }
            $result[] = $item[$key];
        }

        return array_map("unserialize", array_unique(array_map("serialize", $result)));
    }

    static function spliteParts($array, $count = 2) {
        if ($count <= 0) {
            throw new Zend_Exception();
        }

        $index = 0;
        $parts = array_map(function () {return array();}, range(1, $count));

        $count = count($array) / $count;
        foreach ($array as $key => $item) {
            $parts[(int)floor($index++ / $count)][$key] = $item;
        }

        return $parts;
    }

    static function linearJoin($parts) {
        $result = array();
        while (true) {
            $shifted = false;
            foreach ($parts as &$part) {
                if (empty($part)) {
                    continue;
                }
                $shifted = true;
                $result[] = array_shift($part);
            }
            if (!$shifted) {
                break;
            }
        }

        return $result;
    }

    /**
     * Takes file structure:
     * array(
     *     [ID] => 98
     *     [TIMESTAMP_X] => 01.04.2014 15:19:25
     *     [MODULE_ID] => iblock
     *     [HEIGHT] => 327
     *     [WIDTH] => 262
     *     [FILE_SIZE] => 30120
     *     [CONTENT_TYPE] => image/jpeg
     *     [SUBDIR] => iblock/6e2
     *     [FILE_NAME] => 6e2f3782f3c6bc3313615b3545a4229c.jpg
     *     [ORIGINAL_NAME] => bot-7.jpg
     *     [DESCRIPTION] =>
     *     [HANDLER_ID] =>
     *     [SRC] => /upload/resized/274b/274b1b51c4a751331b304239708a59ed.jpg
     * )
     *
     * and return only important fields from it:
     * array(
     *     [id] => 98
     *     [width] => 262
     *     [height] => 327
     *     [src] => /upload/resized/274b/274b1b51c4a751331b304239708a59ed.jpg
     * )
     *
     * File structure should include ID, SRC, WIDTH & HEIGHT fields
     *
     * @param array $file file structure
     * @return array
     */
    static function getImageFields($file) {
        if (empty($file['ID']) || empty($file['SRC']) || empty($file['WIDTH']) || empty($file['HEIGHT'])) {
            return array();
        }

        $result = array(
            'id'     => $file['ID'],
            'src'    => $file['SRC'],
            'width'  => $file['WIDTH'],
            'height' => $file['HEIGHT'],
        );

        return $result;
    }
}

interface QueryInstance {
    public static function instance();

    public function add($data = array());

    public function select($fields, $clear = false);

    public function where($fields, $clear=false);

    public function fetch();

    public function update($id, $data = array());

    public function remove($id);
}

/**
 * Класс работы с Highload ИБ
 * Class Highload
 */
class Highload extends IBlockGetListWrap implements QueryInstance
{

    private $_hiblockId;
    private $_hlblock;

    /* @var \Bitrix\Main\Entity\Base $_entity */
    private $_entity;

    protected $_getlist = array(
        'order'         => array(),
        'filter'        => array(),
        'group'         => array(),
        'offset'        => false,
        'limit'         => false,
        'runtime'       => array(),
        'select'        => array('*')
    );

    /**
     * @var bool флаг использования кэша
     */
    private $_use_cache = true;

    /**
     * @var bool время кэширования. используется только если включен $_use_cache
     */
    private $_cache_time = 3600;

    /**
     * @param bool|int $iblockId
     *
     * @return Highload
     */
    public static function instance($iblockId = false) {
        if (!$iblockId) {
            return null;
        }

        static $_hlblockCache = array();

        $class_name = get_called_class();
        $instance = new $class_name();

        if (!$_hlblockCache[$iblockId]) {
            $allHib = HL\HighloadBlockTable::getList()->fetchAll();
            foreach ($allHib as $hib) {
                $_hlblockCache[$hib['ID']] = $hib;
            }
        }
        $instance->_hiblockId = $iblockId;
        $instance->_hlblock = $_hlblockCache[$iblockId];
        $instance->_entity  = HL\HighloadBlockTable::compileEntity($instance->_hlblock);
        $instance->configureCache();

        return $instance;
    }

    /**
     * Добавление полей и свойств, по которым идет выборка
     *
     * @param $fields array поля которые нужно выбирать
     * @param bool $clear удалить текущий список полей перед добавлением новых
     * @return Highload
     */
    public function select($fields, $clear = false) {
        if ($clear) {
            $this->_getlist['select'] = array();
        }

        $this->_getlist['select'] = array_merge(
            $this->_getlist['select'],
            $fields
        );

        return $this;
    }

    /**
     * @param $fields
     * @param bool $clear
     */
    public function group($fields, $clear = false) {
        if ($clear) {
            $this->_getlist['group'] = array();
        }

        $this->_getlist['group'] = array_merge(
            $this->_getlist['group'],
            $fields
        );
    }

    /**
     * Добавление параметров для фильтрации элементов
     *
     * @param $fields array поля фильтрации. в простейшем случае вида: array("фильтруемое поле"=>"значения фильтра" [, ...])
     * @param bool $clear очистить текущий фильтр перед добавлением
     * @return Highload
     */
    public function where($fields, $clear=false) {
        if ($clear) {
            $this->_getlist['filter'] = array();
        }

        $this->_getlist['filter'] = array_merge(
            $this->_getlist['filter'],
            $fields
        );

        return $this;
    }

    /**
     * Добавление полей сортировки
     *
     * @param $fields array поля сортировки вида: array(by1=>order1[, by2=>order2 [, ..]])
     * @param bool $clear очистить текущий список полей сортировки перед добавлением новых
     * @return Highload
     */
    public function orderBy($fields, $clear=true) {
        if ($clear) {
            $this->_getlist['order'] = array();
        }

        $this->_getlist['order'] = array_merge(
            $this->_getlist['order'],
            $fields
        );

        return $this;
    }

    /**
     * Ограничивает общее количество элементов участвующих в выборке
     *
     * @param $limit
     * @return Highload
     */
    public function limit($limit) {
        $this->_getlist['limit'] = $limit;
        return $this;
    }

    /**
     * Смещение начала выборки
     *
     * @param $offset
     * @return Highload
     */
    public function offset($offset) {
        $this->_getlist['offset'] = $offset;
        return $this;
    }

    /**
     * Нужно ли считать количество записей выборки
     *
     * @return Highload
     */
    public function countTotal() {
        $this->_getlist['runtime'] = array(
            'CNT' => array(
                'expression' => array('COUNT(*)'),
                'data_type'  => 'integer'
            )
        );
        $this->_getlist['select'] = array('CNT');

        return $this;
    }

    /**
     * Нужно ли считать количество записей выборки
     *
     * @param $field
     * @return Highload
     */
    public function summBy($field) {
        $this->_getlist['runtime'] = array(
            'SUM' => array(
                'expression' => array('SUM(' . $field . ')'),
                'data_type'  => 'integer'
            )
        );
        $this->_getlist['select'] = array('SUM');

        return $this;
    }

    /**
     * Вызов низкоуровневой функции битрикса для получения данных
     *
     * @return array|bool
     */
    protected function callGetList() {
        // проверка повторного использования
        $this->checkReuse();

        // объект кэша должен быть доступен из двух скоупов второго уровня. чтобы все видели что используется,
        // объявляется на первом уровне
        $cache = $this->_use_cache ? new CPHPCache() : null;

        $cacheKey = json_encode(array($this->_getlist, $this->_hiblockId));

        // если включено кэширование, то попытка сразу вернуть данные
        if ($this->_use_cache) {
            if ($cache->InitCache($this->_cache_time, $cacheKey)) {
                $results = $cache->GetVars();
                return $results['data'];
            }
        }

        // вызов низкоуровневой функции битрикса для получения данных
        $entityData = $this->_entity->getDataClass();
        $results    = $entityData::getList($this->_getlist);
        // итерирование результатов если нужно
        if (is_a($results, 'Bitrix\Main\DB\MysqlResult')) {

            /* Если активирован тэгированный кэш - стартуем его */
            if (defined("BX_COMP_MANAGED_CACHE")) {
                Zend_Registry::get('BX_CACHE_MANAGER')->StartTagCache($this->_cacheDir);
            }
            // извлечение элементов
            $items   = $results->fetchAll();
            Zend_Registry::get('BX_CACHE_MANAGER')->RegisterTag("highload_id_" . $this->_hlblock);
            $results = $items;

            /* Если активирован тэгированный кэш - стартуем его */
            if (defined("BX_COMP_MANAGED_CACHE")) {
                Zend_Registry::get('BX_CACHE_MANAGER')->EndTagCache();
            }
        }


        // сохранение результатов в кэш
        if ($this->_use_cache) {
            if ($cache->StartDataCache()) {
                $cache->EndDataCache(array(
                    'data' => $results
                ));
            }
        }

        return $results;
    }

    /**
     * Выборка элементов
     *
     * @return array|bool
     */
    public function fetch() {
        return $this->callGetList();
    }

    /**
     * Добавление элемента
     *
     * @param array $data
     * @return bool
     */
    public function add($data = array()) {
        if (!$data) {
            return false;
        }

        $entityData = $this->_entity->getDataClass();
        $result     = $entityData::add($data);

        Zend_Registry::get('BX_CACHE_MANAGER')->ClearByTag("highload_id_" . $this->_hlblock);

        return $result->getId();
    }

    /**
     * Обновление элемента
     *
     * @param $id
     * @param array $data
     * @param bool $updateCache
     * @return bool
     * @throws Zend_Exception
     */
    public function update($id, $data = array(), $updateCache = true) {
        if (!$data) {
            return false;
        }

        $entityData = $this->_entity->getDataClass();
        $result     = $entityData::update($id, $data);

        if ($updateCache) {
            Zend_Registry::get('BX_CACHE_MANAGER')->ClearByTag("highload_id_" . $this->_hlblock);
        }

        return $result;
    }

    /**
     * Удаление элемента по ID
     * @param $id
     * @return bool
     */
    public function remove($id) {
        if (!$id) {
            return false;
        }

        $entityData = $this->_entity->getDataClass();
        $result = false;
        if (is_array($id)) {
            foreach ($id as $el) {
                $result = $entityData::delete($el);
            }
        } else {
            $result = $entityData::delete($id);
        }

        Zend_Registry::get('BX_CACHE_MANAGER')->ClearByTag("highload_id_" . $this->_hlblock);

        return $result->isSuccess();
    }
}
