<?php

/**
 * Class UpdateEventMessageLogger
 */
class UpdateEventMessageLogger extends SQLLoggerAbstract
{
    /**
     * @var array
     */
    private $changes = array();

    /**
     *
     */
    function prepare()
    {
        $arQuery = explode(', ', $this->getStringBetween("SET ", " WHERE"));
        foreach ($arQuery as $val) {
            $ar = explode(' = ', $val);
            $this->changes[$ar[0]] = $ar[1];
        }
    }

    /**
     *
     */
    function setHint()
    {
        $this->hint  = "Изменен шаблон ID=" . $this->getWordAfter("WHERE ID=") . " почтового события. Актуальные поля:\n";
        $this->hint .= var_export($this->changes, true);
    }
}