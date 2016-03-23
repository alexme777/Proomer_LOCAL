<?php

/**
 * Class DropTableLogger
 */
class DropTableLogger extends SQLLoggerAbstract
{
    /**
     *
     */
    function prepare()
    {

    }

    /**
     *
     */
    function setHint()
    {
        $this->hint = "Удалена таблица";
    }
}