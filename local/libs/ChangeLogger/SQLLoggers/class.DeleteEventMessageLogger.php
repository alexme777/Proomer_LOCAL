<?php

/**
 * Class DeleteEventMessageLogger
 */
class DeleteEventMessageLogger extends SQLLoggerAbstract
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
        $this->hint = "Удален шаблон ID=" . $this->getWordAfter("WHERE ID=") . " почтового события.\n";
    }
}