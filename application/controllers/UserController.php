<?php

class UserController extends Zend_Controller_Action
{

    public function init()
    {
		$this->_authService = new Application_Service_Auth();
    }

    public function indexAction()
    {
    	// If the user is not logged in
    	// redirect to the login page
        if(!$this->_authService->getIdentity()){
        	return $this->_redirect('login');
        }
    }

    public function logoutAction()
    {
		$this->_authService->clear();
        return $this->_redirect('login');
    }

}

