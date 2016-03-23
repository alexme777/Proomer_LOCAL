<?
/**
 * Class Sibirix_Model_Iblock_Abstract
 */
abstract class Sibirix_Model_Iblock_Abstract  extends Sibirix_Model_Bitrix_Iblock {

    protected $_iBlockId;
    protected $_selectFields = array('ID');
    protected $_selectSectionFields = array();

    private $_pageSize = 10;

    public $IBLOCK_ID;
    public $ID;
    public $CODE;
    public $NAME;
    public $DESCRIPTION;

    /**
     * @var CIBlockElement
     */
    protected $_ibe;

    /**
     * @var CIBlockSection
     */
    protected $_ibs;

    private $_availableSorts = array();

    /**
     * @return mixed
     * @throws Zend_Exception
     */
    public static function model() {
        $calledClass = get_called_class();
        return new $calledClass();
    }

    public function init() {
        $this->_ibe = new CIBlockElement();
        $this->_ibs = new CIBlockSection();
    }

    /**
     * Папка для хранения кеша
     * @return string
     */
    protected function getCacheFolder() {
        $folder = '/sibirix/';
        if (isset($this->_iBlockId) && !empty($this->_iBlockId)) {
            $folder .= $this->_iBlockId . '/';
        }
        $folder .= get_class($this) . '/';
        return $folder;
    }

    /**
     * Получить список разделов
     * @param $params
     * @return array|null
     */
    public function getSections($params) {
        $cacheId = md5(var_export($params, true) . var_export($this->_iBlockId, true) . '-sections');

        $sections = $this->_getCache($cacheId);

        if ($sections === null) {

            $sections = array();
            $filter = array('IBLOCK_ID' => $this->_iBlockId);

            if ($params['filter']) {
                $filter = array_merge($filter, $params['filter']);
            }

            $sort = array();
            $isSorted = false;
            if ($params['sort']) {
                $sort = $params['sort'];
                $isSorted = true;
            } else {
                $sort['SORT'] = 'ASC';
                $sort['NAME'] = 'ASC';
            }

            $incCount = false;
            if (isset($params['incCount'])) {
                $incCount = (bool)$params['incCount'];
            }

            $sectionsRes = $this->_ibs->GetList($sort, $filter, $incCount, $this->_selectSectionFields);

            while ($sec = $sectionsRes->Fetch()) {
                if ($isSorted) {
                    $sections[] = (object)$sec;
                } else {
                    $sections[(int)$sec['ID']] = (object)$sec;
                }
            }

            $this->_setCache($cacheId, $sections);
        }

        return $sections;
    }

    /**
     * @param array $params
     * @return object|bool
     */
    public function getElement($params = array()) {
        $list = $this->getElements(array_merge($params, array('navParams' => array('nTopCount' => 1))));
        if (count($list) == 0) {
            return false;
        }

        reset($list);
        return current($list);
    }

    /**
     * @param array $params
     * @return array
     */
    public function getElements($params = array()) {
        $cacheId = md5(var_export($params, true) . var_export($this->_iBlockId, true) . var_export($this->_selectFields, true));

        $elements = $this->_getCache($cacheId);

        if ($elements === null) {

            $elements = array();
            $filter = array('IBLOCK_ID' => $this->_iBlockId);

            if ($params['filter']) {
                $filter = array_merge($filter, $params['filter']);
            }

            $sort = array();
            $sorted = false;
            if ($params['sort']) {
                $sort = $params['sort'];
                $sorted = true;
            } else {
                $sort['SORT'] = 'ASC';
                $sort['NAME'] = 'ASC';
            }

            $elRes = $this->_ibe->GetList($sort, $filter, false, (isset($params['navParams']) ? $params['navParams'] : false), $this->_selectFields);

            if (isset($params['options']['page'])) {
                $elRes->NavStart($this->_pageSize, false, $params['options']['page']);
                if ($elRes->NavPageCount < $params['options']['page']) {
                    $this->_setCache($cacheId, $elements);
                    return $elements;
                }
            }

            while ($el = $elRes->Fetch()) {
                if ($sorted) {
                    $elements[] = (object)$el;
                } else {
                    $elements[(int)$el['ID']] = (object)$el;
                }
            }

            $elements = $this->postProcessElements($elements);
            $this->_setCache($cacheId, $elements);
        }

        return $elements;
    }

