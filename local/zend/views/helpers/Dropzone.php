<?
require_once 'Zend/View/Helper/FormElement.php';

class Zend_View_Helper_Dropzone extends Zend_View_Helper_FormElement
{

    public function dropzone($name, $value = null, $attribs = null) {
        if (!isset($attribs['typeTitle'])) {
            $attribs['typeTitle'] = 'изображения';
        }

        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable

        $xhtml = '';
        $xhtml .= '<div class="upload-area ' . $attribs["classZone"] . ' ' . (!empty($value) ? "dz-success" : "") . '" ' . (!empty($attribs["elementId"]) ? "data-element-id=" . $attribs["elementId"] : "") . '>';
        $xhtml .= '<div class="js-dz-text text">Перетащите ' . $attribs['typeTitle'] .' в эту область для их загрузки или нажмите <a href="javascript:void(0);" class="upload-link">загрузить</a></div>';

        //Value
        if (!is_array($value)) {
            $valueList = [$this->view->escape($value)];
        } else {
            $valueList = $value;
        }
        $valueList = array_filter($valueList);
        if (!empty($valueList)) {

            if ($attribs["files"]) {
                foreach ($valueList as $valueId => $valueItem) {
                    $xhtml .= '<div class="upload-doc ' . $valueItem["fileType"] . '" data-value-id="' . $valueId . '">';
                    $xhtml .= '<span class="doc-title">'.$valueItem["fileName"].'</span>';
                    $xhtml .= '<span class="doc-size">'.$valueItem["fileSize"].'</span>';
                    $xhtml .= '<a class="js-file-delete delete " href="javascript:void(0);"></a>';
                    $xhtml .= '</div>';

                }
            } else {
                foreach ($valueList as $valueId => $valueItem) {
                    $xhtml .= '<div class="dz-default-img" rel="group" style="background-image: url(\'' . $valueItem . '\')" data-value-id="' . $valueId . '"><div class="js-del-item del-icon"></div><div class="js-pin-item pin-icon"></div></div>';
					//$xhtml .= '<a class="dz-default-img gallery" rel="group" title="это фото 1" href="/upload/iblock/cfd/cfd75f0329c8b8dc965085773085dace.jpg"><img src="/upload/iblock/cfd/cfd75f0329c8b8dc965085773085dace.jpg"></a>';
				}
            }
        }

        $xhtml .= '</div>';
        $xhtml .= '<span class="js-error error-message"></span>';
        return $xhtml;
    }
}