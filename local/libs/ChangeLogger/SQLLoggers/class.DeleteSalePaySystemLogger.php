<?php

/**
 * Class DeleteSalePaySystemLogger
 */
class DeleteSalePaySystemLogger extends SQLLoggerAbstract
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
        $this->hint  = "Удалена платежная система ID = " . $this->getWordAfter("WHERE ID = ") . ".\n";
    }
}