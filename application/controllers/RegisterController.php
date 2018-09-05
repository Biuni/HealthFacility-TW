<?php

class RegisterController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function enterAction()
    {
		$username 	= $this->getRequest()->getPost('username', null);
		$password 	= $this->getRequest()->getPost('password', null);
		$email 		= $this->getRequest()->getPost('email', null);
		$name 		= $this->getRequest()->getPost('name', null);
		$surname 	= $this->getRequest()->getPost('surname', null);
		$code 		= $this->getRequest()->getPost('code', null);

		$values = array(
			'username' 	=> $username,
			'password' 	=> hash('SHA256', $password),
			'role'		=> 'user',
			'email' 	=> $email,
			'name' 		=> $name,
			'surname' 	=> $surname,
			'code' 		=> $code
		);

        $register = new Application_Model_Register();
        try {
        	$register->create($values);
        	$error = false;
        } catch(Exception $e){
        	$error = true;
        }

        if ($error) {
			return $this->_redirect('register?err=1');
        } else {
			return $this->_redirect('login?reg=1');
        }

    }

}

