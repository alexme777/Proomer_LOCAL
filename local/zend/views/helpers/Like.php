<?php

/**
 * Class Zend_View_Helper_Like
 */
class Zend_View_Helper_Like extends Zend_View_Helper_Abstract {

    public function Like() {
        return $this;
    }

    public function button($elementId, $count, $liked) {
        $activeStr = ($liked)?' active':'';
        $enableStr = (!Sibirix_Model_User::isAuthorized())?' disabled':'';
        if (empty($count)) {
            $count = 0;
        }
        return '<a href="javascript:void(0)" class="like js-like'. $activeStr . $enableStr.'" data-id="' . $elementId .'">
        <span class="heart'. ((Sibirix_Model_User::isAuthorized())?' js-tooltip':''). '" data-description="Мне нравится"></span><span class="js-value">' . $count . '</span></a>';
    }
}