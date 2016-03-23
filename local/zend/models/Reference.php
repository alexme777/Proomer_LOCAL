<?
/**
 * Class Sibirix_Model_Reference
 */
class Sibirix_Model_Reference {

    /**
     * Получает справочник
     * @param $referenceId
     * @param array $selectFields
     * @param string $fieldAsKey
     * @return array
     */
    public static function getReference($referenceId, $selectFields = array("UF_NAME"), $fieldAsKey = "ID") {
        if (empty($selectFields)) $selectFields = array("UF_NAME");
        if (empty($fieldAsKey)) $fieldAsKey = "ID";

        $hlReference = Highload::instance($referenceId);
        $valueList   = $hlReference->select(array_merge([$fieldAsKey], $selectFields), true)->fetch();

        if (empty($valueList)) return array();

        $returnValues = array();
        foreach ($valueList as $value) {
            $selectResult = array();
            foreach ($selectFields as $selectField) {
                $selectResult[] = $value[$selectField];
            }
            if (count($selectResult) == 1) {
                $selectResult = current($selectResult);
            }
            $returnValues[$value[$fieldAsKey]] = $selectResult;
        }

        return $returnValues;
    }
}
