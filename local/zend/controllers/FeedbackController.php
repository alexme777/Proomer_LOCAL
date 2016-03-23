<?

/**
 * Class FeedbackController
 */
class FeedbackController extends Sibirix_Controller {

    /**
     * @var Sibirix_Model_Feedback
     */
    protected $_model;

    public function init() {
        $this->_model  = new Sibirix_Model_Feedback();
    }

    public function addAction() {
        $form = new Sibirix_Form_Feedback();
        $formData = $this->getAllParams();

        if ($form->isValid($formData)) {
            $validData = $form->getValues();

            if (!$form->antiBotCheck()) {
                $this->_response->stopBitrix(true);
                $this->_helper->viewRenderer->setNoRender();
                return false;
            }

            $addResult = $this->_model->add($validData);

            if ($addResult) {
                $notification = new Sibirix_Model_Notification();
                $emailTo = Settings::getOption("FEEDBACK_EMAIL_TO");
                $titleMail = "Новое сообщение";

                $notification->sendFeedback($addResult, $validData, $emailTo, $titleMail);
            } else {
                $form->setFieldErrors("name", "Ошибка добавления");
            }

        } else {
            $form->getFieldsErrors();
        }

        $this->_helper->json(['success' => !$form->issetError(), 'errorFields' => $form->formErrors]);
    }

}