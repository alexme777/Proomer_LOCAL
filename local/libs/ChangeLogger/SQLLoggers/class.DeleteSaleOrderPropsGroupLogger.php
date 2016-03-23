<?php

/**
 * Class DeleteSaleOrderPropsGroupLogger
 */
class DeleteSaleOrderPropsGroupLogger extends SQLLoggerAbstract
{
    /**
     *
     */
    function prepare()
    {

    }

    /**
     *
     */
    function setHint()
    {
        $this->hint  = "Удалена группа свойст ID = " . $this->getWordAfter("WHERE ID = ") . " заказа.\n";
    }
}