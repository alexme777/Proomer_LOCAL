<?php

/**
 * Class DeleteUserFieldLogger
 */
class DeleteUserFieldLogger extends SQLLoggerAbstract
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
        $this->hint  = "Удалено пользовательское поле ID=" . $this->getWordAfter("WHERE ID = ") . ".\n";
    }
}