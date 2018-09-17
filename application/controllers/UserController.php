<?php

/**
* UserController
* 
* This is the controller class
* of the user's reserved area.
*
* @author     Gianluca Bonifazi
* @category   controllers 
* @copyright  Univpm (c) 2018
*/

class UserController extends Zend_Controller_Action
{
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
    * @link       /user
    * @category   actions
    */
    public function indexAction()
    {
        // Initialize the staff model
        $user = new Application_Model_User();

        // Get the userId
        $userId = $this->_authService->getIdentity()->user_id;
        
        // Get all future reservations
        $allReservations = $this->extractResult($user->extractBooking($userId, '>'));
        // Get all past reservations
        $pastReservations = $this->extractResult($user->extractBooking($userId, '<'));
        
        // Aassign the result of query to variables
        // who will used to print the reservation's table
        $this->view->assign('reservations', $allReservations);
        $this->view->assign('pastReservations', $pastReservations);
    }
    
    /**
    * deleteAction
    *
    * @package    AJAX
    * (Delete a reservation)
    *
    * @link       /user/delete
    * @category   actions
    */
    public function deleteAction()
    {
        // Disable the layout and
        // the view's rendering
        $this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        // Initialize the booking model
        $booking = new Application_Model_Booking();
        // Create and set the result variable to 0
        // (if the action goes well it will update to 1)
        $result = 0;
        
        // Get the POST request
        $request = $this->getRequest();

        // Read all values into the POST request
        $bookingId = $request->getPost('bookingId', null);
        
        // Delete the record in the database
        if ($booking->delete($bookingId)) {
            $result = 1;
            $message = 'Prenotazione annullata con successo!';
        } else {
            $message = 'Prenotazione NON annullata! Riprova.';
        }
        
        // Check if the request come
        // from an AJAX function
        if ($request->isXmlHttpRequest()) {
            $this->getResponse()->setHeader('Content-type', 'application/json')->setBody('{ "result": ' . $result . ', "message": "' . $message . '" }');
        } else {
            return $this->_redirect('user');
        }
    }

    /**
    * chatAction
    *
    * @link       /user/chat
    * @category   actions
    */
    public function chatAction()
    {
        // Initialize the chat model
        $chat = new Application_Model_Chat();
        // Get the userId
        $userId = $this->_authService->getIdentity()->user_id;

        // Get all chat messages
        $allMessages = $this->extractResult($chat->get($userId));
        
        // Aassign the result of query to variables
        // who will used to print all messages
        $this->view->assign('userId', $userId);
        $this->view->assign('messages', $allMessages);
    }
    
    /**
    * sendAction
    *
    * @package    AJAX
    * (Store new message in database)
    *
    * @link       /user/send
    * @category   actions
    */
    public function sendAction()
    {
        // Disable the layout and
        // the view's rendering
        $this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        // Initialize the chat model
        $chat = new Application_Model_Chat();
        // Create and set the result variable to 0
        // (if the action goes well it will update to 1)
        $result = 0;
        
        // Get the POST request
        $request = $this->getRequest();

        // Read all values into the POST request
        $message = $request->getPost('message', null);
        $userId = $request->getPost('userId', null);
        
        // Push the values into an array
        $params = array(
            'message' => htmlspecialchars($message),
            'user' => $userId,
            'user_chat_id' => $userId
        );
        
        // Insert new message in the database
        if ($chat->create($params)) {
            $result = 1;
        }
        
        // Check if the request come
        // from an AJAX function
        if ($request->isXmlHttpRequest()) {
            $this->getResponse()->setHeader('Content-type', 'application/json')->setBody('{ "result": ' . $result . ' }');
        } else {
            return $this->_redirect('user');
        }
    }
    
