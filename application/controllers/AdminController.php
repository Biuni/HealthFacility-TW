<?php

class AdminController extends Zend_Controller_Action
{

    public function init()
    {
		$this->_helper->layout->setLayout('admin');
        $this->_authService = new Application_Service_Auth();
    }

    public function indexAction()
    {
        // action body
    }

    public function logoutAction()
    {
		$this->_authService->clear();
        return $this->_redirect('login');
    }


}

