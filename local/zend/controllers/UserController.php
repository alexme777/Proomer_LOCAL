<?
/**
 * Class UserController
 */
class UserController extends Sibirix_Controller {

    /**
     * @var Sibirix_Model_User
     */
    protected $_model;

    public function init() {
        $this->_model  = new Sibirix_Model_User();
    }

    public function passwordRemindAction() {
        $form = new Sibirix_Form_Remind();

        if ($form->isValid($this->getAllParams())) {

            $result = $this->_model->remindPassword($form->getValue('email'));
            if (true === $result) {
                $this->_helper->json(['success' => true]);
            } else {
                $form->getElement('email')->addErrors($result);
                $form->markAsError();
                $form->getFieldsErrors();
            }
        } else {
            $form->getFieldsErrors();
        }

        $this->_helper->json(['success' => false, 'errorFields' => $form->formErrors]);
    }

    /**
     * форма восстановления пароля
     */
    public function passwordChangeAction() {
        $form = new Sibirix_Form_ChangePassword();

        if ($form->isValid($this->getAllParams())) {

            $result = $this->_model->changePassword($form->getValue('login'),$form->getValue('checkword'),$form->getValue('password'));
            if (true === $result) {
                $this->_helper->json(['success' => true]);
            } else {
                $form->getElement('password')->addErrors($result);
                $form->markAsError();
                $form->getFieldsErrors();
            }
        } else {
            $form->getFieldsErrors();
        }

        $this->_helper->json(['success' => false, 'errorFields' => $form->formErrors]);
    }

    /**
     * Меняем местоположение пользователя
     */
    public function changeLocationAction() {
        $newLocation = $this->getParam("userLocation");
        $changeResult = Sibirix_Model_User::setUserLocation($newLocation);
        $this->_helper->json(['success' => $changeResult]);
    }

    public function registrationAction() {
        $form = new Sibirix_Form_Registration();

        if ($form->isValid($this->getAllParams())) {

            $result = $this->_model->register(
                $form->getValue('name'),
                $form->getValue('email'),
                $form->getValue('password'),
                $form->getValue('type'));

            if ($result) {
                $this->_helper->json(['success' => true]);
            } else {
                $form->getElement('email')->addError('Пользователь с данным e-mail уже зарегистрирован.');
                $form->markAsError();
                $form->getFieldsErrors();
            }
        } else {
            $form->getFieldsErrors();
        }

        $this->_helper->json(['success' => false, 'errorFields' => $form->formErrors]);
    }

    public function authAction() {
        $form = new Sibirix_Form_Auth();

        if ($form->isValid($this->getAllParams())) {
            $result = $this->_model->loginByEmail($form->getValue('email'), $form->getValue('password'));
            if (true === $result) {
                $this->_helper->json(['success' => true]);
            } else {
                $form->getElement('email')->addErrors($result);
                $form->markAsError();
                $form->getFieldsErrors();
            }
        } else {
            $form->getFieldsErrors();
        }

        $this->_helper->json(['success' => false, 'errorFields' => $form->formErrors]);
    }
}
