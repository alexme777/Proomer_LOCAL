<?php

/**
 * Class AlterTableLogger
 */
class AlterTableLogger extends SQLLoggerAbstract
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
        $this->hint = "Изменена стуктуры таблицы " . $this->getWordAfter("ALTER TABLE ");
    }
}