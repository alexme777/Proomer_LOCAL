<?php

/**
 * Class AddSaleOrderPropsLogger
 */
class AddSaleOrderPropsLogger extends SQLLoggerAbstract
{
    private $keys;

    private $vals;

    private $output;

    /**
     *
     */
    function prepare()
    {
        $this->keys = explode(", ", $this->getStringBetween('b_sale_order_props(', ')'));
        $this->vals = explode(", ", $this->getStringAfter("VALUES("));

        $this->output = array_combine($this->keys, $this->vals);
    }

    /**
     *
     */
    function setHint()
    {
        $this->hint  = "Добавлено новое свойство заказа. Поля:\n";
        $this->hint .= var_export($this->output, true);
    }
}