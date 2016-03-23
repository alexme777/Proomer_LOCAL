<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

/**
 * Class EDebug
 *
 * содержит функции двух типов:
 *      - управление уровнем отладки
 *
 * не зависит от сторонних классов
 */
class EDebug {
    /**
     * Пути и файлы которые нужно игнорировать при отладке
     * @var array
     */
    static protected $paths = array();

    /**
     * @var int
     */
    static protected $errorReportingLevel = 0;

    static public function init($errorLevel, $ignoringPaths = array()) {
        Bitrix\Main\Application::getInstance()->getExceptionHandler()->setDebugMode(true);
        foreach ($ignoringPaths as $iPath) {
            self::addIgnoringPath($iPath);
        }

        self::setErrorLevel($errorLevel);
    }

    /**
     * Очищает буфер
     */
    static public function clearOutput() {
        while (ob_get_length()) {
            ob_end_clean();
        }
    }

    /**
     * Добавляет путь для игнорирования при отладке
     * @param $path
     */
    static public function addIgnoringPath($path) {
        self::$paths[] = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
    }

    /**
     * Устаналивает уровень логгирования ошибок
     * @param int $level
     * @return int
     */
    static public function setErrorLevel($level = E_WARNING) {

        $oldErrorReportingLevel = self::$errorReportingLevel;

        if ($level !== null) {
            self::$errorReportingLevel = $level;
        }

        set_error_handler(array(__CLASS__, 'debugErrorHandler'));

        return $oldErrorReportingLevel;
    }

    /**
     * Обработчик ошибок
     *
     * @param $errNo
     * @param $errStr
     * @param string $errFile
     * @param int $errLine
     * @param array $errContext
     * @return bool
     */
    static public function debugErrorHandler($errNo, $errStr, $errFile = '', $errLine = 0, /** @noinspection PhpUnusedParameterInspection */
                                             $errContext = array()) {

        if (error_reporting() == 0 || !(self::$errorReportingLevel & $errNo)) {
            return true;
        }

        // проверка что файл в игноре
        if (self::isIgnoredFile($errFile)) {
            return true;
        }

        // форматирование вывода ошибки

        self::templateError($errLine, $errFile, $errNo, $errStr);

        return true;
    }

    static protected function trimFilename($filename, $value) {
        if (strpos($filename, $value) === 0) {
            $count = 1;
            $filename = str_replace($value, '', $filename, $count);
        }
        return $filename;
    }


    /**
     * Проверка что файл является заигноренным
     *
     * @param $filename
     * @return bool
     */
    static private function isIgnoredFile($filename) {
        static $root = null;
        if ($root === null) {
            $root = self::fullRealPath($_SERVER['DOCUMENT_ROOT']);
        }
        $filename = realpath($filename);

        $filename = self::trimFilename($filename, $root);

        if (empty($filename)) {
            return true;
        }

        foreach (self::$paths as $path) {
            if (strpos($filename, $path) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Вывод ошибки
     *
     * @param $errLine
     * @param $errFile
     * @param $errNo
     * @param $errStr
     */
    static private function templateError($errLine, $errFile, $errNo, $errStr) {

        self::clearOutput();

        $backTrace = '';
        $backTraceArray = debug_backtrace();
        foreach ($backTraceArray as $num=>$stackItem) {
            $backTrace .= "    #{$num}:\n      file: " . realpath($stackItem['file']) . "\n      line: {$stackItem['line']}\n      function: {$stackItem['function']}\n\n";
        }
        unset($backTraceArray);

        $errFragment = '';
        if ($errLine > 0 && file_exists($errFile)) {
            $buffer = file($errFile);

            if ($buffer !== false && $errLine <= count($buffer)) {
                if ($errLine > 1) {
                    $errFragment = '   ' . ($errLine - 1) . ': ' . htmlspecialchars($buffer[$errLine - 2]);
                }
                $errFragment .= '   ' . $errLine . ': <font color="red">' . htmlspecialchars($buffer[$errLine - 1]) . '</font>';
                if ($errLine < count($buffer) - 1) {
                    $errFragment .= '   ' . ($errLine + 1) . ': ' . htmlspecialchars($buffer[$errLine]);
                }

                $errFragment = '<strong>' . $errFragment . '</strong>';
            }
        }

        echo "<pre>" . print_r($errFile, true) . "</pre>";
        echo "<pre>ERROR FOUND:\n  CODE: {$errNo} (" . self::friendlyErrorType($errNo) . ")\n  MESSAGE: {$errStr}\n  FILE: {$errFile}\n  LINE: {$errLine}\n" . ($errFragment != "" ? "  CODE FRAGMENT:\n{$errFragment}\n" : "") . "  STACK BACKTRACE:\n" . $backTrace . "</pre>";
        die();
    }

    /**
     * Строковое представление ошибки по её коду
     *
     * @param int $type
     * @return string
     */
    static protected function friendlyErrorType($type) {
        switch($type) {
            case E_ERROR: // 1 //
                return 'E_ERROR';
            case E_WARNING: // 2 //
                return 'E_WARNING';
            case E_PARSE: // 4 //
                return 'E_PARSE';
            case E_NOTICE: // 8 //
                return 'E_NOTICE';
            case E_CORE_ERROR: // 16 //
                return 'E_CORE_ERROR';
            case E_CORE_WARNING: // 32 //
                return 'E_CORE_WARNING';
            case E_COMPILE_ERROR: // 64 //
                return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING: // 128 //
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR: // 256 //
                return 'E_USER_ERROR';
            case E_USER_WARNING: // 512 //
                return 'E_USER_WARNING';
            case E_USER_NOTICE: // 1024 //
                return 'E_USER_NOTICE';
            case E_STRICT: // 2048 //
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR: // 4096 //
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED: // 8192 //
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED: // 16384 //
                return 'E_USER_DEPRECATED';
        }
        return "";
    }
    
    /**
     * Полное восстановление пути из Symlink и/или DOS Path
     *
     * @param string $fileName
     * @return string
     */
    static private function fullRealPath($fileName){

        do {
            $originalFileName = $fileName;
            $fileName = realpath($originalFileName);
        } while ($originalFileName !== $fileName);

        return $fileName;
    }
}
