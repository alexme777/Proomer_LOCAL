<?php

/**
 * Class AddEventTypeLogger
 */
class AddEventTypeLogger extends SQLLoggerAbstract
{
    /**
     * @var
     */
    private $arFields;

    /**
     *
     */
    function prepare()
    {
        $strFields = $this->getStringBetween("VALUES(", ")");
        $this->arFields = explode(', ', $strFields);
    }

    /**
     *
     */
    function setHint()
    {
        $this->hint  = "Добавлен новый тип почтового события:\n";
        $this->hint .= "    Сортировка: " . $this->arFields[0] . "\n";
        $this->hint .= "    Описание: " . $this->arFields[1] . "\n";
        $this->hint .= "    Название: " . $this->arFields[2] . "\n";
        $this->hint .= "    Код почтового события: " . $this->arFields[3] . "\n";
        $this->hint .= "    Сайт: " . $this->arFields[4] . "\n";
    }
}