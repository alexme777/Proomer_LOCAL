<?php

/**
 * Class AddUserFieldLogger
 */
class AddUserFieldLogger extends SQLLoggerAbstract
{
    private $keys;

    private $vals;

    private $output;

    /**
     *
     */
    function prepare()
    {
        $this->keys = explode(", ", $this->getStringBetween('b_user_field(', ')'));
        $this->vals = explode(", ", $this->getStringBetween("VALUES(", ")"));

        $this->output = array_combine($this->keys, $this->vals);
    }

    /**
     *
     */
    function setHint()
    {
        $this->hint  = "Добавлено новое пользовательское поле (код поля: " .$this->output['`FIELD_NAME`']. ") для объекта " .$this->output['`ENTITY_ID`']. ". Поля:\n";
        $this->hint .= var_export($this->output, true);
    }
}