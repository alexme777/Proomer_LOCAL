<?
/**
 * Class Sibirix_Model_User
 */
class Sibirix_Model_User extends Sibirix_Model_Bitrix {

    /**
     * @var CUser
     */
    protected $_bx_user;

    protected $_instanceClass = 'Sibirix_Model_User_Row';

    protected static $userLocation;

    /**
     * @var array
     */
    private $_login_errors;

    protected $_selectFields = [
        'ID',
        'ACTIVE',
        'LOGIN',
        'EMAIL',
        'PERSONAL_PHONE',
        'PERSONAL_PHOTO',
        'NAME',
        'LAST_NAME',
        'PERSONAL_PHOTO',
        'WORK_WWW',
        'WORK_PROFILE',
        'UF_STYLES',
        'DATE_REGISTER',
		'WORK_COMPANY',
		'PERSONAL_CITY',
		'PERSONAL_STREET'
    ];

    /**
     * @param null $initParams
     * @throws Zend_Exception
     */
    public function init($initParams = NULL) {
        $this->_bx_user = Zend_Registry::get('BX_USER');
    }

    public static function isAuthorized() {
        /* @var CUser $cuser */
        $user = Zend_Registry::get('BX_USER');
        return $user->IsAuthorized();
    }

    public static function isAdmin() {
        /* @var CUser $cuser */
        $user = Zend_Registry::get('BX_USER');
        return $user->IsAdmin();
    }
	
	/**
     * @param $filter
     * @param $sort
     * @param $page
     * @return object
     */
    public function getUserList($filter, $sort, $page, $profile=false, $limit) {
        $planList = $this->select($this->_selectFields, true)->where($filter)->orderBy($sort, true)->getPageItem($page, $limit);
      //  $this->_getDesignInfo($planList->items);

        return $planList;
    }

    /**
     * @param bool $force
     *
     * @return UserSelect
     */
    protected function _getQueryInstance($force = false) {
        if ($force || !$this->_ibs) {
            $this->_ibs = UserSelect::instance();
            $this->_ibs->select($this->_selectFields);
        }

        return $this->_ibs;
    }

    /**
     * @return mixed
     * @throws Zend_Exception
     */
    public static function getId() {
        $bxUser = Zend_Registry::get('BX_USER');
        return (int)$bxUser->GetID();
    }

    /**
     * @return Sibirix_Model_User_Row
     */
    public static function getCurrent() {
        static $user;
        if ($user) {
            return $user;
        }

        $self = new static();
        $userId = static::getId();
        $user = $self->getElement($userId);
	
        return $user;
    }

    /**
     * @param $login
     * @param $password
     * @return bool|array
     */
    public function login($login, $password) {
        $errors = $this->_bx_user->Login($login, $password);
        if (!is_array($errors)) {
            return true;
        }

        $filter = new Zend_Filter_StripTags();
        return array($errors['ERROR_TYPE'] => $filter->filter($errors['MESSAGE']));
    }

    /**
     * @param $email
     * @param $password
     * @return bool|array
     */
    public function loginByEmail($email, $password) {
        if (!check_email($email)) {
            return $this->login($email, $password);
        }

        $filter = array(
            "ACTIVE" => "Y",
            "=EMAIL"  => $email
        );

        $fields = array("LOGIN");

        $rsUsers = $this->_bx_user->GetList($by, $order, $filter, array("FIELDS" => $fields));
        if ($arUser = $rsUsers->Fetch()) {
            $login = $arUser['LOGIN'];
            if ($this->login($login, $password) === true) {
                return true;
            }
        }

        return $this->login($email, $password);
    }

    /**
     * @return array
     */
    public function getLoginErrors() {
        return $this->_login_errors;
    }

    /**
     * @param $id
     * @return array
     */
    public function findById($id) {
        $rsUser = $this->_bx_user->GetByID($id);
        return $rsUser->Fetch();
    }

    /**
     * @param $email
     * @return array|bool
     */
    public function findByEmail($email) {
        $filter = array(
            "ACTIVE" => "Y",
            "EMAIL"  => $email
        );

        $rsUsers = $this->_bx_user->GetList($by, $order, $filter);
        if ($arUser = $rsUsers->Fetch()) {
            return $arUser;
        }

        return false;
    }

