<?php

/**
 * Class AddSalePaySystemLogger
 */
class AddSalePaySystemLogger extends SQLLoggerAbstract
{
    private $keys;

    private $vals;

    private $output;

    /**
     *
     */
    function prepare()
    {
        $this->keys = explode(", ", $this->getStringBetween('b_sale_pay_system(', ')'));
        $this->vals = explode(", ", $this->getStringAfter("VALUES("));

        $this->output = array_combine($this->keys, $this->vals);
    }

    /**
     *
     */
    function setHint()
    {
        $this->hint  = "Добавлена новая платежная система. Поля:\n";
        $this->hint .= var_export($this->output, true);
    }
}