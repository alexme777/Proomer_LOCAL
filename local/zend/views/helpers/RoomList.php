<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: FormRadio.php 24865 2012-06-02 01:02:32Z adamlundrigan $
 */


/**
 * Abstract class for extension
 */
require_once 'Zend/View/Helper/FormElement.php';

class Zend_View_Helper_RoomList extends Zend_View_Helper_FormElement {

    public function roomList($name, $value = null) {
        $info = $this->_getInfo($name, $value);
        extract($info); // name, value, attribs, options, listsep, disable

        $html = '';
        if (!empty($value)) {
            foreach ($value as $room) {
                $html .= '<div class="room" data-room-id="' . $room["ID"] . '" title="' . $room["NAME"] . '">';
                $html .= '<span>' . $room["NAME"] . ' — ' . $room["AREA"] . ' М<sup>2</sup></span>';
                $html .= '<a href="javascript:void(0);" class="delete js-delete" data-has-img="' . $room["HAS_IMG"] . '"></a>';
                $html .= '</div>';
            }
        }

        return $html;
    }
}
