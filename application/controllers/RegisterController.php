<?php

/**
* RegisterController
* 
* This is the controller class
* of the register page.
*
* @author     Gianluca Bonifazi
* @category   controllers 
* @copyright  Univpm (c) 2018
*/

class RegisterController extends Zend_Controller_Action
{
    /**
    * Initialize action controller
    */
    public function init()
    {}
    
    /**
    * indexAction
    *
    * @link       /register
    * @category   actions
    */
    public function indexAction()
    {}
    
    /**
    * enterAction
    *
    * @link       /register/enter
    * @category   actions
    */
    public function enterAction()
    {
        // Read the POST request and assign
        // to the variables
		$username 	= $this->getRequest()->getPost('username', null);
		$password 	= $this->getRequest()->getPost('password', null);
		$email 		= $this->getRequest()->getPost('email', null);
		$name 		= $this->getRequest()->getPost('name', null);
		$surname 	= $this->getRequest()->getPost('surname', null);
		$code 		= $this->getRequest()->getPost('code', null);

        // Push the values into an array
		$values = array(
			'username' 	=> $username,
			'password' 	=> hash('SHA256', $password),
			'role'		=> 'user',
			'email' 	=> $email,
			'name' 		=> $name,
			'surname' 	=> $surname,
			'code' 		=> $code
		);

        // Check the validity of the values
        if ($values['username'] == ''   || $values['username']  == null ||
            $values['password'] == ''   || $values['password']  == null ||
            $values['email']    == ''   || $values['email']     == null ||
            $values['name']     == ''   || $values['name']      == null ||
            $values['surname']  == ''   || $values['surname']   == null ||
            $values['code']     == ''   || $values['code']      == null) {
            return $this->_redirect('register?err=1');
        }

        // Initialize the register model
        $register = new Application_Model_Register();
        // Try to create a new record
        // in the database with the new user
        try {
        	$register->create($values);
        	$error = false;
        } catch(Exception $e){
            // If username or email are already used
            // print an error.
        	$error = true;
        }

        if ($error) {
			return $this->_redirect('register?err=2');
        } else {
			return $this->_redirect('login?reg=1');
        }

    }

}

