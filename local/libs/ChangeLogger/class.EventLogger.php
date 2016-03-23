<?php

/**
 * Class EventLogger
 */
class EventLogger
{
    /**
     * @var
     */
    protected static $_instance;

    /**
     * @var
     */
    private $_events = array(
        'iblock' => array(
            'OnAfterIblockAdd',
            'OnBeforeIblockUpdate',
            'OnAfterIblockUpdate',
            'OnBeforeIblockDelete',
            'OnAfterIBlockPropertyAdd',
            'OnBeforeIBlockPropertyUpdate',
            'OnAfterIBlockPropertyUpdate',
            'OnBeforeIBlockPropertyDelete'
        ),
        'main' => array(
            'OnAfterGroupAdd',
            'OnAfterGroupUpdate',
            'OnGroupDelete',
            'OnAfterEpilog',
            'OnAfterSetOption_reports'
        ),
        'sale' => array(
            'OnPersonTypeAdd',
            'OnPersonTypeUpdate',
            'OnPersonTypeDelete',
            'OnStatusAdd',
            'OnStatusUpdate',
            'OnStatusDelete'
        ),
        'catalog' => array(
            'OnBeforeGroupAdd',
            'OnGroupUpdate',
            'OnGroupDelete'
        )
    );

    /**
     *
     */
    final private function __construct()
    {
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
     * @return EventLogger
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
    private function startLogger()
    {
        require_once(realpath(dirname(__FILE__)) . '/EventLoggers/class.ModuleEventLogger.php');

        foreach ($this->_events as $module => $events) {
            $class = ucfirst($module) . 'EventLogger';
            $file = realpath(dirname(__FILE__)) . '/EventLoggers/class.' . $class . '.php';
            //debug($file);
            if ($file) {
                require_once($file);
                foreach ($events as $event) {
                    $handler = $event . 'Handler';
                    AddEventHandler($module, $event, array($class, $handler));
                }
            }
        }

        return $this;
    }
}