    /**
     * @param $login
     * @param $checkword
     * @param $password
     * @return array|bool
     */
    public function changePassword($login, $checkword, $password) {
        $result = $this->_bx_user->ChangePassword($login, $checkword, $password, $password);
        $user = $this->findByEmail($login);
        $this->_bx_user->Authorize($user['ID'], true);
        if ($result['TYPE'] === 'OK') {
            return true;
        }

        return array($result['FIELD'] => $result['MESSAGE']);
    }

    /**
     * @param $email
     * @return bool
     */
    public function remindPassword($email) {
        $user = $this->findByEmail($email);
        if ($user) {
            $result = $this->_bx_user->SendPassword($user['LOGIN'], $email);
            if ($result['TYPE'] === 'OK') {
                return true;
            }
        } else {
            $result['FIELD'] = 'email';
            $result['MESSAGE'] = 'Введенный e-mail не зарегистрирован на сайте';
        }


        return array($result['FIELD'] => $result['MESSAGE']);
    }

    /**
     * Обновление личных данных дизайнера
     * @param $user
     * @param $name
     * @param $lastName
     * @param $phone
     * @param $email
     * @param $portfolio
     * @param $about
     * @param $styles
     * @return bool
     */
    public function updateDesignerProfile($user, $name, $lastName, $phone, $email, $portfolio, $about, $styles, $kompany, $address, $city) {
        $login = $email;
        $userModel = new Sibirix_Model_User();
        $result = [];
        if ($res = $userModel->findByEmail($email)) {
            if ($res['ID'] != Sibirix_Model_User::getId()) {
                $result['email'] = 'Введенный e-mail уже используется';

                return $result;
            }
        }

        //для админа
        if ($login == 'admin@sibirix.ru') {
            $login = 'admin';
        }

        if (empty($styles)) {
            $styles = [];
        }

        $fields = [
            'NAME' => $name,
            'LAST_NAME' => $lastName,
            'EMAIL' => $email,
            'LOGIN' => $login,
            'PERSONAL_PHONE' => $phone,
            'WORK_WWW' => $portfolio,
            'WORK_PROFILE' => $about,
            'UF_STYLES' => $styles,
			'KOMPANY' => $kompany,
			'PERSONAL_STREET' => $address,
			'WORK_COMPANY' => $kompany,
			'PERSONAL_CITY' => $city, 
        ];

        $result = $this->_bx_user->Update($user->ID, $fields);

        return $result;
    }

    /**
     * Обновление личных данных клиента
     * @param $user
     * @param $name
     * @param $lastName
     * @param $phone
     * @param $email
     * @return bool
     */
    public function updateClientProfile($user, $name, $lastName, $phone, $email) {
        $userModel = new Sibirix_Model_User();
        $result = [];
        if ($res = $userModel->findByEmail($email)) {
            if ($res['ID'] != Sibirix_Model_User::getId()) {
                $result['email'] = 'Введенный e-mail уже используется';

                return $result;
            }
        }

        $fields = [
            'NAME' => $name,
            'LAST_NAME' => $lastName,
            'EMAIL' => $email,
            'LOGIN' => $email,
            'PERSONAL_PHONE' => $phone
        ];

        $result = $this->_bx_user->Update($user->ID, $fields);

        return $result;
    }
	
	    public function updateProviderProfile($user, $name, $lastName, $phone, $email) {
        $userModel = new Sibirix_Model_User();
        $result = [];
        if ($res = $userModel->findByEmail($email)) {
            if ($res['ID'] != Sibirix_Model_User::getId()) {
                $result['email'] = 'Введенный e-mail уже используется';

                return $result;
            }
        }

        $fields = [
            'NAME' => $name,
            'LAST_NAME' => $lastName,
            'EMAIL' => $email,
            'LOGIN' => $email,
            'PERSONAL_PHONE' => $phone
        ];

        $result = $this->_bx_user->Update($user->ID, $fields);

        return $result;
    }

    /**
     * Обновление типа пользователя
     * @param $user
     * @param $type
     * @return bool
     */
    public function updateType($user, $type) {
        $fields = [
            'GROUP_ID' => [$type]
        ];

        $result = $this->_bx_user->Update($user->ID, $fields);
        return $result;
    }

