<?
/**
 * abstract class with bitrix cache support
 * Class Sibirix_Model_Bitrix
 * @property int ID
 */
abstract class Sibirix_Model_Bitrix implements JsonSerializable {

    /**
     * @var IBlockWrap|Highload
     */
    protected $_ibs;

    protected $_iblockId;
    protected $_instanceClass = 'Sibirix_Model_Row';
    protected $_selectFields = ['ID', 'IBLOCK_SECTION_ID','IBLOCK_ID', 'NAME','DETAIL_PICTURE', 'PREVIEW_PICTURE'];
    protected $_selectSectionFields = ['ID', 'IBLOCK_SECTION_ID','IBLOCK_ID', 'NAME', 'DETAIL_PICTURE', 'PREVIEW_PICTURE'];

    protected $_data = null;

    protected $_asObject = true;

    protected $_pageSize = 10;

    /**
     * model instance setup
     * @param null $initParams
     */
    public function init($initParams = NULL) {

    }

    public function __construct($initParams = NULL) {
        if (!$this->_instanceClass) {
            static $_instanceClassRepo;

            $cl = get_called_class();
            if (!$_instanceClassRepo || !isset($_instanceClassRepo[$cl])) {
                $_instanceClassRepo[$cl] = $cl . '_Row';
            }

            $this->_instanceClass = $_instanceClassRepo[$cl];
        }

        $this->init($initParams);
    }

    /**
     * Получить список разделов
     * @param $params
     * @return array|null
     */
    public function getSections($params = []) {
        $ibh = IBlockSectionSelect::instance($this->_iblockId);
        $ibh->select(is_array($params['select']) ? $params['select'] : $this->_selectSectionFields, true);

        if (is_array($params['where'])) {
            $ibh->where($params['where']);
        }

        if (is_array($params['sort'])) {
            $ibh->orderBy($params['sort'], true);
        }

        if (count($params['incCnt']) > 0) {
            $withSubitems = (bool)$params['incCnt']['withSubitems'];
            $onlyActive   = (bool)$params['incCnt']['onlyActive'];
            $ibh->calcCount($withSubitems, $onlyActive);
        }

        if (isset($params['cache'])) {
            $ibh->cache($params['cache']);
        }

        $sections = $ibh->fetch();

        if ($this->_asObject) {
            $this->_collectionToObject($sections);
        }
		
        return $sections;
    }

    /**
     * Добавить элемент в свой инфоблок
     * @param $fields
     * @return bool
     */
    public function add($fields) {
        $fields['IBLOCK_ID'] = $this->_iblockId;
        $ibe = new CIBlockElement();
        return $ibe->Add($fields);
    }

    /**
     * Обновить элемент в своём инфоблоке
     * @param $id
     * @param $fields
     * @return bool
     */
    public function update($id, $fields) {
        $fields['IBLOCK_ID'] = $this->_iblockId;
        $ibe = new CIBlockElement();
        return $ibe->Update($id, $fields);
    }
	public function updateValue($id, $fields) {   
		$ibe = new CIBlockElement();
		return $ibe->SetPropertyValuesEx($id, $this->_iblockId, $fields); 
    }

    /**
     * @param $itemId
     * @return object|Sibirix_Model_Row
     */
    public function getElement($itemId = false) {
        if (is_numeric($itemId)) {
            $this->where(['ID' => $itemId]);
        }

        $this->limit(1)
            ->cache(false);

        $elements = $this->getElements();
        if (0 == count($elements)) {
            return false;
        }

        reset($elements);
        return current($elements);
    }

    /**
     * @return array
     */
    public function getElements() {
        $elements = $this->fetch();
        reset($elements);
        $this->_getQueryInstance(true);
        return $elements;
    }

