<?php

/**
 * Class DeleteFormFieldLogger
 */
class DeleteFormFieldLogger extends SQLLoggerAbstract
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
        $this->hint  = "Удален вопрос ID=" . $this->getWordAfter("WHERE ID=") . " вэб-формы.\n";
    }
}