<?php

/**
 * Класс для выборки разделов
 *
 * @package files
 * @subpackage classes
 *
 * Class Sibirix_Model_Bitrix_IblockHelper_SectionSelect
 *
 * @method static Sibirix_Model_Bitrix_IblockHelper_SectionSelect instance
 * @method Sibirix_Model_Bitrix_IblockHelper_SectionSelect cache
 * @method Sibirix_Model_Bitrix_IblockHelper_SectionSelect useRawFetch
 * @method Sibirix_Model_Bitrix_IblockHelper_SectionSelect where
 * @method Sibirix_Model_Bitrix_IblockHelper_SectionSelect order
 */
class Sibirix_Model_Bitrix_IblockHelper_SectionSelect extends Sibirix_Model_Bitrix_IblockHelper_Abstract {
    /**
     * Функция над которой оборачивается класс
     *
     * @var array
     */
    protected $_getlist_function = array("CIBlockSection", "GetList");

    /**
     * Параметры для передачи в getList
     *
     * @var array
     */
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
     * @param array $fields пользовательский полей
     * @param bool $clear удалить текущий список полей перед добавлением новых
     * @return Sibirix_Model_Bitrix_IblockHelper_SectionSelect
     */
    public function select($fields, $clear = true) {
        return parent::select($fields, $clear);
    }

    /**
     * Включает подсчет элементов в разделе которое будет возвращено в поле ELEMENT_CNT каждого раздела
     *
     * @param bool $withSubitems
     * @param bool $onlyActive
     *
     * @return Sibirix_Model_Bitrix_IblockHelper_SectionSelect
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
     * @param bool|int $limit
     *
     * @return $this
     */
    public function limit($limit = false) {
        if ($limit) {
            $this->_getlist['arNavStartParams'] = array(
                'iNumPage'  => 1,
                'nPageSize' => $limit,
            );
        }
        return $this;
    }

    /**
     * Итоговая выборка разделов
     *
     * @return array
     */
    public function fetch() {
        $result = parent::callGetList();
        return $result;
    }

    /**
     * Возвращает элементы в виде объектов
     *
     * @return array
     */
    public function fetchObjects() {
        $items = $this->fetch();
        return array_map(function ($item) {
            return (object)$item;
        }, $items);
    }
} 