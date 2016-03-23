<?php

/**
 * Класс для манипуляций над массивами элементов инфоблока
 *
 * @package files
 * @subpackage classes
 *
 * Class Sibirix_Model_Bitrix_IblockHelper_Helper
 */
class Sibirix_Model_Bitrix_IblockHelper_Helper {
    /**
     * Константа режима отладки
     */
    const DEBUG = false;

    /**
     * Загрузка файлов
     *
     * @param $ids
     * @return array
     */
    static function loadFilesArray(&$ids) {
        if (empty($ids)) {
            return array();
        }

        $cFile = new CFile();
        $iterator = $cFile->GetList(array(), array("@ID" => implode(",", $ids)));

        $files = array();
        while ($file = $iterator->Fetch()) {
            $file['SRC'] = $cFile->GetFileSRC($file);
            $files[$file['ID']] = $file;
        }

        $ids = $files;
        return $ids;
    }


    /**
     * Загрузка файлов в массиве элементов
     *
     * @todo переписать с использованием других методов IBlockHelper
     * @param array $elements массив элементов инфоблока
     * @param array $keys ключи в элементе где хранятся ID файла
     * @return array результирующий массив элементов с файлами в качестве значений по ключам
     */
    static function loadFiles(&$elements, $keys = array('PREVIEW_PICTURE', 'DETAIL_PICTURE')) {
        $cFile = new CFile();

        foreach ($keys as $key) {
            // Собираем ID файлов
            $ids = array();
            foreach ($elements as &$item) {
                if (!array_key_exists($key, $item)) {
                    continue;
                }
                $value = $item[$key];

                if (!is_array($value)) {
                    $value = array($value);
                }
                $ids = array_merge($ids, $value);
            }

            if (empty($ids)) {
                continue;
            }

            // получаем их данные
            $iterator = $cFile->GetList(array(), array("@ID" => implode(",", $ids)));

            $files = array();
            while ($file = $iterator->Fetch()) {
                $files[$file['ID']] = $file;
            }

            // размещаем файлы по ключам
            foreach ($elements as &$item) {
                if (!array_key_exists($key, $item)) {
                    continue;
                }

                $value = $item[$key];
                $isSingle = !is_array($value);
                if ($isSingle) {
                    $value = array($value);
                }

                foreach ($value as &$multItem) {
                    $fileId = $multItem;
                    if (!array_key_exists($fileId, $files)) {
                        continue;
                    }
                    $file = $files[$fileId];
                    if (empty($file)) {

                        continue;
                    }
                    $file['SRC'] = $cFile->GetFileSRC($file);
                    $multItem = $file;
                }

                if ($isSingle) {

                    $value = reset($value);
                }
                $item[$key] = $value;
            }
        }

        return $elements;
    }

    /**
     * Извлекает массивы значений по ключам из массивов элементов
     *
     * <pre>
     * $elms = array(
     *   array('year' => 2000, 'id' => 1),
     *   array('year' => 2000, 'id' => 2),
     *   array('year' => 2001, 'id' => 3),
     *   array('year' => 2001, 'id' => 4),
     * );
     * </pre>
     * <code>
     *   IBlockHelper::extractFields($elms, array('year', 'id'), false);
     * </code>
     * <pre>
     * array(
     *   'year' => array(2000, 2000, 2001, 2001), // дубли остаются
     *   'id' => array(1, 2, 3, 4)
     * );
     * </pre>
     * <code>
     *   IBlockHelper::extractFields($elms, array('year', 'id'), true);
     * </code>
     * <pre>
     * array(
     *   'year' => array(2000, 2001), // оставлены только уникальные значения
     *   'id' => array(1, 2, 3, 4)
     * );
     * </pre>
     *
     * @param array $elements массив элементов
     * @param array $keys ключи по которым извлечь значения
     * @param bool $unique оставлять только уникальные значения по каждому ключу
     * @param bool $collect свалить значения по всем ключам в кучу
     * @return array
     */
    static function extractFields($elements, $keys = array('ID'), $unique = true, $collect = false) {
        if (empty($elements)) {
            return array();
        }
        $values = array();
        foreach ($keys as $key) {
            foreach ($elements as $element) {
                if (!array_key_exists($key, $element)) {
                    continue;
                }
                $values[$key][] = $element[$key];
            }
            if ($unique) {
                $values[$key] = array_unique($values[$key]);
            }
        }
        /*if (count($values) === 1) {

            $values = reset($values);
        }*/
        if ($collect) {
            $new_values = array();
            foreach ($keys as $key) {
                if (is_array($values[$key])) {
                    $new_values = array_merge($new_values, $values[$key]);
                }
            }
            $values = $unique ? array_unique($new_values) : $new_values;
        }
        return $values;
    }

