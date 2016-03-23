<?php

/**
 * Class UpdateEventTypeLogger
 */
class UpdateEventTypeLogger extends SQLLoggerAbstract
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
        $this->hint  = "Изменен тип почтового события с ID=" . $this->getStringBetween("ID='", "'") . " . Актуальные поля:\n";
        $this->hint .= "    Сортировка: " . $this->getStringBetween("`SORT` = ", ",") . "\n";
        $this->hint .= "    Описание: " . $this->getStringBetween("`DESCRIPTION` = ", ",") . "\n";
        $this->hint .= "    Название: " . $this->getStringBetween("`NAME` = ", ",") . "\n";
        $this->hint .= "    Код почтового события: " . $this->getStringBetween("`EVENT_NAME` = ", ",") . "\n";
        $this->hint .= "    Сайт: " . $this->getStringBetween("`LID` = ", " ") . "\n";
    }
}