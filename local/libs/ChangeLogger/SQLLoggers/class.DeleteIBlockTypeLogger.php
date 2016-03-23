<?php

/**
 * Class DeleteIBlockTypeLogger
 */
class DeleteIBlockTypeLogger extends SQLLoggerAbstract
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
        $this->hint  = "Удален тип информационного блока ID=" . $this->getWordAfter("WHERE ID=") . ".\n";
    }
}