<?
require_once 'Zend/View/Helper/FormElement.php';

class Zend_View_Helper_RangeSlider extends Zend_View_Helper_FormElement
{

    public function rangeSlider($name, $value = null, $attribs = null)
    {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable

        $form = 0;
        $to = 100000;
        if (!empty($attribs["from"])) {
            $form = $attribs["from"];
            unset($attribs["from"]);
        }

        if (!empty($attribs["to"])) {
            $to = $attribs["to"];
            unset($attribs["to"]);
        }

        // build the element
        $disabled = '';
        if ($disable) {
            // disabled
            $disabled = ' disabled="disabled"';
        }
        $value = explode(":", $value);

        $xhtml = '';
        $xhtml .= '<div class="range-wrapper">';
        $xhtml .= '<input type="text"'
                . ' name="' . $this->view->escape($name) . 'Min"'
                . ' id="' . $this->view->escape($id) . 'Min"'
                . ' value="' . $this->view->escape($value[0]) . '"'
                . ' class="js-min-value"'
                . $disabled
                . $this->getClosingBracket();
        $xhtml .= "&nbsp;-&nbsp;";
        $xhtml .= '<input type="text"'
            . ' name="' . $this->view->escape($name) . 'Max"'
            . ' id="' . $this->view->escape($id) . 'Max"'
            . ' value="' . $this->view->escape($value[1]) . '"'
            . ' class="js-max-value"'
            . $disabled
            . $this->getClosingBracket();
        $xhtml .= '</div>';
        $xhtml .= '<div id="' . $this->view->escape($id) . 'Slider" class="js-slider js-slider-price" data-min="'.$form.'" data-max="'.$to.'"></div>';
        $xhtml .= '<div class="min-value">'.$form.'</div><div class="max-value">'.$to.'</div>';
        return $xhtml;
    }
}