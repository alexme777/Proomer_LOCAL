<?php

/**
 * Class UpdateLangLogger
 */
class UpdateLangLogger extends SQLLoggerAbstract
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
        $this->hint  = "Изменен сайт LID = ". $this->getWordAfter("WHERE LID=") .". Актуальные поля:\n";
        $this->hint .= var_export($this->res, true);
    }
}