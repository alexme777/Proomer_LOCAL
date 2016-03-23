<?php

/**
 * Class DeleteSaleDeliveryHandlerLogger
 */
class DeleteSaleDeliveryHandlerLogger extends SQLLoggerAbstract
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
        $this->hint  = "Очищены настройки автоматизированной службы доставки HID = " . $this->getWordAfter("WHERE HID=") . ".\n";
    }
}