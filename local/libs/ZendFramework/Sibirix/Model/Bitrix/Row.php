<?
/**
 *
 */
abstract class Sibirix_Model_Bitrix_Row implements JsonSerializable {

    protected $_data = null;
    public function __construct() {

    }

    public function setData($data) {
        $this->_data = $data;
        return $this;
    }

    public function hasField($name) {
        if (null === $this->_data) {
            return false;
        }
        if (array_key_exists($name . '_VALUE', $this->_data)) {
            return true;
        }
        if (array_key_exists($name, $this->_data)) {
            return true;
        }
        $method = 'get' . ucfirst($name);
        if (method_exists($this, $method)) {
            return true;
        }
        return false;
    }

    public function __isset($name) {
        return is_array($this->_data) ? array_key_exists($name, $this->_data) : false;
    }

    public function __get($name) {
        if (null === $this->_data) {
            throw new Zend_Db_Exception("instance not loaded");
        }

        if (array_key_exists($name . '_VALUE', $this->_data)) {
            return $this->_data[$name . '_VALUE'];
        }

        if (array_key_exists($name, $this->_data)) {
            return $this->_data[$name];
        }

        $method = 'get' . ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->{$method}();
        }

        throw new Zend_Db_Exception("not found field [$name]");
    }

    public function __set($name, $value) {
        $this->_data[$name] = $value;
    }

    public function getRawData() {
        return $this->_data;
    }

    protected function _normalizeFields($data) {
        $_fields = [];
        foreach ($data as $name => $value) {
            if ('_VALUE_ID' === substr($name, -9)) {
                continue;
            } else if ('_VALUE' === substr($name, -6)) {
                $name = substr($name, 0, strlen($name) - 6);
            }

            $_fields[$name] = $value;
        }

        return $_fields;
    }

    public function getSaveArray() {
        $_fields = [];
        $_property = [];
        $_data = $this->_normalizeFields($this->_data);
        foreach ($_data as $name => $value) {
            if ('PROPERTY_' == substr($name, 0, 9)) {
                $_property[substr($name, 9)] = $value;
            } else {
                $_fields[$name] = $value;
            }
        }

        $_fields['PROPERTY_VALUES'] = $_property;

        return $_fields;
    }

    /**
     * поддержка Zend_Json
     *
     * @return array
     */
    public function toArray() {
        return $this->_data;
    }

    /**
     * @return array
     */
    public function jsonSerialize() {
        return $this->toArray();
    }
}