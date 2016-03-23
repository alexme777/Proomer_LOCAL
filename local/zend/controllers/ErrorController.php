<?
/**
 * Class ErrorController
 */
class ErrorController extends Zend_Controller_Action {

    /**
     *
     */
    public function errorAction() {
        $errors = $this->getParam('error_handler');

        if (!$errors || !$errors instanceof ArrayObject) {
            $this->view->message = 'Произошла непредвиденная ошибка';
            return;
        }

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                // Return control to Bitrix
                $this->_helper->viewRenderer->setNoRender(true);
                return;

            default:
                if ($errors->exception->getCode() == 404) {
                    $this->_response->setBitrix404();
                } else {
                    // application error
                    $this->getResponse()->setHttpResponseCode(500);
                    $priority = Zend_Log::CRIT;
                    $this->view->message = 'Программный сбой';
                }
                break;
        }

        // Log exception, if logger available
        $log = $this->getLog();
        if ($log) {
            $log->log($this->view->message, $priority, $errors->exception);
            $log->log('Параметры запроса', $priority, $errors->request->getParams());
        }

        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }

        $this->view->request = $errors->request;
    }

    /**
     * @return bool
     */
    public function getLog() {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }
}

