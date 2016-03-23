<?php

/**
 * Class Zend_View_Helper_Like
 */
class Zend_View_Helper_Favourite extends Zend_View_Helper_Abstract {

    public function Favourite() {
        return $this;
    }

    public function button($elementId, $favourite) {
        $activeStr = ($favourite)?' active':'';
        $enableStr = (!Sibirix_Model_User::isAuthorized())?' disabled':'';
		$favouriteClass = ($favourite)?'my-favourite favourite js-add-favourite js-tooltip mainTooltip':'to-favourite favourite js-add-favourite js-tooltip mainTooltip ';
		return '<a href="javascript:void(0)" class="'.$favouriteClass.$enableStr.'" data-description="Добавить в избранное" data-element-id="'.$elementId .'"></a>';
	}
}


   