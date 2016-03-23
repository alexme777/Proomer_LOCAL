<?php

/**
 * Class AddIBlockRightLogger
 */
class AddIBlockRightLogger extends SQLLoggerAbstract
{
    private $keys;

    private $vals;

    /**
     *
     */
    function prepare()
    {
        $this->keys = explode(', ', $this->getStringBetween('b_iblock_right(', ')'));
        $this->vals = explode(', ', $this->getStringBetween('VALUES(', ')'));
    }

    /**
     *
     */
    function setHint()
    {
        $this->hint  = "Изменены права доступа инфоблока. Расширенное управление правами ВКЛЮЧЕНО.\n";
        $this->hint .= var_export(array_combine($this->keys, $this->vals), true);
    }
}