<?php

/**
 * Class AddSaleDeliveryHandlerLogger
 */
class AddSaleDeliveryHandlerLogger extends SQLLoggerAbstract
{
    private $keys;

    private $vals;

    private $output;

    /**
     *
     */
    function prepare()
    {
        $this->keys = explode(", ", $this->getStringBetween('b_sale_delivery_handler(', ')'));
        $this->vals = explode(", ", $this->getStringAfter("VALUES ("));

        $this->output = array_combine($this->keys, $this->vals);
    }

    /**
     *
     */
    function setHint()
    {
        $this->hint  = "Активирована автоматизированная служба доставки. Поля:\n";
        $this->hint .= var_export($this->output, true);
    }
}