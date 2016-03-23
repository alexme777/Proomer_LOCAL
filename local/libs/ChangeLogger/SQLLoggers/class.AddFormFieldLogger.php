<?php

/**
 * Class AddFormFieldLogger
 */
class AddFormFieldLogger extends SQLLoggerAbstract
{
    private $keys;

    private $vals;

    /**
     *
     */
    function prepare()
    {
        $this->keys = explode(', ', $this->getStringBetween('b_form_field(', ')'));
        $this->vals = explode(", ", $this->getStringBetween("VALUES (", "')"));
    }

    /**
     *
     */
    function setHint()
    {
        $this->hint  = "Добавлен новый вопрос для вэб-формы FORM_ID=".trim($vals[count($vals)-1], "'").". Поля:\n";
        $this->hint .= var_export(array_combine($this->keys, $this->vals), true);
    }
}