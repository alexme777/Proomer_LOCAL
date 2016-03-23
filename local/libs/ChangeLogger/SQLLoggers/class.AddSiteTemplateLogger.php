<?php

/**
 * Class AddSiteTemplateLogger
 */
class AddSiteTemplateLogger extends SQLLoggerAbstract
{
    private $keys;

    private $vals;

    private $output;

    /**
     *
     */
    function prepare()
    {
        $this->keys = explode(", ", $this->getStringBetween('b_site_template(', ')'));
        $this->vals = explode(", ", $this->getStringAfter("VALUES("));

        $this->output = array_combine($this->keys, $this->vals);
    }

    /**
     *
     */
    function setHint()
    {
        $this->hint  = "Изменен шаблон сайта LID = ". $arOutput['SITE_ID'] .". Поля:\n";
        $this->hint .= var_export($this->output, true);
    }
}