    /**
     * Получение списка записей (с учетом настроенных параметров IBlockHelper'а) в виде справочника
     * Если передано несколько полей для построения стравочника - возвращает массив объектов,
     * если одно - массив значений
     * @param $addEmpty
     * @param string|array $field
     * @return array
     */
    public function getReference($addEmpty = true, $field = 'NAME') {
        if (!is_array($field)) $field = [$field];
        $short = array_map(function($obj) { return str_replace('_VALUE', '', $obj); }, $field);

        $this->select(array_merge(['ID'], $short), true)->orderBy([$short[0] => 'ASC'], true);

        $previousAsObject = $this->_asObject;
        $this->_asObject = true;
        $elements = $this->fetch();

        $this->_getQueryInstance(true);

        $result = [];
        if ($addEmpty) $result[''] = '';

        foreach ($elements as $element) {
            $result[$element->ID] = $element;
        }

        if ((array_search('PREVIEW_PICTURE', $field) !== false) || (array_search('DETAIL_PICTURE', $field) !== false)) {
            $fileIdList = [];
            foreach ($result as $element) {
                if (!empty($element->PREVIEW_PICTURE) && is_numeric($element->PREVIEW_PICTURE)) $fileIdList[] = $element->PREVIEW_PICTURE;
                if (!empty($element->DETAIL_PICTURE)  && is_numeric($element->DETAIL_PICTURE))  $fileIdList[] = $element->DETAIL_PICTURE;
            }

            if (count($fileIdList)) {
                $files = EHelper::getFileData($fileIdList);
                foreach ($result as $ind => $element) {
                    if (!empty($element->PREVIEW_PICTURE)) {
                        $result[$ind]->PREVIEW_PICTURE = $files[$element->PREVIEW_PICTURE];
                    }
                    if (!empty($element->DETAIL_PICTURE)) $result[$ind]->DETAIL_PICTURE = $files[$element->DETAIL_PICTURE];
                }
            }
        }

        if (count($field) == 1) {
            $fieldName = $field[0];
            foreach ($result as $ind => $val) {
                $result[$ind] = $val->$fieldName;
            }
        }

        $this->_asObject = $previousAsObject;
        return $result;
    }

    /**
     * Загрудка картинок для списка элементов
     * @param $elements
     * @param bool $imageFields
     * @return mixed
     */
    public function loadListPictures($elements, $imageFields = false) {
        if (!is_array($imageFields)) {
            $imageFields = ['PREVIEW_PICTURE', 'DETAIL_PICTURE'];
        }

        $photoIds = [];
        foreach ($imageFields as $imageField) {
            foreach ($elements as $ind => $element) {
                if ($element->hasField($imageField)) {
                    $photoIds[] = $element->$imageField;
                }
            }
        }

        if (count($photoIds)) {
            $files = EHelper::getFileData($photoIds);
            foreach ($imageFields as $imageField) {
                foreach ($elements as $ind => $element) {
                    if ($element->hasField($imageField)) {
                        $element->$imageField = $files[$element->$imageField];
                    }
                }
            }
        }

        return $elements;
    }

    /**
     * @param int|bool $page
     * @return object
     */
    public function getPage($page = 1) {
        $this->page($page, $this->_pageSize);

        $elements = $this->fetch();

        $pageData = (object)[
            'size'     => $this->_pageSize,
            'current'  => $page,
            'totalItemsCount' => $this->getTotalCount()
        ];

        $this->_getQueryInstance(true);

        return (object)[
            'pageData' => $pageData,
            'items' => $elements
        ];
    }
	
	public function getPageItem($page = 1, $pagesize = 1) {
        $this->page($page, $pagesize);

        $elements = $this->fetch();

        $pageData = (object)[
            'size'     => $pagesize,
            'current'  => $page,
            'totalItemsCount' => $this->getTotalCount()
        ];

        $this->_getQueryInstance(true);

        return (object)[
            'pageData' => $pageData,
            'items' => $elements
        ];
    }

    public function getPageSize() {
        return $this->_pageSize;
    }

    public function isResultAsObject() {
        return $this->_asObject;
    }

    public function resultAsObject() {
        $this->_asObject = true;

        return $this;
    }

    public function resultAsArray() {
        $this->_asObject = false;

        return $this;
    }

