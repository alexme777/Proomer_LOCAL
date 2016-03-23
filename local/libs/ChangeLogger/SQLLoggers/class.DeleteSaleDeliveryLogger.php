<?php

/**
 * Class DeleteSaleDeliveryLogger
 */
class DeleteSaleDeliveryLogger extends SQLLoggerAbstract
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
        $this->hint  = "Удалена настраиваемая служба доставки ID = " . $this->getWordAfter("WHERE ID = ") . ".\n";
    }
}