    /**
    * getAction
    *
    * @package    AJAX
    * (Get new messages)
    *
    * @link       /user/get
    * @category   actions
    */
    public function getAction()
    {
        // Disable the layout and
        // the view's rendering
        $this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        // Initialize the chat model
        $chat = new Application_Model_Chat();
        // Create and set the result variable to 0
        // (if the action goes well it will update to 1)
        $result = 0;
        // Get the userId
        $userId = $this->_authService->getIdentity()->user_id;
        
        // Get the POST request
        $request = $this->getRequest();

        // Read all values into the POST request
        $data = $request->getParam('lastData', null);
        
        // Get all the new message written after $data
        $newMessages = $this->extractResult($chat->getLastMessage($userId, $data));
        
        // Check if the request come
        // from an AJAX function
        if ($request->isXmlHttpRequest()) {
            $this->getResponse()->setHeader('Content-type', 'application/json')->setBody('{ "result": ' . $result . ', "messages": ' . json_encode($newMessages) . ' }');
        } else {
            return $this->_redirect('user');
        }
    }
    
    /**
    * profileAction
    *
    * @link       /user/profile
    * @category   actions
    */
    public function profileAction()
    {
        // Initialize the user model
        $user = new Application_Model_User();
        // Get the userId
        $userId = $this->_authService->getIdentity()->user_id;
        
        // Aassign the result of query to variables
        // who will used to print user information
        $this->view->assign('userData', $this->extractResult($user->find($userId)));
    }
    
    /**
    * passwordAction
    *
    * @link       /user/profile
    * @category   actions
    */
    public function passwordAction()
    {
        // Initialize the user model
        $user = new Application_Model_User();
        // Get userId
        $userId = $this->_authService->getIdentity()->user_id;
        
        // Read all values into the POST request
        $oldpassword = $this->getRequest()->getPost('oldpassword', null);
        $newpassword1 = $this->getRequest()->getPost('newpassword1', null);
        $newpassword2 = $this->getRequest()->getPost('newpassword2', null);
        
        // Check the validity of the values
        if ($oldpassword == null || $oldpassword == '') {
            return $this->_redirect('user/profile?err=1');
        } else if ($newpassword1 !== $newpassword2) {
            return $this->_redirect('user/profile?err=2');
        } else if ($user->checkPassword($userId, $oldpassword)) {
            // Create the password hash
            $newPwd = array(
                'password' => hash('SHA256', $newpassword1)
            );
            // Update the password in the database
            if ($user->update($newPwd, $userId)) {
                return $this->_redirect('user/profile?ok=1');
            } else {
                return $this->_redirect('user/profile?err=3');
            }
        } else {
            return $this->_redirect('user/profile?err=4');
        }
        
    }
    
    /**
    * emailAction
    *
    * @link       /user/email
    * @category   actions
    */
    public function emailAction()
    {
        // Initialize the user model
        $user = new Application_Model_User();
        // Get userId
        $userId = $this->_authService->getIdentity()->user_id;
        
        // Read all values into the POST request
        $newMail = $this->getRequest()->getPost('email', null);
        
        // Check the validity of the values
        if ($newMail == null || $newMail == '') {
            return $this->_redirect('user/profile?err=5');
        } else if (!filter_var($newMail, FILTER_VALIDATE_EMAIL)) {
            return $this->_redirect('user/profile?err=6');
        } else {
            $mail = array(
                'email' => $newMail
            );
            // Update the email in the database
            try {
                $user->update($mail, $userId);
                return $this->_redirect('user/profile?ok=2');
            } catch (Exception $e) {
                return $this->_redirect('user/profile?err=7');
            }
        }
        
    }
    
    /**
    * logoutAction
    *
    * @link       /user/logout
    * @category   actions
    */   
    public function logoutAction()
    {
        $this->_authService->clear();
        return $this->_redirect('login');
    }

    /**
    * extractResult
    *
    * @category   utilities
    */
    public function extractResult($result)
    {
        // Extract the result of a query
        // and return an array with the values
        $data = array();
        $rowsetArray = $result->toArray();
        foreach ($rowsetArray as $column => $value) {
            $data[$column] = $value;
        }
        return $data;
    }
    
}

