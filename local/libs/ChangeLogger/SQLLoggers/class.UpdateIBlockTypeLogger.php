<?php

/**
 * Class UpdateIBlockTypeLogger
 */
class UpdateIBlockTypeLogger extends SQLLoggerAbstract
{
    private $res = array();

    /**
     *
     */
    function prepare()
    {
        $arQuery = explode(', ', $this->getStringBetween("SET ", " WHERE"));
        foreach ($arQuery as $val) {
            $ar = explode(' = ', $val);
            $this->res[$ar[0]] = $ar[1];
        }
    }

    /**
     *
     */
    function setHint()
    {
        $this->hint  = "Изменен тип информационного блока ID=" . $this->getWordAfter("WHERE ID=") . ". Актуальные поля:\n";
        $this->hint .= var_export($this->res, true);
    }
}