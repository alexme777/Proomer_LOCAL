<?php
use Bitrix\Highloadblock as HL;

/**
 * Класс работы с Highload ИБ
 *
 * @package files
 * @subpackage classes
 *
 * Class Sibirix_Model_Bitrix_IblockHelper_Highload
 */
class Sibirix_Model_Bitrix_IblockHelper_Highload extends Sibirix_Model_Bitrix_IblockHelper_Abstract {
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
    private $_use_cache = false;

    /**
     * @var bool время кэширования. используется только если включен $_use_cache
     */
    private $_cache_time = false;

    /**
     *
     */
    private function __construct() {
        CModule::IncludeModule("highloadblock");

    }

    /**
     * @param bool|int $iblockId
     *
     * @return Sibirix_Model_Bitrix_IblockHelper_Highload
     */
    public static function instance($iblockId) {
        if (!$iblockId) {
            return null;
        }

        static $_hlblockCache = [];

        $class_name = get_called_class();
        $instance   = new $class_name();

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
     * @return Sibirix_Model_Bitrix_IblockHelper_Highload
     */
    public function select($fields, $clear = false) {
        if ($clear) {
            $this->_getlist['select'] = array();
        }

        $this->_getlist['select'] = array_merge($this->_getlist['select'], $fields);

        return $this;
    }

    /**
     * Добавление параметров для фильтрации элементов
     *
     * @param $fields array поля фильтрации. в простейшем случае вида: array("фильтруемое поле"=>"значения фильтра" [, ...])
     * @param bool $clear очистить текущий фильтр перед добавлением
     * @return Sibirix_Model_Bitrix_IblockHelper_Highload
     */
    public function where($fields, $clear = false) {
        if ($clear) {
            $this->_getlist['filter'] = array();
        }

        $this->_getlist['filter'] = array_merge($this->_getlist['filter'], $fields);

        return $this;
    }

    /**
     * Добавление полей сортировки
     *
     * @param $fields array поля сортировки вида: array(by1=>order1[, by2=>order2 [, ..]])
     * @param bool $clear очистить текущий список полей сортировки перед добавлением новых
     * @return Sibirix_Model_Bitrix_IblockHelper_Highload
     */
    public function order($fields, $clear = true) {
        if ($clear) {
            $this->_getlist['order'] = array();
        }

        $this->_getlist['order'] = array_merge($this->_getlist['order'], $fields);

        return $this;
    }

    /**
     * Ограничивает общее количество элементов участвующих в выборке
     *
     * @param $limit
     * @return Sibirix_Model_Bitrix_IblockHelper_Highload
     */
    public function limit($limit) {
        $this->_getlist['limit'] = $limit;
        return $this;
    }

    /**
     * Смещение начала выборки
     *
     * @param $offset
     * @return Sibirix_Model_Bitrix_IblockHelper_Highload
     */
    public function offset($offset) {
        $this->_getlist['offset'] = $offset;
        return $this;
    }

    /**
     * Нужно ли считать количество записей выборки
     *
     * @return Sibirix_Model_Bitrix_IblockHelper_Highload
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
     * Вызов низкоуровневой функции битрикса для получения данных
     *
     * @return mixed
     */
    protected function callGetList() {
        // проверка повторного использования
        $this->checkReuse();

        // объект кэша должен быть доступен из двух скоупов второго уровня. чтобы все видели что используется,
        // объявляется на первом уровне
        $cache = $this->_use_cache ? new CPHPCache() : null;

        $cacheKey = json_encode([$this->_getlist, $this->_hiblockId]);

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
            // извлечение элементов
            $items   = $results->fetchAll();
            $results = $items;
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
     * @return bool
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

        return $result->getId();
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

        if (is_array($id)) {
            foreach ($id as $el) {
                $result = $entityData::delete($el);
            }
        } else {
            $result = $entityData::delete($id);
        }

        return (isset($result) ? $result->isSuccess() : false);
    }
} 