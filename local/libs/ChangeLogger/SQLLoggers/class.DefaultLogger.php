<?php

/**
 * Class DefaultLogger
 */
class DefaultLogger extends SQLLoggerAbstract
{
    private $keys;

    private $vals;

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
        $this->hint  = "Наверное что-то случилось...:\n";
        $this->hint .= $this->query;
    }
}