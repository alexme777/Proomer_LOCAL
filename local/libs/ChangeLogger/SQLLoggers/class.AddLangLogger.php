<?php

/**
 * Class AddLangLogger
 */
class AddLangLogger extends SQLLoggerAbstract
{
    private $keys;

    private $vals;

    /**
     *
     */
    function prepare()
    {
        $this->keys = explode(", ", $this->getStringBetween('b_lang(', ')'));
        $this->vals = explode(", ", $this->getStringBetween("VALUES(", ")"));
    }

    /**
     *
     */
    function setHint()
    {
        $this->hint  = "Добавлен новый сайт. Поля:\n";
        $this->hint .= var_export(array_combine($this->keys, $this->vals), true);
    }
}