    /**
     * Изменить аватарку
     * @param $user
     * @param $fileInfo
     * @return bool
     */
    public function updatePhoto($user, $fileInfo) {
        $result = $this->_bx_user->Update($user->ID, ['PERSONAL_PHOTO' => $fileInfo]);

        return $result;
    }

    /**
     * Проверка корректности пароля по id пользователя
     * @param $id
     * @param $password
     * @return bool
     */
    public function isUserPassword($id, $password) {
        $user = $this->findById($id);
        $salt = substr($user['PASSWORD'], 0, (strlen($user['PASSWORD']) - 32));
        $realPassword = substr($user['PASSWORD'], -32);
        $password = md5($salt.$password);
        return ($password == $realPassword);
    }

    public function checkUser($email, $password) {
        $user = $this->findByEmail($email);
        if($user) {
            if ($this->isUserPassword($user['ID'], $password)) {
                return $user['ID'];
            }
        }

        return false;
    }

    /**
     * Получает ID города из cookie или по IP и записывает в cookie
     * @return bool
     * @throws Zend_Exception
     */
    public static function getUserLocation() {
        if (static::$userLocation) return static::$userLocation;

        $application = Zend_Registry::get("BX_APPLICATION");
        $cityId      = $application->get_cookie("USER_LOCATION");

        if ($cityId) {
            static::$userLocation = $cityId;
            return static::$userLocation;
        }

        $geoIpModel = new Sibirix_Model_GeoIp();
        $userCityId = $geoIpModel->getClientBitrixLocation();

        if (!$userCityId) {
            $cityModel  = new Sibirix_Model_City();
            $userCityId = $cityModel->getDefaultCity();
        }

        $application->set_cookie("USER_LOCATION", $userCityId);
        static::$userLocation = $userCityId;
        return static::$userLocation;
    }

    /**
     * Перезаписывает ID города пользователя вручную в cookie
     * @param $cityId
     * @return bool
     * @throws Zend_Exception
     */
    public static function setUserLocation($cityId) {
        $cityModel = new Sibirix_Model_City();
        $city      = $cityModel->getElement($cityId);

        if (!empty($city)) {
            Zend_Registry::get("BX_APPLICATION")->set_cookie("USER_LOCATION", $cityId);
            static::$userLocation = $cityId;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Регистрация пользователя
     * Если пользователь с данным email существует, возвращает false
     * @param $name
     * @param $email
     * @param $password
     * @param $type
     * @return bool
     */
    public function register($name, $email, $password, $type) {
        $checkEmail = $this->findByEmail($email);

        if ($checkEmail) {
            return false;
        }

        $fields = [
            "NAME" => $name,
            "EMAIL" => $email,
            "LOGIN" => $email,
            "ACTIVE" => "Y",
            "PASSWORD" => $password,
            "CONFIRM_PASSWORD" => $password,
            "GROUP_ID" => [$type]
        ];

        $newId = $this->_bx_user->Add($fields);
        if (intval($newId) > 0) {
            $this->_bx_user->Update($newId, ["UF_NOTIF_STATUS" => 1]);
            $this->_bx_user->Authorize($newId, true);
            $modelNotification = new Sibirix_Model_Notification();

            $modelNotification->registrationSuccess($newId, $name, $email, $type);

            return true;
        } else {
            return false;
        }
    }

    /**
     * Изменение пароля из профиля
     * @param $user
     * @param $password
     * @param $newPassword
     * @param $newPasswordConfirm
     * @return array|bool
     */
    public function changeProfilePassword($user, $password, $newPassword, $newPasswordConfirm) {

        if (!empty($password)) {
            if ($this->isUserPassword($user->ID, $password)) {
                if (strlen($newPassword) < 6) {
                    return ['newPassword' => 'Задайте пароль не менее 6 символов'];
                }
                $fields['PASSWORD'] = $newPassword;
                $fields['CONFIRM_PASSWORD'] = $newPasswordConfirm;
            } else {
                return ['password' => 'Неверный пароль'];
            }
        }

        $result = $this->_bx_user->Update($user->ID, $fields);
        return $result;
    }
}
