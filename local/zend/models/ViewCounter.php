<?

/**
 * Class  extends Sibirix_Model_Bitrix
 */
class Sibirix_Model_ViewCounter {

    protected static $viewCounter;
    protected static $profileViewCounter;

    public static function getViewCounter($profile = false) {
        $cookieName = ($profile)?'PROFILE_VIEW_COUNTER':'VIEW_COUNTER';
        $staticName = ($profile)?'profileViewCounter':'viewCounter';

        $viewCounterList = self::getCounterList($profile);

        if (static::${$staticName} && in_array(static::${$staticName}, $viewCounterList)) return static::${$staticName};

        $application = Zend_Registry::get("BX_APPLICATION");
        $viewCounterValue = $application->get_cookie($cookieName);

        if ($viewCounterValue  && in_array($viewCounterValue, $viewCounterList)) {
            static::${$staticName} = $viewCounterValue;
            return static::${$staticName};
        }

        $application->set_cookie($cookieName, $viewCounterList[0]);
        static::${$staticName} = $viewCounterList[0];
        return static::${$staticName};
    }

    public static function setViewCounter($viewCounterValue) {
        Zend_Registry::get("BX_APPLICATION")->set_cookie("VIEW_COUNTER", $viewCounterValue);
        static::$viewCounter = $viewCounterValue;
    }

    public static function setProfileViewCounter($viewCounterValue) {
        Zend_Registry::get("BX_APPLICATION")->set_cookie("PROFILE_VIEW_COUNTER", $viewCounterValue);
        static::$profileViewCounter = $viewCounterValue;
    }

    public static function getCounterList($profile=false) {
        $elementCntString = ($profile)?Settings::getOption("ELEMENT_PROFILE_CNT"):Settings::getOption("ELEMENT_CNT");

        if (empty($elementCntString)) {
            $elementCntArray = ($profile)?array(15, 30, 60):array(20, 40, 60);
        } else {
            $elementCntArray = explode(',', $elementCntString);

            foreach ($elementCntArray as $key => $elementCntItem) {
                $elementCntItem = (int)trim($elementCntItem);
                if (empty($elementCntItem)) {
                    unset($elementCntArray[$key]);
                } else {
                    $elementCntArray[$key] = $elementCntItem;
                }
            }

            sort($elementCntArray);
            $elementCntArray = array_values($elementCntArray);
        }

        return $elementCntArray;
    }

}
