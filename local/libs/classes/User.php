<?

/**
 * Класс для выборки пользователей
 */
class UserSelect extends IBlockGetListWrap implements QueryInstance
{
    private $_use_cache = false;
    private $_cache_time = false;

    // функция над которой оборачивается класс
    protected $_getlist_function = ["CUser", "GetList"];

    // параметры для передачи в getList
    protected $_getlist = [
        "arOrder"    => ["timestamp_x" => "desc"],
        '_fakeOrder' => "",
        "arFilter"   => ["ACTIVE" => "Y", 'UF_DELETED' => false],
        "arParams"   => []
    ];

    /**
     * Создание экземпляра класса выборки
     * @return UserSelect
     * @param bool $iblock_id
     * @return UserSelect
     */
    public static function instance($iblock_id = false) {
        $class_name = get_called_class();
        /**
         * @var $instance self
         */
        $instance = new $class_name();
        $instance->_cacheDir =  'user_select';

        $instance->configureCache();

        return $instance;
    }

    /**
     * @param array $fields
     * @param bool $clear
     * @return $this|IBlockGetListWrap
     */
    public function select($fields, $clear = false) {
        if ($clear) {
            unset($this->_getlist["arParams"]['SELECT']);
            unset($this->_getlist["arParams"]['FIELDS']);
        }

        $_fields = [];
        $_select = [];

        foreach ($fields as $field) {
            if (strlen($field) > 3 && substr($field, 0, 3) == 'UF_') {
                $_select[] = $field;
            } else {
                $_fields[] = $field;
            }
        }

        if (!empty($_select)) {
            if (isset($this->_getlist["arParams"]['SELECT'])) {
                $this->_getlist["arParams"]['SELECT'] = array_merge($this->_getlist["arParams"]['SELECT'], $_select);
            } else {
                $this->_getlist["arParams"]['SELECT'] = $_select;
            }
        }

        if (!empty($_fields)) {
            if (isset($this->_getlist["arParams"]['FIELDS'])) {
                $this->_getlist["arParams"]['FIELDS'] = array_merge($this->_getlist["arParams"]['FIELDS'], $_fields);
            } else {
                $this->_getlist["arParams"]['FIELDS'] = $_fields;
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return self
     */
    public function where($fields, $clear=false) {
        $fields = $this->_where($fields);

        return parent::where($fields, $clear);
    }

    /**
     * @param $fields
     * @return $fields
     */
    private function _where($fields) {
        foreach ($fields as $key => $field) {
            if ($key == 'ID' && is_array($field)) {
                $fields[$key] = implode(" | ", array_filter($field));
            } else {
                if (is_array($field)) {
                    $fields[$key] = self::_where($field);
                }
            }
        }

        return $fields;
    }

    /**
     * {@inheritdoc}
     *
     * @return self
     */
    public function orderBy($fields, $clear=false) {return parent::orderBy($fields, true);}

    /**
     * Постраничная выборка страницы $pageIndex по $pageSize элементов
     *
     * @param $pageIndex
     * @param $pageSize
     * @return $this
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

        $this->_getlist['arParams']['NAV_PARAMS'] = $params;

        return $this;
    }

    /**
     * Итоговая выборка разделов
     * @return array
     */
    public function fetch() {
        $result = $this->callGetList();

        return $result;
    }

    /**
     * Посчитать количество
     * @return mixed
     */
    public function count() {
        $params['nPageSize'] = 1;
        $this->_getlist['arParams']['NAV_PARAMS'] = $params;

        $callObject = new $this->_getlist_function[0];

        $results = $callObject->{$this->_getlist_function[1]}($this->_getlist['arOrder'], $this->_getlist['_fakeOrder'], $this->_getlist['arFilter'], $this->_getlist['arParams']);
        return $results->NavRecordCount;
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
        /* @var $results CDBResult */
        $results = $callObject->{$this->_getlist_function[1]}($this->_getlist['arOrder'], $this->_getlist['_fakeOrder'], $this->_getlist['arFilter'], $this->_getlist['arParams']);

        // итерирование результатов если нужно
        if (is_a($results, 'CDBResult')) {
            // есть возможность узнать и сохранить общее количество элементов и сгенерировать пагинатор
            $this->_totalCount = $results->NavRecordCount;
            if ($this->_pagerTemplate !== false) {
                $this->_pager = $results->GetPageNavString('', $this->_pagerTemplate);
            }

            // извлечение элементов
            $pageExist  = true;
            $pageSize   = $this->_getlist['arParams']['NAV_PARAMS'] ? $this->_getlist['arParams']['NAV_PARAMS']['nPageSize'] : null;
            $pageNumber = $this->_getlist['arParams']['NAV_PARAMS'] ? $this->_getlist['arParams']['NAV_PARAMS']['iNumPage'] : null;
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

    public function subQuery() {
        $fieldList = array_merge(
            $this->_getlist["arParams"]['FIELDS']
                ? $this->_getlist["arParams"]['FIELDS']
                : [],
            $this->_getlist["arParams"]['SELECT']
                ? $this->_getlist["arParams"]['SELECT']
                : []
        );

        if (count($fieldList) !== 1) {
            throw new IBlockException('run subQuery fields [' . implode(", ", $fieldList) . ']');
        }

        $column = array_shift($fieldList);

        if ($column == 'UF_*') {
            throw new IBlockException('run subQuery field ' . $column);
        }

        $result = $this->fetch();
        foreach ($result as &$item) {
            $item = $item[$column];
        }

        return $result;
    }

    /**
     * Ограничивает общее количество элементов участвующих в выборке
     *
     * @param $limit
     * @return UserSelect
     */
    public function limit($limit) {
        $this->_getlist['arParams']['NAV_PARAMS'] = array(
            'nTopCount' => $limit
        );
        return $this;
    }

    public function add($data = array()) {

    }

    public function update($id, $data = array()) {

    }

    public function remove($id) {

    }
}