    /**
     * Извлекает массив значений по ключу из массивов элементов, проверяет на уникальность
     *
     * <pre>
     * $elms = array(
     *   array('year' => 2000, 'id' => 1),
     *   array('year' => 2000, 'id' => 2),
     *   array('year' => 2001, 'id' => 3),
     *   array('year' => 2001, 'id' => 4),
     * );
     * </pre>
     * <code>
     *   IBlockHelper::extractFields($elms, 'year');
     * </code>
     * <pre>
     * array(2000, 2001); // дубли убираются
     * </pre>
     * <code>
     *   IBlockHelper::extractFields($elms, 'id');
     * </code>
     * <pre>
     * array(1, 2, 3, 4);
     * </pre>
     *
     * @param $items
     * @param $key
     * @return array
     */
    static function extractByKeyFields($items, $key) {
        $result = array();
        foreach ($items as $item) {
            if (empty($item[$key])) {
                continue;
            }
            $result[] = $item[$key];
        }

        return array_map("unserialize", array_unique(array_map("serialize", $result)));
    }

    /**
     * Удаление пустых разделов
     *
     * замечание. использовать только для выборок разделов с подсчетом количества дочерних элементов (calcCount)
     *
     * @param array $sections разделы возвращенные через класс IBlockSectionSelect
     * @throws Zend_Exception
     */
    static function filterEmptySections(&$sections) {
        foreach ($sections as $index => $section) {
            if (!array_key_exists('ELEMENT_CNT', $section)) {
                throw new Zend_Exception('You should use IBlockSectionSelect::calcCount before fetching elements!');
            }
            if (!$section['ELEMENT_CNT']) {
                unset($sections[$index]);
            }
        }
    }

    /**
     * Рекурсивно удаляет пустые элементы массива
     *
     * @param $elements
     */
    static function filterEmptyFields(&$elements) {
        foreach ($elements as $key => $value) {
            if (is_array($value)) {
                self::filterEmptyFields($elements[$key]);
            }
            if (empty($elements[$key])) {
                unset($elements[$key]);
            }
        }
    }

    /**
     * Группировка элементов по значениию поля
     *
     * <pre>
     * $elms = array(
     *   array('year' => 2000, 'id' => 1),
     *   array('year' => 2000, 'id' => 2),
     *   array('year' => 2001, 'id' => 3),
     *   array('year' => 2001, 'id' => 4),
     * );
     * </pre>
     * <code>
     *   IBlockHelper::groupByField($elms);
     * </code>
     * <pre>
     * array(
     *   '2000' => array(
     *      array('year' => 2000, 'id' => 1),
     *      array('year' => 2000, 'id' => 2),
     *   ),
     *   '2001' => array(
     *      array('year' => 2001, 'id' => 3),
     *      array('year' => 2001, 'id' => 4),
     *   )
     * );
     * </pre>
     *
     * @param $items
     * @param $field
     * @return array
     */
    static function groupByField($items, $field) {
        $result = array();
        foreach ($items as $id => $item) {
            $key = $item[$field];
            $result[$key][$id] = $item;
        }
        return $result;
    }

    /**
     * Перенос значения поля в качестве ключа массива элементов
     *
     * <pre>
     * $elms = array(
     *   0 => array('year' => 2001, 'id' => 1),
     *   1 => array('year' => 2002, 'id' => 2),
     *   2 => array('year' => 2003, 'id' => 3),
     *   3 => array('year' => 2004, 'id' => 4),
     * );
     * </pre>
     * <code>
     *   IBlockHelper::fieldToKey($elms);
     * </code>
     * <pre>
     * array(
     *   2001 => array('year' => 2001, 'id' => 1),
     *   2002 => array('year' => 2002, 'id' => 2),
     *   2003 => array('year' => 2003, 'id' => 3),
     *   2004 => array('year' => 2004, 'id' => 4),
     * );
     * </pre>
     *
     * @param array $items элементы инфоблока
     * @param string $field имя ключа
     */
    static function fieldToKey(&$items, $field) {
        $result = array();
        if (!empty($items)) {
            foreach ($items as $item) {
                $result[$item[$field]] = $item;
            }
        }
        $items = $result;
    }

