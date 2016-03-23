<?
/**
 * Class Sibirix_Model_Promo
 */
class Sibirix_Model_Promo extends Sibirix_Model_Bitrix {

    protected $_iblockId = IB_PROMO;
    protected $_selectFields = array(
        'ID', 'NAME', 'PREVIEW_PICTURE', 'PREVIEW_TEXT', 'PROPERTY_LINK'
    );

    public function getItems() {
        $promo = $this
            ->limit(4)
            ->getElements();

        $this->getImageData($promo);

        return $promo;
    }
}
