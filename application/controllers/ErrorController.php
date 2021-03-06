<?php

/**
 * ErrorController
 * 
 * This is the controller class
 * of the error page.
 *
 * @author     Gianluca Bonifazi
 * @category   controllers 
 * @copyright  Univpm (c) 2018
 */

class ErrorController extends Zend_Controller_Action
{
    
    /**
    * errorAction
    *
    * @category   actions
    */
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        
        if (!$errors || !$errors instanceof ArrayObject) {
            $this->view->message = 'You have reached the error page';
            return;
        }
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error - controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $priority = Zend_Log::NOTICE;
                $this->view->message = 'Page not found';
                break;
            default:
                // 500 error - application error
                $this->getResponse()->setHttpResponseCode(500);
                $priority = Zend_Log::CRIT;
                $this->view->message = 'Application error';
                break;
        }

        // Print log exception, if logger available
        if ($log = $this->getLog()) {
            switch ($priority) {
                case 5:
                    $log->notice('Page not found => ' . $errors->exception->getMessage());
                    break;
                case 2:
                    $log->crit('Application error => ' . $errors->exception->getMessage());
                    break;
                default:
                    $log->warn('General error => ' . $errors->exception->getMessage());
                    break;
            }
        }
        
        // Conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }
        
        $this->view->request = $errors->request;
    }

    /**
    * getLog
    *
    * @category   utilities
    */    
    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->_logger) {
            return false;
        }
        $log = $bootstrap->_logger;
        return $log;
    }
    
    
}

