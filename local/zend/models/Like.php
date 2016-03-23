<?

/**
 * Class Sibirix_Model_Like
 *
 */
class Sibirix_Model_Like {

    protected $userId;
    protected $hh;

    /**
     * @throws Zend_Exception
     */
    function __construct() {
        $this->userId = Sibirix_Model_User::getId();
        $this->hh = Highload::instance(HL_LIKES)->cache(0);

    }

    /**
     * @param $elementId
     * @return bool
     */
    public function add($elementId) {
        $items = $this->hh->where(['UF_USER_ID' => $this->userId, 'UF_DESIGN' => $elementId])->fetch();
        $item = reset($items);

        if (!$item) {
            $result = $this->hh->add([
                'UF_USER_ID' => $this->userId,
                'UF_DESIGN'  => $elementId
            ]);
        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * @param $elementId
     * @return bool
     */
    public function remove($elementId) {
        $items = $this->hh->where(['UF_USER_ID' => $this->userId, 'UF_DESIGN' => $elementId])->fetch();
        $item = reset($items);

        $this->hh = Highload::instance(HL_LIKES)->cache(0);
        if ($item) {
            $this->hh->remove($item['ID']);
        }

        return true;
    }
}