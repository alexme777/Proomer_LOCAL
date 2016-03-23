<?php

/**
 * Class AddSalePaySystemActionLogger
 */
class AddSalePaySystemActionLogger extends SQLLoggerAbstract
{
    private $keys;

    private $vals;

    private $output;

    /**
     *
     */
    function prepare()
    {
        $this->keys = explode(", ", $this->getStringBetween('b_sale_pay_system_action(', ')'));
        $this->vals = explode("', '", $this->getStringAfter("VALUES("));

        $this->output = array_combine($this->keys, $this->vals);
    }

    /**
     *
     */
    function setHint()
    {
        $this->hint  = "Добавлен новый Обработчик для типа плательщика (PERSON_TYPE_ID = ".$this->output['`PERSON_TYPE_ID`']."), платежной системы (PAY_SYSTEM_ID = ".$this->output['`PAY_SYSTEM_ID`']."). Поля:\n";
        $this->hint .= var_export($this->output, true);
    }
}