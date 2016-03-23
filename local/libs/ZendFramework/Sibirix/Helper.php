<?
/**
 * Class Sibirix_Helper
 */
class Sibirix_Helper {

    /**
     * @var bool
     */
    private static $log = false;

    /**
     * Логирование
     * @return bool
     */
    static function log() {
		if (self::$log === false) {
			self::$log = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('Log');
		}
		return self::$log;
	}

    /**
     * Получение опций из конфига
     * @param $name
     * @param null $section
     * @return null
     */
    static function getOption($name, $section = null) {
		$options = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption($name);
		if ($section) {
			$options = (isset($options[$section]) ? $options[$section] : null);
		}
		return $options;
	}
}