<?
/**
 * Class Sibirix_Model_Iblock_Reference
 */
abstract class Sibirix_Model_Iblock_Reference  extends Sibirix_Model_Iblock_Abstract {

    /**
     * Получить все элементы справочника в формате Ключ => Значение
     * @param bool $addEmpty
     * @return array
     */
    public function getKeyValues($addEmpty = true) {
        $values = $this->getElements();
        $result = array();
        if ($addEmpty) $result[''] = '';

        foreach ($values as $val) {
            $result[$val->ID] = $val->NAME;
        }

        return $result;
    }
}
