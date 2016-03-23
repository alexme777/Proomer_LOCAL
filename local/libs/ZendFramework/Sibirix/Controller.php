<?
/**
 * Class Sibirix_Controller
 * Общий предок для всех наших контроллеров
 *
 * @property Zend_Controller_Request_Http _request
 */
class Sibirix_Controller extends Zend_Controller_Action {

    /**
     * @param $element
     */
    protected function _setSeoElementParams($element) {
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('description', $element->SEO['ELEMENT_META_DESCRIPTION']);
        Zend_Registry::get('BX_APPLICATION')->SetPageProperty('keywords', $element->SEO['ELEMENT_META_KEYWORDS']);
        if (trim($element->SEO['ELEMENT_META_TITLE'])) {
            Zend_Registry::get('BX_APPLICATION')->SetPageProperty('title', $element->SEO['ELEMENT_META_TITLE']);
        } else {
            Zend_Registry::get('BX_APPLICATION')->SetPageProperty('title', $element->NAME);
        }

        if (trim($element->SEO['ELEMENT_PAGE_TITLE'])) {
            Zend_Registry::get('BX_APPLICATION')->SetTitle($element->SEO['ELEMENT_PAGE_TITLE']);
        } else {
            Zend_Registry::get('BX_APPLICATION')->SetTitle($element->NAME);
        }

    }
}
