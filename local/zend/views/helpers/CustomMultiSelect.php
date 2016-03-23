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


/**
 * Helper to generate a set of radio button elements
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_View_Helper_CustomMultiSelect extends Zend_View_Helper_FormSelect
{
    /**
     * Input type to use
     * @var string
     */
    protected $_inputType = 'select';

    /**
     * Whether or not this element represents an array collection by default
     * @var bool
     */
    protected $_isArray = false;

    /**
     * Generates a set of radio button elements.
     *
     * @access public
     *
     * @param string|array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are extracted in place of added parameters.
     *
     * @param mixed $value The radio value to mark as 'checked'.
     *
     * @param array $options An array of key-value pairs where the array
     * key is the radio value, and the array value is the radio text.
     *
     * @param array|string $attribs Attributes added to each radio.
     *
     * @return string The radio buttons XHTML.
     */
    public function customMultiSelect($name, $value = null, $attribs = null, $options = null, $listsep = "<br />\n")
    {
        $info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
        extract($info); // name, value, attribs, options, listsep, disable

        $placeholder = '';
        $label = '';
        $wrapClass1 = '';
        $wrapClass2 = '';
        $wrapClass3 = '';

        // retrieve attributes for labels (prefixed with 'label_' or 'label')
        $label_attribs = array();
        foreach ($attribs as $key => $val) {
            if ($key == 'placeholder') {
                $placeholder = $val;
            }
            if ($key == 'label') {
                $label = $val;
            }
            if ($key == 'wrapClass1') {
                $wrapClass1 = $val;
            }
            if ($key == 'wrapClass2') {
                $wrapClass2 = $val;
            }
            if ($key == 'wrapClass3') {
                $wrapClass3 = $val;
            }
            if ($key == 'help') {
                $help = $val;
            }

            if ($key == 'placeholder' || $key == 'wrapClass1' || $key == 'wrapClass2' || $key == 'wrapClass3' || $key == 'label' || $key == 'help') {
                unset($attribs[$key]);
            }

            $tmp    = false;
            $keyLen = strlen($key);
            if ((6 < $keyLen) && (substr($key, 0, 6) == 'label_')) {
                $tmp = substr($key, 6);
            } elseif ((5 < $keyLen) && (substr($key, 0, 5) == 'label')) {
                $tmp = substr($key, 5);
            }

            if ($tmp) {
                // make sure first char is lowercase
                $tmp[0] = strtolower($tmp[0]);
                $label_attribs[$tmp] = $val;
                unset($attribs[$key]);
            }
        }

        $labelPlacement = 'append';
        foreach ($label_attribs as $key => $val) {
            switch (strtolower($key)) {
                case 'placement':
                    unset($label_attribs[$key]);
                    $val = strtolower($val);
                    if (in_array($val, array('prepend', 'append'))) {
                        $labelPlacement = $val;
                    }
                    break;
            }
        }

        $options = (array) $options;
        $element = "";

        if (!empty($wrapClass1)) {
            $element .= '<div class="'.$wrapClass1.'">';
        }

        if (!empty($wrapClass2)) {
            $element .= '<div class="'.$wrapClass2.'">';
        }

        $element .= '<label for="' . $name . '"'. $this->_htmlAttribs($label_attribs) . '>'. $label. '</label>';
        if (!empty($help)) {
            $element .= '<span class="help js-tooltip" data-description="'.$help.'"></span>';
        }

        $element .= '<div class="js-select-multi select-multi">'.
            '<select class="js-select-multiple" '.$this->_htmlAttribs($attribs).' name="'.$name.'">';

        foreach ($options as $ind => $option) {
            $element .= '<option value="' . $ind . '"' . ((!empty($value) && in_array($ind, $value)) ? ' selected' : '') . '>' . $option . '</option>';
        }

        $valueStr = (!empty($value)) ? implode(",", $value) : '';

        $element .= '</select><input type="hidden" id='.$name.' class="js-selected-values" value="'.$valueStr.'"><div class="placeholder">'.$placeholder.'</div></div>';

        if (!empty($wrapClass1)) {
            $element .= '</div>';
        }

        if (!empty($wrapClass2)) {
            $element .= '</div>';
        }

        $element .= '<div class="'.$wrapClass3.'"><div class="label"><div class="select2-multi-results js-selected-html"></div></div></div>';

        return $element;
    }
}
