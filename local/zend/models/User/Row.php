<?

/**
 * Class Sibirix_Model_User_Row
 *
 * @property string fullName
 * @property array countries
 *
 * @property string ID
 * @property string EMAIL
 * @property string LOGIN
 * @property string PASSWORD
 *
 * @property string NAME
 * @property string LAST_NAME
 */
class Sibirix_Model_User_Row extends Sibirix_Model_Bitrix_Row {

    protected $_groupList = null;
    protected $_roleId = null;

    public function getFullName() {
        return trim(sprintf('%s %s', $this->NAME, $this->LAST_NAME));
    }

    /**
     * @return int
     */
    public function getType() {
        $this->_loadGroups();
        $userGroups = $this->_groupList;

        if (array_search(CLIENT_TYPE_ID, $userGroups) !== false) {
            return CLIENT_TYPE_ID;
        } elseif (array_search(DESIGNER_TYPE_ID, $userGroups) !== false) {
            return DESIGNER_TYPE_ID;
        } 
		elseif (array_search(PROVIDER_TYPE_ID, $userGroups) !== false) {
            return PROVIDER_TYPE_ID;
        }
		elseif (array_search(SELLER_TYPE_ID, $userGroups) !== false) {
            return SELLER_TYPE_ID;
        }
		else {
            return UNDEFINED_TYPE_ID;
        }
    }

    /**
     * @return array
     */
    public function getTextType() {
        $this->_loadGroups();
        $userGroups = $this->_groupList;

        if (array_search(DESIGNER_TYPE_ID, $userGroups) !== false) {
            return [
                "class"    => 'designer',
                "fullName" => 'дизайнер'
            ];
        } else if(array_search(CLIENT_TYPE_ID, $userGroups) !== false) {
            return [
                "class" => 'client',
                "fullName" => 'клиент'
            ];
        }
		else if(array_search(PROVIDER_TYPE_ID, $userGroups) !== false) {
            return [
                "class" => 'provider',
                "fullName" => 'поставщик'
            ];
        }
		else{
			return [
                "class" => 'client',
                "fullName" => 'клиент'
            ];
		}
    }

    public function inGroup($groupId) {
        $this->_loadGroups();
        return in_array($groupId, $this->_groupList);
    }

    protected function _loadGroups() {
        if (is_null($this->_groupList)) {
            $cUser = new CUser();
            $this->_groupList = $cUser->GetUserGroup($this->ID);
            $this->_groupList = array_map(function($item) {
                return intval($item);
            }, $this->_groupList);
        }
    }

    public function onSiteTime() {
        $date = new DateTime($this->DATE_REGISTER);
        $today = new DateTime("now");

        $interval = $today->diff($date);

        $timeString = '';

        if ($interval->y > 0) {
            $timeString = $interval->y . ' ' . EHelper::getWordForm($interval->y, ['год', 'года', 'лет']);
        }

        if ($interval->m > 0) {
            if ($interval->y > 0 && $interval->d == 0) {
                $timeString .= ' и ';
            } elseif ($interval->y > 0) {
                $timeString .= ' ';
            }
            $timeString .= $interval->m . ' ' . EHelper::getWordForm($interval->m, ['месяц', 'месяца', 'месяцев']);
        }

        if ($interval->d > 0) {
            if ($interval->y > 0 || $interval->m > 0) {
                $timeString .= ' и ';
            }
            $timeString .= $interval->d . ' ' . EHelper::getWordForm($interval->d, ['день', 'дня', 'дней']);
        }

        return $timeString;
    }
}
