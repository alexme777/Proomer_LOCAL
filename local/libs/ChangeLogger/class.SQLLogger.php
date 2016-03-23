<?php

/**
 * Class SQLLogger
 */
class SQLLogger
{
    /**
     * @var
     */
    protected static $_instance;

    /**
     * @var
     */
    private $query;

    /**
     * @var
     */
    private $type;

    /**
     *
     */
    final private function __construct() {}

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
     * @param $query
     * @return $this
     */
    public function setQuery($query)
    {
        $this->query = trim($query);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        /*$this->setType('Default');
        return true;*/
        
        // Изменена структура таблицы
        if (strripos($this->query, 'ALTER TABLE')  === 0) {
            $this->setType('alterTable');
            return true;
        }

        // Добавлена таблица
        if (strripos($this->query, 'CREATE TABLE') === 0) {
            $this->setType('createTable');
            return true;
        }

        // Удалена таблица
        if (strripos($this->query, 'DROP TABLE')   === 0) {
            $this->setType('dropTable');
            return true;
        }

        // Добавлен новый тип почтового события
        if (strripos($this->query, 'INSERT INTO b_event_type') === 0) {
            $this->setType('addEventType');
            return true;
        }

        // Изменен тип почтового события
        if (strripos($this->query, 'UPDATE b_event_type') === 0) {
            $this->setType('updateEventType');
            return true;
        }

        // Удален тип почтового события
        if (strripos($this->query, 'DELETE FROM b_event_type') === 0) {
            $this->setType('deleteEventType');
            return true;
        }

        // Добавлен новый шаблон почтового события
        if (strripos($this->query, "INSERT INTO b_event_message(") === 0) {
            $this->setType('addEventMessage');
            return true;
        }

        // Изменен шаблон почтового события
        if (strripos($this->query, "UPDATE b_event_message ") === 0) {
            $this->setType('updateEventMessage');
            return true;
        }

        // Удален шаблон почтового события
        if (strripos($this->query, "DELETE FROM b_event_message ") === 0) {
            $this->setType('deleteEventMessage');
            return true;
        }

        // Добавлен новый тип информационного блока
        if (strripos($this->query, "INSERT INTO b_iblock_type(") === 0) {
            $this->setType('addIBlockType');
            return true;
        }

        // Добавлены новые языковые настройки для типа информационного блока
        if (strripos($this->query, "INSERT INTO b_iblock_type_lang(") === 0) {
            $this->setType('addIBlockTypeLang');
            return true;
        }

        // Изменен тип информационного блока
        if (strripos($this->query, "UPDATE b_iblock_type ") === 0) {
            $this->setType('updateIBlockType');
            return true;
        }

        // Удален тип информационного блока
        if (strripos($this->query, "DELETE FROM b_iblock_type ") === 0) {
            $this->setType('deleteIBlockType');
            return true;
        }

        // Изменение прав доступа информационного блока. Расширенное управление правами ВЫКЛЮЧЕНО
        if (strripos($this->query, "INSERT INTO b_iblock_group(") === 0) {
            $this->setType('addIBlockGroup');
            return true;
        }

        // Изменение прав доступа информационного блока. Расширенное управление правами ВКЛЮЧЕНО
        if (strripos($this->query, "INSERT INTO b_iblock_right(") === 0) {
            $this->setType('addIBlockRight');
            return true;
        }

        // Добавление новой вэбформы
        if (strripos($this->query, "INSERT INTO b_form(") === 0) {
            $this->setType('addForm');
            return true;
        }

        // Изменение существующей вэбформы
        if (strripos($this->query, "UPDATE b_form ") === 0) {
            $this->setType('updateForm');
            return true;
        }

        // Удаление вэбформы
        if (strripos($this->query, "DELETE FROM b_form ") === 0) {
            $this->setType('deleteForm');
            return true;
        }

        // Добавление вопроса вэбформы
        if (strripos($this->query, "INSERT INTO b_form_field(") === 0) {
            $this->setType('addFormField');
            return true;
        }

        // Изменение вопроса вэбформы
        if (strripos($this->query, "UPDATE b_form_field SET `TIMESTAMP_X`") === 0) {
            $this->setType('updateFormField');
            return true;
        }

        // Удаление вопроса вэбформы
        if (strripos($this->query, "DELETE FROM b_form_field ") === 0) {
            $this->setType('deleteFormField');
            return true;
        }

        // Добавление нового пользовательского поля
        if (strripos($this->query, "INSERT INTO b_user_field(") === 0) {
            $this->setType('addUserField');
            return true;
        }

        // Изменение пользовательского поля
        if (strripos($this->query, "UPDATE b_user_field SET ") === 0) {
            $this->setType('updateUserField');
            return true;
        }

        // Удаление пользовательского поля
        if (strripos($this->query, "DELETE FROM b_user_field ") === 0) {
            $this->setType('deleteUserField');
            return true;
        }

        // Добавление нового сайта
        if (strripos($this->query, "INSERT INTO b_lang(") === 0) {
            $this->setType('addLang');
            return true;
        }

        // Изменение существующего сайта
        if (strripos($this->query, "UPDATE b_lang SET `EMAIL`") === 0) {
            $this->setType('updateLang');
            return true;
        }

        // Удаление существующего сайта
        if (strripos($this->query, "DELETE FROM b_lang ") === 0) {
            $this->setType('deleteLang');
            return true;
        }

        // Изменение привязки шаблонов к сайту
        if (strripos($this->query, "INSERT INTO b_site_template(") === 0) {
            $this->setType('addSiteTemplate');
            return true;
        }

        // Добавление группы свойств заказа
        if (strripos($this->query, "INSERT INTO b_sale_order_props_group(") === 0) {
            $this->setType('addSaleOrderPropsGroup');
            return true;
        }

        // Изменение группы свойств заказа
        if (strripos($this->query, "UPDATE b_sale_order_props_group ") === 0) {
            $this->setType('updateSaleOrderPropsGroup');
            return true;
        }

        // Удаление группы свойств заказа
        if (strripos($this->query, "DELETE FROM b_sale_order_props_group ") === 0) {
            $this->setType('deleteSaleOrderPropsGroup');
            return true;
        }

        // Добавление свойства заказа
        if (strripos($this->query, "INSERT INTO b_sale_order_props(") === 0) {
            $this->setType('addSaleOrderProps');
            return true;
        }

        // Изменение свойства заказа
        if (strripos($this->query, "UPDATE b_sale_order_props ") === 0) {
            $this->setType('updateSaleOrderProps');
            return true;
        }

        // Удаление свойства заказа
        if (strripos($this->query, "DELETE FROM b_sale_order_props WHERE ID") === 0) {
            $this->setType('deleteSaleOrderProps');
            return true;
        }

        // Добавление платежной системы
        if (strripos($this->query, "INSERT INTO b_sale_pay_system(") === 0) {
            $this->setType('addSalePaySystem');
            return true;
        }

        // Изменение платежной системы
        if (strripos($this->query, "UPDATE b_sale_pay_system ") === 0) {
            $this->setType('updateSalePaySystem');
            return true;
        }

        // Удаление платежной системы
        if (strripos($this->query, "DELETE FROM b_sale_pay_system WHERE ID") === 0) {
            $this->setType('deleteSalePaySystem');
            return true;
        }

        // Добавление Обработчика для типа плательщика
        if (strripos($this->query, "INSERT INTO b_sale_pay_system_action(") === 0) {
            $this->setType('addSalePaySystemAction');
            return true;
        }

        // Изменение Обработчика для типа плательщика
        if (strripos($this->query, "UPDATE b_sale_pay_system_action ") === 0) {
            $this->setType('updateSalePaySystemAction');
            return true;
        }

        // Удаление Обработчика для типа плательщика
        if (strripos($this->query, "DELETE FROM b_sale_pay_system_action WHERE ID") === 0) {
            $this->setType('deleteSalePaySystemAction');
            return true;
        }

        // Добавление настраиваемой службы доставки
        if (strripos($this->query, "INSERT INTO b_sale_delivery(") === 0) {
            $this->setType('addSaleDelivery');
            return true;
        }

        // Изменение настраиваемой службы доставки
        if (strripos($this->query, "UPDATE b_sale_delivery ") === 0) {
            $this->setType('updateSaleDelivery');
            return true;
        }

        // Удаление настраиваемой службы доставки
        if (strripos($this->query, "DELETE FROM b_sale_delivery WHERE ID") === 0) {
            $this->setType('deleteSaleDelivery');
            return true;
        }

        // Добавление автоматизированной службы доставки
        if (strripos($this->query, "INSERT INTO b_sale_delivery_handler(") === 0) {
            $this->setType('addSaleDeliveryHandler');
            return true;
        }

        // Изменение автоматизированной службы доставки
        if (strripos($this->query, "UPDATE b_sale_delivery_handler ") === 0) {
            $this->setType('updateSaleDeliveryHandler');
            return true;
        }

        // Удаление автоматизированной службы доставки
        if (strripos($this->query, "DELETE FROM b_sale_delivery_handler WHERE HID") === 0) {
            $this->setType('deleteSaleDeliveryHandler');
            return true;
        }

        return false;
    }

    /**
     * @param $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string | bool
     */
    public function getHint()
    {
        require_once(realpath(dirname(__FILE__)) . '/SQLLoggers/class.SQLLoggerAbstract.php');

        $class = ucfirst($this->type) . "Logger";
        $file = realpath(dirname(__FILE__)) . "/SQLLoggers/class." . $class . ".php";

        if (file_exists($file)) {
            require_once($file);
            $logger = new $class($this->query);
            return $logger->getHint();
        } else {
            return false;
        }
    }
}