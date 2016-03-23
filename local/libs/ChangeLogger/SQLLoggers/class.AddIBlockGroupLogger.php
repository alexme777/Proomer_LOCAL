<?php

/**
 * Class AddIBlockGroupLogger
 */
class AddIBlockGroupLogger extends SQLLoggerAbstract
{
    private $groupId;

    private $iblockId;
    
    private $right;

    /**
     *
     */
    function prepare()
    {
        $this->groupId  = $this->getWordAfter("WHERE ID = ");
        $this->iblockId = rtrim($this->getWordAfter("SELECT "), ',');
        $this->right    = $this->getWordAfter(", ID, ");
    }

    /**
     *
     */
    function setHint()
    {
        $this->hint  = "Изменены права доступа инфоблока. Расширенное управление правами ВЫКЛЮЧЕНО.\n";
        $this->hint .= "Инфоблок ID=".$this->iblockId.". Группа пользователей ID=".$this->groupId.". Новое значение: ".$this->right."\n";
    }
}