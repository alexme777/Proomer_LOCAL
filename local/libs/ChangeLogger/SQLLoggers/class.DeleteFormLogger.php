<?php

/**
 * Class DeleteFormLogger
 */
class DeleteFormLogger extends SQLLoggerAbstract
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
        $this->hint  = "Удалена вэб-форма ID=" . $this->getWordAfter("WHERE ID=") . ".\n";
    }
}