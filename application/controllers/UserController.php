<?php

class UserController extends Zend_Controller_Action
{

    public function init()
    {
		$this->_authService = new Application_Service_Auth();
    }

    public function indexAction()
    {
        
    }

    public function logoutAction()
    {
		$this->_authService->clear();
        return $this->_redirect('login');
    }

}

