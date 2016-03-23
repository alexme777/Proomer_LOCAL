<?php

/**
 * Class AddFormLogger
 */
class AddFormLogger extends SQLLoggerAbstract
{
    private $keys;

    private $vals;

    /**
     *
     */
    function prepare()
    {
        $this->keys = explode(', ', $this->getStringBetween('b_form(', ')'));
        $this->vals = explode(', ', $this->getStringBetween('VALUES(', ')'));
    }

    /**
     *
     */
    function setHint()
    {
        $this->hint  = "Добавлена новая вэб-форма. Поля:\n";
        $this->hint .= var_export(array_combine($this->keys, $this->vals), true);
    }
}