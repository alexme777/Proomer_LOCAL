<?php

/**
 * Class SQLLoggerAbstract
 */
abstract class SQLLoggerAbstract
{
    /**
     * @var
     */
    protected $query;

    /**
     * @var
     */
    protected $hint;

    /**
     * @param $query
     */
    public function __construct($query)
    {
        $this->setQuery($query);
        $this->prepare();
        $this->setHint();
    }

    /**
     * @param $query
     */
    protected function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * @return mixed
     */
    abstract function prepare();

    /**
     * @return mixed
     */
    abstract function setHint();

    /**
     * @return mixed
     */
    public function getHint()
    {
        $str = $this->hint."\n".$this->query;
        return $str;
    }

    /**
     * @param $str
     * @return mixed
     */
    protected function getWordAfter($str)
    {
        $pattern = "/(?<=" . $str . ")\S+/i";
        preg_match($pattern, $this->query, $out);

        return $out[0];
    }

    /**
     * @param $start
     * @param $end
     * @return string
     */
    protected function getStringBetween($start, $end)
    {
        $string = " " . $this->query;
        $pos = strpos($string, $start);

        if ($pos == 0) {
            return "";
        }

        $pos += strlen($start);
        $len = strpos($string, $end, $pos) - $pos;

        return substr($string, $pos, $len);
    }


    /**
     * @param $start
     * @param bool $delLast
     * @return string
     */
    protected function getStringAfter($start, $delLast=true)
    {
        $string = " " . $this->query;
        $pos = strpos($string, $start);

        if ($pos == 0) {
            return "";
        }

        $pos += strlen($start);

        if($delLast)
            return substr($string, $pos, -1);
        else
            return substr($string, $pos);
    }
}