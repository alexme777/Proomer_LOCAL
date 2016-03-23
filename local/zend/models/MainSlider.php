<?
/**
 * Class Sibirix_Model_MainSlider
 */
class Sibirix_Model_MainSlider extends Sibirix_Model_Bitrix {

    protected $_iblockId = IB_MAIN_SLIDER;
    protected $_selectFields = array(
        'ID', 'NAME', 'CODE', 'DETAIL_PICTURE', 'PREVIEW_TEXT', 'PROPERTY_DESIGN'
    );

    public function getItems() {
        $limit = Settings::getOption('maxSlidesCount', MAX_SLIDES_COUNT);
	
        $slides['ITEMS'] = $this
            ->limit($limit)
            ->getElements();
		
        $this->getImageData($slides['ITEMS'], 'DETAIL_PICTURE');
	
        $designs = [];
        foreach ($slides['ITEMS'] as $slide) {
            $designs[] = $slide->PROPERTY_DESIGN_VALUE;
        }

        $designItems = (new Sibirix_Model_Design())
            ->where(['ID' => $designs])
            ->getElements();

        foreach ($designItems as $item) {
            $slides['DESIGN'][$item->ID] = $item;
        }

        return $slides;
    }
}
