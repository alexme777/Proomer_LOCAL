<?php

/**
 * Class AddEventMessageLogger
 */
class AddEventMessageLogger extends SQLLoggerAbstract
{
    /**
     * @var array
     */
    private $keys = array();

    /**
     * @var array
     */
    private $vals = array();

    /**
     *
     */
    function prepare()
    {
        $this->keys = explode(', ', $this->getStringBetween('b_event_message(', ')'));
        $this->vals = explode("', '", $this->getStringAfter("VALUES('"));
    }

    /**
     *
     */
    function setHint()
    {
        $this->hint  = "Добавлен новый шаблон почтового события. Поля:\n";
        $this->hint .= var_export(array_combine($this->keys, $this->vals), true);
    }
}