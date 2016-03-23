<?php

/**
 * Class DeleteLangLogger
 */
class DeleteLangLogger extends SQLLoggerAbstract
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
        $this->hint  = "Удален сайт LID=" . $this->getWordAfter("WHERE LID=") . ".\n";
    }
}