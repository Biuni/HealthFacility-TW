<?php

/**
* LoginController
* 
* This is the controller class
* of the login page.
*
* @author     Gianluca Bonifazi
* @category   controllers 
* @copyright  Univpm (c) 2018
*/

class LoginController extends Zend_Controller_Action
{
    protected $_authService;
    
    /**
    * Initialize action controller
    */
    public function init()
    {
        // Initialize the auth service
        $this->_authService = new Application_Service_Auth();
    }
    
    /**
    * indexAction
    *
    * @link       /login
    * @category   actions
    */
    public function indexAction()
    {
        // If the user is logged
        // redirect to the reserved area
        if ($this->_authService->getIdentity()) {
            return $this->_helper->redirector('index', $this->_authService->getIdentity()->role);
        }
    }
    
    /**
    * enterAction
    *
    * @link       /login/enter
    * @category   actions
    */
    public function enterAction()
    {
        // Read the POST request and assign
        // the username and password into two variables
        $username = $this->getRequest()->getPost('username', null);
        $password = $this->getRequest()->getPost('password', null);
        
        // Check the validity of the values
        if ($username == null || $password == null) {
            return $this->_redirect('login?err=1');
        }
        
        // Push username and password into an array
        // (The password is converted to hash)
        $credentials = array(
            'username' => htmlspecialchars($username),
            'password' => hash('SHA256', $password)
        );
        
        // If the result of authentication is FALSE
        // redirect the user to the login page with error
        if (false === $this->_authService->authenticate($credentials)) {
            return $this->_redirect('login?err=1');
        } else {
            // If the result of authentication is TRUE
            // Redirect the user to the reserved area
            return $this->_helper->redirector('index', $this->_authService->getIdentity()->role);
        }
        
    }
    
}

