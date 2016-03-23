<?php

/**
 * Класс для выборки элементов
 *
 * @package files
 * @subpackage classes
 *
 * Class Sibirix_Model_Bitrix_IblockHelper_ElementSelect
 *
 * @method static Sibirix_Model_Bitrix_IblockHelper_ElementSelect instance
 * @method Sibirix_Model_Bitrix_IblockHelper_ElementSelect cache
 * @method Sibirix_Model_Bitrix_IblockHelper_ElementSelect useRawFetch
 * @method Sibirix_Model_Bitrix_IblockHelper_ElementSelect select
 * @method Sibirix_Model_Bitrix_IblockHelper_ElementSelect where
 * @method Sibirix_Model_Bitrix_IblockHelper_ElementSelect order
 */
class Sibirix_Model_Bitrix_IblockHelper_ElementSelect extends Sibirix_Model_Bitrix_IblockHelper_Abstract {
    /**
     * Префикс для полей свойств
     */
    const PROPERTY_PREFIX = 'PROPERTY_';

    /**
     * параметры для передачи в getList
     *
     * @var array
     */
    protected $_getlist = array(
        'arOrder' => array( // по-умолчанию сортируем по SORT и ID
            'SORT' => 'ASC',
            'ID' => 'DESC',
        ),
        'arFilter' => array(
            'ACTIVE' => 'Y' // по-умолчанию фильтруем активные элементы
        ),
        'arGroupBy' => false,
        'arNavStartParams' => false,
        'arSelectFields' => array( // выбираемые по-умолчанию поля
            'ID',
            'NAME',
            'IBLOCK_ID', // в документации сказано, что без этого поля при указании полей выборки выборка неопределенна
            'IBLOCK_SECTION_ID'
        )
    );

    /**
     * Задает группировку
     *
     * @param $field
     * @return Sibirix_Model_Bitrix_IblockHelper_ElementSelect
     */
    public function uniqueBy($field) {
        $this->_getlist['arGroupBy'] = array($field);
        return $this;
    }

    /**
     * Постраничная выборка страницы $pageIndex по $pageSize элементов
     *
     * @param $pageIndex
     * @param $pageSize
     * @return Sibirix_Model_Bitrix_IblockHelper_ElementSelect
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
     * Ограничивает общее количество элементов участвующих в выборке
     *
     * @param $limit int Количество
     *
     * @return Sibirix_Model_Bitrix_IblockHelper_ElementSelect
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
     * @return Sibirix_Model_Bitrix_IblockHelper_ElementSelect
     */
    public function nearElement($element_id, $count=1) {
        $this->_getlist['arNavStartParams'] = array(
            'nPageSize' => $count,
            'nElementID' => $element_id
        );
        return $this;
    }

    /**
     * Итоговая выборка элементов
     *
     * Если $collectProperties=true свойства группируются на манер компонентов битрикса
     *
     * @param bool $collectProperties флаг, нужно ли собирать свойства как в компонентах
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
     * Возвращает элементы в виде объектов
     *
     * @param bool $collectProperties
     *
     * @return array
     */
    public function fetchObjects($collectProperties = false) {
        $items = $this->fetch($collectProperties);
        return array_map(function($item) {
            return (object)$item;
        }, $items);
    }

    /**
     * Подсчет элементов по заданным полям
     *
     * @param $fields
     * @return int
     */
    public function countBy($fields) {
        if (!is_array($fields)) {
            $fields = array($fields);
        }

        $this->_getlist['arGroupBy'] = $fields;
        return parent::callGetList();
    }

    /**
     * Подчет общего количества элементов по заданному фильтру
     *
     * @return int
     */
    public function countTotal() {
        $this->_getlist['arGroupBy'] = array();
        return parent::callGetList();
    }

    /**
     * Сборка свойств как в компонентах
     *
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
}