    /**
     * Для каждого элемента сложного массива формирует ключ-значение
     *
     * <pre>
     * $elms = array(
     *   0 => array('id' => 1, 'year' => 2001),
     *   1 => array('id' => 2, 'year' => 2002),
     *   2 => array('id' => 3, 'year' => 2003),
     *   3 => array('id' => 4, 'year' => 2004),
     * );
     * </pre>
     * <code>
     *   IBlockHelper::fieldToKey($elms, 'id', 'year');
     * </code>
     * <pre>
     * array(
     *   1 => 2001,
     *   2 => 2002,
     *   3 => 2003,
     *   4 => 2004,
     * );
     * </pre>
     *
     * @param array $items массив ассоциативных массивов
     * @param string $key ключ индекса
     * @param string $field ключ значения
     *
     *@return array
     */
    static function fieldByKey($items, $key, $field) {
        $result = array();
        if (!empty($items)) {
            foreach ($items as $item) {
                $result[$item[$key]] = $item[$field];
            }
        }
        return $result;
    }

    /**
     * Оставляет только нужные ключи в каждом элементе массива
     *
     * <pre>
     * $elms = array(
     *   array('year' => 2001, 'id' => 1),
     *   array('year' => 2002, 'id' => 2),
     *   array('year' => 2003, 'id' => 3),
     *   array('year' => 2004, 'id' => 4),
     * );
     * </pre>
     * <code>
     *      IBlockHelper::filterFields($elms, array('year'));
     * </code>
     * <pre>
     * array(
     *   array('year' => 2001),
     *   array('year' => 2002),
     *   array('year' => 2003),
     *   array('year' => 2004),
     * );
     * </pre>
     *
     * @param array $items элементы инфоблока
     * @param array $keys массив ключей которые необходимо оставить в каждом элементе
     */
    static function filterFields(&$items, $keys) {
        foreach ($items as &$item) {
            $new_item = array();
            foreach ($keys as $key) {
                if (!array_key_exists($key, $item)) {
                    continue;
                }
                $new_item[$key] = $item[$key];
            }
            $item = $new_item;
        }
    }

    /**
     * Разбиение элементов пришедшего массива на части (колонки) поровну
     * @param $array
     * @param int $count
     *
     * @return array
     * @throws Zend_Exception
     */
    static function splitParts($array, $count = 2) {
        if ($count <= 0) {
            throw new Zend_Exception();
        }

        $index = 0;
        $parts = array_fill(0, $count, array());

        $count = count($array) / $count;
        foreach ($array as $key => $item) {
            $parts[(int)(floor($index++ / $count))][$key] = $item;
            /*$index %= $count;*/
        }
        return $parts;
    }

    /**
     * @todo Написать описание
     * @param $parts
     *
     * @return array
     */
    static function linearJoin($parts) {
        $result = array();

        while (true) {
            $shifted = false;
            foreach ($parts as &$part) {
                if (empty($part)) {
                    continue;
                }
                $shifted = true;
                $result[] = array_shift($part);
            }
            if (!$shifted) {
                break;
            }
        }

        return $result;
    }

    /**
     * Возвращает только нужные поля из структуры массива файла
     *
     * Берет структуру файла:
     * <pre>
     * array(
     *     [ID] => 98
     *     [TIMESTAMP_X] => 01.04.2014 15:19:25
     *     [MODULE_ID] => iblock
     *     [HEIGHT] => 327
     *     [WIDTH] => 262
     *     [FILE_SIZE] => 30120
     *     [CONTENT_TYPE] => image/jpeg
     *     [SUBDIR] => iblock/6e2
     *     [FILE_NAME] => 6e2f3782f3c6bc3313615b3545a4229c.jpg
     *     [ORIGINAL_NAME] => bot-7.jpg
     *     [DESCRIPTION] =>
     *     [HANDLER_ID] =>
     *     [SRC] => /upload/resized/274b/274b1b51c4a751331b304239708a59ed.jpg
     * )
     * </pre>
     * и возвращает только нужные поля из нее:
     * <pre>
     * array(
     *     [id] => 98
     *     [width] => 262
     *     [height] => 327
     *     [src] => /upload/resized/274b/274b1b51c4a751331b304239708a59ed.jpg
     * )
     * </pre>
     * Возвращаемая структура содержит след. поля: ID, SRC, WIDTH & HEIGHT
     *
     * @param array $file file structure
     * @return array
     */
    static function getImageFields($file) {
        if (empty($file['ID']) || empty($file['SRC']) || empty($file['WIDTH']) || empty($file['HEIGHT'])) {
            return array();
        }

        $result = array(
            'id'     => $file['ID'],
            'src'    => $file['SRC'],
            'width'  => $file['WIDTH'],
            'height' => $file['HEIGHT'],
        );

        return $result;
    }
}
