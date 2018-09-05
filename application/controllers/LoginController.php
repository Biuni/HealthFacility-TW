<?php

class LoginController extends Zend_Controller_Action
{
	protected $_authService;

    public function init()
    {
        $this->_authService = new Application_Service_Auth();
    }

    public function indexAction()
    {
        // Redirect to user reserved area
        // if user are logged in
    	if ($this->_authService->getIdentity()) {
        	return $this->_helper->redirector('index', $this->_authService->getIdentity()->role);
    	}
    }

    public function enterAction()
    {
		
		$username = $this->getRequest()->getPost('username', null);
		$password = $this->getRequest()->getPost('password', null);

		if ($username == null || $password == null) {
        	return $this->_redirect('login?err=1');
		}
        
        $credentials = array(
            'username' => $username,
            'password' => hash('SHA256', $password)
        );

        if (false === $this->_authService->authenticate($credentials)) {
        	return $this->_redirect('login?err=1');
        } else {
        	// Redirect to user reserved area
        	return $this->_helper->redirector('index', $this->_authService->getIdentity()->role);
        }

    }

}

