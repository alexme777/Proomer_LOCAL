<?php

/**
 * Class ChangeLogger
 */
class ChangeLogger
{
    /**
     * @var
     */
    protected static $_instance;

    /**
     * @var
     */
    private $_sqlLogger;

    /**
     * @var
     */
    private $_eventLogger;

    /**
     * @var string
     */
    private $_logFile;

    /**
     * @var
     */
    private $_logInstance;
    
    /**
     * @var bool
     */
    private $_prevLogState;

    /**
     *
     */
    final private function __construct()
    {
        $hostname = strtolower(gethostname());
        $this->_logFile = P_LOG_DIR . "/changelog-$hostname.txt";
        $this->startLogger();
    }

    /**
     *
     */
    final private function __clone() {}

    /**
     *
     */
    final private function __wakeup() {}

    /**
     * @return ChangeLogger
     */
    public static function getInstance()
    {
        if (! isset(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * @return $this
     */
    private function setSQLLogger()
    {
        require_once(realpath(dirname(__FILE__)) . '/class.SQLLogger.php');

        $this->_sqlLogger = SQLLogger::getInstance();

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSQLLogger()
    {
        return $this->_sqlLogger;
    }

    /**
     * @param $query
     */
    public function logQuery($query)
    {
        $sqlLogger = $this->_sqlLogger->setQuery($query);

        if ($sqlLogger->validate()) {
            $this->saveToLog($sqlLogger->getHint());
        }
    }

    /**
     * @return $this
     */
    public function startLogger()
    {
        $this->openLogFile()->setSQLLogger()->setEventLogger();

        require_once(realpath(dirname(__FILE__)) . '/class.EDatabase.php');

        global $DB;
        if (get_class($DB) !== 'EDatabase') {
            // Создание нового класса CDatabase
            $newDB = new EDatabase();
            // Клонирование свойств
            $newDB->edbInitFromObject($DB);
            $this->_prevLogState = $DB->ShowSqlStat;
            $newDB->ShowSqlStat = true;
            // Замена стандартного объекта
            $DB = $newDB;
            // Хук для остановки логера
            AddEventHandler('main', 'OnEpilog', array($this, 'stopLogger'));
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function stopLogger()
    {
        global $DB;
        if (get_class($DB) === 'EDatabase' && !$DB->bxShowSqlStat) {
            $DB->ShowSqlStat = $this->_prevLogState;
        }

        $this->closeLogFile();

        return $this;
    }

    /**
     * @return $this
     */
    public function setEventLogger()
    {
        require_once(realpath(dirname(__FILE__)) . '/class.EventLogger.php');

        $this->_eventLogger = EventLogger::getInstance();

        return $this;
    }

    /**
     * @param string $mode
     * @return $this
     */
    public function openLogFile($mode = "ab+")
    {
        if (! isset($this->_logInstance)) {
            $this->_logInstance = fopen($this->_logFile, $mode);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function closeLogFile()
    {
        @fclose($this->_logInstance);
        $this->_logInstance = null;

        return $this;
    }

    /**
     * @param $str
     * @return $this
     */
    public function saveToLog($str)
    {
    $str = date('Y-m-d H:i:s') . "\n\n" . $str;
        $str = trim($str) . "\n\n----------------------------------------------------\n\n";
        fputs($this->_logInstance, $str);

        return $this;
    }
}