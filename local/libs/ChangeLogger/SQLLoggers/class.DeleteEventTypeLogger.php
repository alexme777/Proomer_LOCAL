<?php

/**
 * Class DeleteEventTypeLogger
 */
class DeleteEventTypeLogger extends SQLLoggerAbstract
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
        $this->hint = "Удален тип почтового события " . $this->getWordAfter("EVENT_NAME=") . "\n";
    }
}