    protected function _collectionToObject(&$elements) {
        if (!empty($this->_instanceClass)) {
            $elObj = new $this->_instanceClass();
        } else {
            $class = get_called_class() . '_Row';
            if (!class_exists($class)) {
                throw new Zend_Db_Exception('not found row class "' . $class . '"');
            }

            $elObj = new $class();
        }

        foreach ($elements as &$element) {
            /**
             * @var $_elObj self
             */
            $_elObj = clone($elObj);
            $_elObj->setData($element);
            $element = $_elObj;
        }
        reset($elements);

        return $this;
    }

    public function setData($data) {
        $this->_data = $data;

        return $this;
    }

    public function __isset($name) {
        return array_key_exists($name, $this->_data);
    }

    public function __get($name) {
        if (null === $this->_data) {
            throw new Zend_Db_Exception("instance not loaded");
        }

        if (array_key_exists($name . '_VALUE', $this->_data)) {
            return $this->_data[$name . '_VALUE'];
        }

        if (array_key_exists($name, $this->_data)) {
            return $this->_data[$name];
        }

        throw new Zend_Db_Exception("not found field [$name]");
    }

    public function __set($name, $value) {
        $this->_data[$name] = $value;
    }

    public function getRawData() {
        return $this->_data;
    }

    /**
     * @param $obj self
     * @return array
     */
    public static function prepareFormValues($obj) {
        $props = [
            'ID' => $obj->ID
        ];
        $rawData = $obj->getRawData();
        $regep = '/^PROPERTY_(.*)_VALUE$/';
        $regepEnum = '/^PROPERTY_(.*)_ENUM_ID$/';
        foreach ($rawData as $key => $value) {
            if (preg_match($regep, $key, $matches)) {
                $props[$matches[1]] = $value;
            }
            if (preg_match($regepEnum, $key, $matches)) {
                $props[$matches[1]] = $value;
            }
        }
        return $props;
    }

    /**
     * Получение инстанса IBlockWrap для проксирования его вызова через методы модели
     *
     * @param bool $force
     * @return IBlockWrap
     */
    protected function _getQueryInstance($force = false) {
        if ($force || !$this->_ibs) {
            $this->_ibs = IBlockWrap::instance($this->_iblockId);
            $this->_ibs
                ->select($this->_selectFields);
        }

        return $this->_ibs;
    }

