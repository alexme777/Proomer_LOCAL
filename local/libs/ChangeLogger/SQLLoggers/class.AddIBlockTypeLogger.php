<?php

/**
 * Class AddIBlockTypeLogger
 */
class AddIBlockTypeLogger extends SQLLoggerAbstract
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
        $this->keys = explode(', ', $this->getStringBetween('b_iblock_type(', ')'));
        $this->vals = explode(', ', $this->getStringBetween('VALUES(', ')'));
    }

    /**
     *
     */
    function setHint()
    {
        $this->hint  = "Добавлен тип информационного блока. Поля:\n";
        $this->hint .= var_export(array_combine($this->keys, $this->vals), true);
    }
}