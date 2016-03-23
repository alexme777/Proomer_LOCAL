<?php

/**
 * Class AddIBlockTypeLangLogger
 */
class AddIBlockTypeLangLogger extends SQLLoggerAbstract
{
    /**
     * @var array
     */
    private $keys = array();

    /**
     * @var array
     */
    private $vals = array();

    private $BTId;

    private $LId;

    /**
     *
     */
    function prepare()
    {
        $this->keys = explode(', ', $this->getStringBetween('b_iblock_type_lang(IBLOCK_TYPE_ID, LID,', ')'));
        $this->vals = explode(', ', $this->getStringBetween('SELECT BT.ID, L.LID,', 'FROM'));

        $this->BTId = $this->getWordAfter("BT.ID=");
        $this->LId  = $this->getWordAfter("L.LID=");
    }

    /**
     *
     */
    function setHint()
    {
        $this->hint  = "Добавлены языковые настройки (LID=". $this->LId .") для типа информационного блока ID=". $this->BTId .". Поля:\n";
        $this->hint .= var_export(array_combine($this->keys, $this->vals), true);
    }
}