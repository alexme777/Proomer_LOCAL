<?php

/**
 * Class EnumUtils
 * хелпер для работы со полями типа СПИСОК
 */
class EnumUtils
{

    protected static $_cache = [];

    /**
     * @param $iblockId
     * @param $propertyName
     * @return array
     */
    protected static function _getRawList($iblockId, $propertyName) {
        if (isset(self::$_cache[$iblockId][$propertyName])) {
            return self::$_cache[$iblockId][$propertyName];
        }
        $iterator = CIBlockPropertyEnum::GetList(
            array('SORT' => 'ASC', 'ID' => 'ASC'),
            array(
                'IBLOCK_ID' => $iblockId,
                'PROPERTY_ID' => $propertyName,
            )
        );
        $items = array();
        while ($item = $iterator->GetNext()) {
            $items[] = $item;
        }

        self::$_cache[$iblockId][$propertyName] = $items;

        return $items;
    }

    /**
     * Получение списка возможных значений в свойстве типа СПИСОК
     * @param $iblockId
     * @param $propertyName
     * @return array
     */
    static function getPropertyValuesList($iblockId, $propertyName)
    {
        $items = self::_getRawList($iblockId, $propertyName);
        IBlockHelper::fieldToKey($items, 'ID');
        return $items;
    }

    /**
     * Получаение id свойства типа checkbox
     * @param $iblockId
     * @param $propertyName
     * @param $xmlId
     * @return array
     */
    static function getPropertyCheckboxId($iblockId, $propertyName, $xmlId)
    {
        $checkboxId = false;
        $propertyList = self::getPropertyValuesList($iblockId, $propertyName);
        foreach ($propertyList as $propertyVariant) {
            if ($propertyVariant["XML_ID"] == $xmlId) {
                $checkboxId = $propertyVariant["ID"];
                break;
            }
        }
        return $checkboxId;
    }

    /**
     * Получение ID варианта в поле типа СПИСОК по его XML_ID
     * @param $iblockId
     * @param $propertyName
     * @param $xmlId
     * @return mixed
     */
    static function getListIdByXmlId($iblockId, $propertyName, $xmlId)
    {
        $items = self::_getRawList($iblockId, $propertyName);
        foreach ($items as $item) {
            if ($item['XML_ID'] == $xmlId) return $item['ID'];
        }
        return false;
    }

    /**
     * @param $iblockId
     * @param $propertyName
     * @param $value
     * @return mixed
     */
    static function getListIdByValue($iblockId, $propertyName, $value)
    {
        $items = self::_getRawList($iblockId, $propertyName);
        foreach ($items as $item) {
            if ($item['VALUE'] == $value) return $item['ID'];
        }
        return false;
    }

    /**
     * @param $iblockId
     * @param $propertyName
     * @param $id
     * @return array
     */
    static function getListItemById($iblockId, $propertyName, $id)
    {
        $items = self::_getRawList($iblockId, $propertyName);
        foreach ($items as $item) {
            if ($item['ID'] == $id) return $item;
        }
        return false;
    }

    /**
     * @param $iblockId
     * @param $propertyName
     * @param $xmlId
     * @return bool
     */
    static function getListItemByXmlId($iblockId, $propertyName, $xmlId)
    {
        $items = self::_getRawList($iblockId, $propertyName);
        foreach ($items as $item) {
            if ($item['XML_ID'] == $xmlId) return $item;
        }

        return false;
    }

    /**
     * Add new value to properties list
     * @param integer $propertyId ID of property
     * @param string $value
     * @param string | null $xmlId external ID will be generated automatically if not passed
     * @param int $sort
     * @return mixed ID of added property's value or false
     */
    static function addItem($propertyId, $value, $xmlId = null, $sort = 500)
    {
        if ($xmlId === null) {
            $xmlId = ($xmlId);
        }

        $iblockEnum = new CIBlockPropertyEnum();
        $propId = $iblockEnum->Add(
            array(
                'PROPERTY_ID' => $propertyId,
                'VALUE'       => $value,
                'XML_ID'      => $xmlId,
                'SORT'        => $sort,
            )
        );

        return $propId;
    }

    /**
     * @param array $params
     * @return array
     */
    static function getUserFieldValuesList($params = [])
    {
        $iterator = CUserFieldEnum::GetList(
            [],
            $params
        );

        $items = [];
        while ($item = $iterator->Fetch()) {
            $items[] = $item;
        }

        IBlockHelper::fieldToKey($items, 'ID');

        return $items;
    }

    /**
     * @param array $params
     * @return mixed
     */
    static function getUserFieldIdByXmlId($params = array())
    {
        $iterator = CUserFieldEnum::GetList(
            array(),
            $params
        );

        $item = $iterator->Fetch();

        return $item['ID'];
    }
}
