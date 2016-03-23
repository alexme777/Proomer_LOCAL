<?php

/**
 * Class UpdateSaleDeliveryHandlerLogger
 */
class UpdateSaleDeliveryHandlerLogger extends SQLLoggerAbstract
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
        $this->hint  = "Изменена автоматизированная служба доставки HID = " . $this->getWordAfter("WHERE HID=") . ". Актуальные поля:\n";
        $this->hint .= var_export($this->res, true);
    }
}