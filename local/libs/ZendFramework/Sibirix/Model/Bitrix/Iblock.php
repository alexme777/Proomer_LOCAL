<?php
/**
 * abstract class to support the bitrix iblock
 * Class Sibirix_Model_Bitrix_Iblock
 */
abstract class Sibirix_Model_Bitrix_Iblock extends Sibirix_Model_Bitrix {

    protected $bxProps = null;

    /**
     * @param array $config
     * @throws Zend_Exception
     */
    public function __construct($config = array()) {
        if (!CModule::IncludeModule('iblock')) {
            throw new Zend_Exception('Модуль инфоблоков не установлен', 500);
        }

        parent::__construct($config);
    }

    /**
     * _initBitrixProperties
     * configure the list of fields for selects
     *
     * @param array $params
     * @return bool
     */
    protected function _initBitrixProperties($params = array()) {
        $params['excludeProps']   = (isset($params['excludeProps'])   && is_array($params['excludeProps'])   ? $params['excludeProps']   : array());
        $params['unprocessProps'] = (isset($params['unprocessProps']) && is_array($params['unprocessProps']) ? $params['unprocessProps'] : array());
        $params['linkedProps']    = (isset($params['linkedProps'])    && is_array($params['linkedProps'])    ? $params['linkedProps']    : array());

        $cacheId = 'bx_properties_' . md5(serialize($params));

        $this->bxProps = $this->_getCache($cacheId);
        if (is_array($this->bxProps)) {
            return true;
        }

        $dbResult = CIBlockProperty::GetList(array(), array('IBLOCK_ID' => static::IBLOCK_ID));

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

                    $ibe = new CIBlockElement();
                    $dbLinkVariants = $ibe->GetList(array(), array(
                            'IBLOCK_ID' => $arProp['LINK_IBLOCK_ID']
                        ), false, false, $linkSelect);

                    while ($propVariant = $dbLinkVariants->Fetch()) {
                        $this->bxProps[$arProp['CODE']]['VARIANTS'][$propVariant['ID']] = $propVariant;
                    }
                    break;
            }
        }

        $this->_setCache($cacheId, $this->bxProps);
        return true;
    }

    /**
     * get the one element by specified params
     *
     * @param array $params
     * @return array|false
     */
    abstract public function getElement($params = array());

    /**
     * get the set elements by specified params
     *
     * @param array $params
     * @return array
     */
    abstract public function getElements($params = array());
}