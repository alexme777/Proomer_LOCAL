<?php

/**
 * Class DeleteSalePaySystemActionLogger
 */
class DeleteSalePaySystemActionLogger extends SQLLoggerAbstract
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
        $this->hint  = "Удален Обработчик ID = " . $this->getWordAfter("WHERE ID = ") . " для типа плательщика.\n";
    }
}