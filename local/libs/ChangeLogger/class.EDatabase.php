<?php
require_once(realpath(dirname(__FILE__)) . '/class.ChangeLogger.php');

class EDatabase extends CDatabase
{
	public $bxShowSqlStat = false;
	
    public function edbInitFromObject(CDatabase $db){
        $state = get_object_vars($db);
        foreach ($state as $varName=>$varValue) {
            $this->$varName = $varValue;
        }
        unset($state);
		
		$this->bxShowSqlStat = $db->ShowSqlStat;
    }

    public function addDebugQuery($strSql, $execTime) {
        ChangeLogger::getInstance()->logQuery($strSql);
		
		if ($this->bxShowSqlStat) {
			parent::addDebugQuery($strSql, $execTime);
		}
    }
}