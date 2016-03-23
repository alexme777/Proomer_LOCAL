<?php

/**
 * Class UpdateSalePaySystemActionLogger
 */
class UpdateSalePaySystemActionLogger extends SQLLoggerAbstract
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
        $this->hint  = "Изменен Обработчик ID = ". $this->getWordAfter("WHERE ID = ") ." для типа плательщика (PERSON_TYPE_ID = ".$this->res['`PERSON_TYPE_ID`']."), платежной системы (PAY_SYSTEM_ID = ".$this->res['`PAY_SYSTEM_ID`']."). Актуальные поля:\n";
        $this->hint .= var_export($this->res, true);
    }
}