    /**
     * @param $elementId
     */
    public function remove($elementId) {
        $this->_getQueryInstance()->remove($elementId);
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
     * @return $this
     */
    public function select($fields, $clear = false) {
        $this->_getQueryInstance()->select($fields, $clear);

        return $this;
    }

    /**
     * Добавление параметров для фильтрации элементов
     *
     * примечание. при формировании фильтра смотреть https://dev.1c-bitrix.ru/api_help/iblock/filter.php
     *
     * @param $fields array поля фильтрации. в простейшем случае вида: array("фильтруемое поле"=>"значения фильтра" [, ...])
     * @param bool $clear очистить текущий фильтр перед добавлением
     * @return $this
     */
    public function where($fields, $clear=false) {
        $this->_getQueryInstance()->where($fields, $clear);

        return $this;
    }

    /**
     * Добавление полей сортировки
     *
     * @param $fields array поля сортировки вида: array(by1=>order1[, by2=>order2 [, ..]])
     * @param bool $clear очистить текущий список полей сортировки перед добавлением новых
     * @return $this
     */
    public function orderBy($fields, $clear=false) {
        $this->_getQueryInstance()->orderBy($fields, $clear);

        return $this;
    }

    /**
     * Ручное задание navParams
     * @param $navParams
     * @return $this
     */
    public function navParams($navParams) {
        $this->_getQueryInstance()->navParams($navParams);

        return $this;
    }

    /**
     * Ограничивает общее количество элементов участвующих в выборке
     *
     * @param $limit
     * @return $this
     */
    public function limit($limit) {
        $this->_getQueryInstance()->limit($limit);

        return $this;
    }

    /**
     * @return int
     */
    public function count() {
        $count = $this->_getQueryInstance()->count();
        $this->_getQueryInstance(true);
        return $count;
    }

    /**
     * Итоговая выборка элементов
     *
     * @param bool $collectProperties флаг, нужно ли собирать свойства как в компонентах
     *
     * @return array
     */
    public function fetch($collectProperties = false) {
        $result =  $this->_getQueryInstance()->fetch($collectProperties);




        if ($this->_asObject) {
            $this->_collectionToObject($result);
        }

        return $result;
    }

    /**
     * Постраничная выборка страницы $pageIndex по $pageSize элементов
     *
     * @param $pageIndex
     * @param $pageSize
     * @return $this
     */
    public function page($pageIndex = false, $pageSize = false) {
        $pageSize = $pageSize ? $pageSize : $this->_pageSize;
        $this->_getQueryInstance()->page($pageIndex, $pageSize);
        return $this;
    }
	
    public function test(){
		CModule::IncludeModule("blog");
		$SORT = Array("DATE_PUBLISH" => "DESC", "NAME" => "ASC");
		$arFilter = Array(

		);	
		
		$arSelectedFields = array("ID", "BLOG_ID","CATEGORY_ID_F", "SHOW_COUNTER", "TITLE", "DATE_PUBLISH", "AUTHOR_ID", "PREVIEW_PICTURE", "SHOW_COUNTER", "DETAIL_TEXT", "BLOG_ACTIVE", "BLOG_URL", "BLOG_GROUP_ID", "BLOG_GROUP_SITE_ID", "AUTHOR_LOGIN", "AUTHOR_NAME", "AUTHOR_LAST_NAME", "AUTHOR_SECOND_NAME", "BLOG_USER_ALIAS", "BLOG_OWNER_ID", "VIEWS", "NUM_COMMENTS", "ATTACH_IMG", "BLOG_SOCNET_GROUP_ID", "CODE", "MICRO");
		
		$dbPosts = CBlogPost::GetList(
			$SORT,
			$arFilter,
			false,
			$COUNT,
			$arSelectedFields
		);
		
		$arPosts = array();
		while($arPost = $dbPosts->Fetch())
		{
			array_push($arPosts, $arPost);
		}
		
	

		/*  CModule::IncludeModule("catalog");
		$dbProductDiscounts = CCatalogDiscount::GetList(
		array("SORT" => "ASC"),
		array("ID" => "1"),
		false,
		false,
		array("ID", "PRODUCT_ID")
		);*/
		return $arPosts;
    }
    /**
     * Включает использование кэша на $time секунд
     * 
     * @param $time
     * @return $this
     */
    public function cache($time) {
        $this->_getQueryInstance()->cache($time);

        return $this;
    }

    /**
     * Получение общего количество элементов подпадающих под выборку
     * @return bool|int
     */
    public function getTotalCount() {
        return $this->_getQueryInstance()->getTotalCount();
    }

    /**
     * @param $field
     * @return $this
     */
    public function uniqueBy($field) {
        $this->_getQueryInstance()->uniqueBy($field);

        return $this;
    }

    /**
     * @return IBlockWrap
     */
    public function asSubQuery(){
        return $this->_getQueryInstance();
    }

    /**
     * поддержка Zend_Json
     *
     * @return array
     */
    public function toArray() {
        return $this->_data;
    }

    /**
     * @return array
     */
    public function jsonSerialize() {
        return $this->toArray();
    }


    public function getImageData(&$elementList, $imageField = ["DETAIL_PICTURE", "PREVIEW_PICTURE"]) {
        $files = array();

        if (!is_array($imageField)) {
            $fieldList = [$imageField];
        } else {
            $fieldList = $imageField;
        }

        if (!is_array($elementList)) {
            $elements = [$elementList];
        } else {
            $elements = $elementList;
        }

        if (!empty($elements) && !empty($fieldList)) {
            foreach ($elements as $element) {
                foreach ($fieldList as $field) {
						
                    if(is_object($element)) {
					
                        if (!isset($element->$field)) {
                            continue;
                        }
	
                        if (is_array($element->$field)) {
                            $files = array_merge($files, $element->$field);
                        } else {
                            $files[] = $element->$field;
                        }
						
                    } else {
                        if (!isset($element[$field])) {
                            continue;
                        }

                        if (is_array($element[$field])) {
                            $files = array_merge($files, $element[$field]);
                        } else {
                            $files[] = $element[$field];
                        }
						
                    }
					
                }
				
            }
        }
	
        if (!empty($files) > 0) {
            $files = EHelper::getFileData($files);
            foreach ($elements as $key => $element) {
                foreach ($fieldList as $field) {
                    if(is_object($element)) {
                        if (!isset($element->$field)) {
                            continue;
                        }

                        if (is_array($element->$field)) {
                            $arrayProp = $element->$field;
                            foreach ($arrayProp as $keyVariant => $elementFieldVariant) {
                                $arrayProp[$keyVariant] = $files[$elementFieldVariant];
                            }
                            $elements[$key]->$field = $arrayProp;
                        } else {
                            $elements[$key]->$field = $files[$element->$field];
                        }
                    } else {
                        if (!isset($element[$field])) {
                            continue;
                        }

                        if (is_array($element[$field])) {
                            $arrayProp = $element[$field];
                            foreach ($arrayProp as $keyVariant => $elementFieldVariant) {
                                $arrayProp[$keyVariant] = $files[$elementFieldVariant];
                            }
                            $elements[$key][$field] = $arrayProp;
                        } else {
                            $elements[$key][$field] = $files[$element[$field]];
                        }
                    }
                }
            }
        }
		
        if (!is_array($elementList)) {
            $elementList = $elements[0];
        } else {
            $elementList = $elements;
        }
		return $elementList;
    }

    /**
     * Получает СЕО данные элементов
     * @param $elementList
     */
    public function getSeoElementParams(&$elementList) {
        if (!is_array($elementList)) {
            $elements = [$elementList];
        } else {
            $elements = $elementList;
        }

        foreach ($elements as $key => $element) {
            $ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($element->IBLOCK_ID, $element->ID);
            $elements[$key]->SEO = $ipropValues->getValues();
        }

        if (!is_array($elementList)) {
            $elementList = $elements[0];
        } else {
            $elementList = $elements;
        }
    }

    /**
     * Получает СЕО данные раздела
     * @param $sectionList
     */
    public function getSeoSectionParams(&$sectionList) {
        if (!is_array($sectionList)) {
            $sections = [$sectionList];
        } else {
            $sections = $sectionList;
        }
		
        foreach ($sections as $key => $section) {
            $ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues($section->IBLOCK_ID, $section->ID);
            $sections[$key]->SEO = $ipropValues->getValues();
        }
		
        if (!is_array($sectionList)) {
            $sectionList = $sections[0];
        } else {
            $sectionList = $sections;
        }
    }

    /**
     * Кэшированный рандом
     * @param $cacheId
     * @param array $fields
     * @param array $filter
     * @param bool $elementCnt
     * @return array
     */
    public function getRandElements($cacheId, $fields = [], $filter = [], $elementCnt = false, $method = false, $model = false) {
        $cacheTime = 60 * 60 * 24 * 30;
        $obCache   = new CPHPCache();
        if ($obCache->InitCache($cacheTime, $cacheId, '/' . $cacheId . '-rand')) {
            $vars = $obCache->GetVars();
            $data = $vars[$cacheId];
        } else {
            if (empty($fields)) {
                $fields = $this->_selectListFields;
            }

            $data = $this
                ->select($fields, true)
                ->where($filter)
                ->getElements();

            if ($method) {
                call_user_func_array([$model, $method], [$data]);
            }

            if ($obCache->StartDataCache()) {
                $obCache->EndDataCache(array($cacheId => $data));
            }
        }

        shuffle($data);

        if ($elementCnt > 0) {
            $resultData = array_slice($data, 0, $elementCnt);
        } else {
            $resultData = $data;
        }

        return $resultData;
    }
}
