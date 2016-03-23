<?php

/**
 * Class DeleteSaleOrderPropsLogger
 */
class DeleteSaleOrderPropsLogger extends SQLLoggerAbstract
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
        $this->hint  = "Удалено свойство ID = " . $this->getWordAfter("WHERE ID = ") . " заказа.\n";
    }
}