<?php

/**
 * Class CreateTableLogger
 */
class CreateTableLogger extends SQLLoggerAbstract
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
        $this->hint = "Добавлена таблица";
    }
}