    /**
     * Выбрать элементы, возвращает массив с параметрами пагинации
     * @param array $params
     * @return array
     */
    public function getElementsPagination($params = array()) {
        $cacheId = md5(var_export($params, true) . var_export($this->_iBlockId, true) . var_export($this->_selectFields, true));

        $elements = $this->_getCache($cacheId);

        if ($elements === null) {

            $elements = array();
            $filter = array('IBLOCK_ID' => $this->_iBlockId);

            if ($params['filter']) {
                $filter = array_merge($filter, $params['filter']);
            }

            $sort = array();
            if ($params['sort']) {
                $sort = $params['sort'];
            } else {
                if (count($this->_availableSorts)) {
                    $sort[reset($this->_availableSorts)] = 'ASC';

                } else {
                    $sort['SORT'] = 'ASC';
                    $sort['NAME'] = 'ASC';
                }
            }

            $elRes = $this->_ibe->GetList($sort, $filter, false, (isset($params['navParams']) ? $params['navParams'] : false), $this->_selectFields);
            $elRes->NavStart();

            while ($el = $elRes->Fetch()) {
                $elements[] = (object)$el;
            }

            $elements = $this->postProcessElements($elements);

            $sortBy = array_keys($sort);
            $sortBy = $sortBy[0];
            $sortOrder = reset($sort);

            $page = $elRes->PAGEN;
            if ($page > $elRes->NavPageCount) $page = $elRes->NavPageCount;
            if ($page < 1) $page = 1;
            $elements = array(
                'data'       => $elements,
                'pagination' => array(
                    'page'   => $page,
                    'pages'  => $elRes->NavPageCount,
                    'inpage' => $elRes->NavPageSize,
                    'total'  => $elRes->NavRecordCount,
                ),
                'sort' => array(
                    'field'     => array_search($sortBy, $this->_availableSorts),
                    'direction' => $sortOrder
                )
            );

            $this->_setCache($cacheId, $elements);
        }

        return $elements;
    }

    /**
     * Постобработка элементов перед сохранением в кеш
     * @param $elements
     * @return mixed
     */
    public function postProcessElements($elements) {
        // Override in descendants
        return $elements;
    }

    public function select($fields = array()) {
        $this->_selectFields = $fields;

        return $this;
    }

    /**
     * _initBitrixProperties
     * configure the list of fields for selects
     *
     * @param array $params
     * @return bool
     */
    protected function _initBitrixProperties($params = array()) {
        $params['excludeProps'] = (isset($params['excludeProps']) && is_array($params['excludeProps']) ? $params['excludeProps'] : array());
        $params['unprocessProps'] = (isset($params['unprocessProps']) && is_array($params['unprocessProps']) ? $params['unprocessProps'] : array());
        $params['linkedProps'] = (isset($params['linkedProps']) && is_array($params['linkedProps']) ? $params['linkedProps'] : array());

        $cacheId = 'bx_properties_' . $this->_iBlockId . '_' . md5(serialize($params));

        $this->bxProps = $this->_getCache($cacheId);
        if (is_array($this->bxProps)) {
            return true;
        }

        $dbResult = CIBlockProperty::GetList(array(), array('IBLOCK_ID' => $this->_iBlockId));

        $this->bxProps = array();
        while ($arProp = $dbResult->Fetch()) {
            if (in_array($arProp['CODE'], $params['excludeProps'])) {
                continue;
            }

            $this->bxProps[$arProp['CODE']] = $arProp;

            if (in_array($arProp['CODE'], $params['unprocessProps'])) {
                continue;
            }

            switch ($arProp['PROPERTY_TYPE']) {
                case 'L':
                    $dbPropVariant = CIBlockProperty::GetPropertyEnum($arProp['ID']);
                    while ($propVariant = $dbPropVariant->Fetch()) {
                        $this->bxProps[$arProp['CODE']]['VARIANTS'][$propVariant['ID']] = $propVariant;
                    }
                    break;

                case 'E':
                    $linkSelect = array(
                        'ID',
                        'NAME',
                        'CODE',
                        'PREVIEW_TEXT'
                    );
                    if (isset($params['linkedProps'][$arProp['CODE']])) {
                        $linkSelect = array_merge($linkSelect, $params['linkedProps'][$arProp['CODE']]);
                    }
                    $dbLinkVariants = $this->_ibe->GetList(array(), array(
                            'IBLOCK_ID' => $arProp['LINK_IBLOCK_ID']
                        ), false, false, $linkSelect);

                    while ($propVariant = $dbLinkVariants->Fetch()) {
                        $this->bxProps[$arProp['CODE']]['VARIANTS'][$propVariant['ID']] = $propVariant;
                    }
                    break;
            }
        }

        $this->_setCache($cacheId, $this->bxProps);

        return $this;
    }

    public function getMetadata() {
        return $this->bxProps;
    }

    public function setConfig($config = array()) {
        if (!isset($config['cache'])) {
            $config['cache'] = array();
        }

        if (!isset($config['cache']['type'])) {
            $config['cache']['type'] = 'A';
        }

        parent::setConfig($config);
    }
}
