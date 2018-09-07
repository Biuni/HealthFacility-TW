<?php

class UserController extends Zend_Controller_Action
{

    public function init()
    {
		$this->_authService = new Application_Service_Auth();
    }

    public function indexAction()
    {
        $userId = $this->_authService->getIdentity()->user_id;
        $user = new Application_Model_User();

        $allReservations = $this->extractResult($user->extractBooking($userId, '>'));
        $pastReservations = $this->extractResult($user->extractBooking($userId, '<'));

        $this->view->assign(
            'reservations',
            $allReservations
        );
        $this->view->assign(
            'pastReservations',
            $pastReservations
        );
    }

    // Delete a reservation
    public function deleteAction()
    {
        $request = $this->getRequest();
        $this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $booking = new Application_Model_Booking();
        $result = 0;

        // --------------------------------
            $bookingId = $request->getPost('bookingId');
        // --------------------------------
        
        if ($booking->delete($bookingId)) {
            $result = 1;
            $message = 'Prenotazione annullata con successo!';
        } else {
            $message = 'Prenotazione NON annullata! Riprova.';
        }

        if ($request->isXmlHttpRequest()) { 
            $this->getResponse()->setHeader('Content-type','application/json')->setBody('{ "result": '.$result.', "message": "'.$message.'" }');
        } else {
            return $this->_redirect('user');
        }
    }

    // Get the chat message from loading page
    public function chatAction()
    {
        $userId = $this->_authService->getIdentity()->user_id;
        $chat = new Application_Model_Chat();
        $allMessages = $this->extractResult($chat->get($userId));

        $this->view->assign(
            'userId',
            $userId
        );
        $this->view->assign(
            'messages',
            $allMessages
        );
    }

    // Insert new messagge into database
    public function sendAction()
    {
        $request = $this->getRequest();
        $this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $chat = new Application_Model_Chat();
        $result = 0;

        // --------------------------------
            $message = $request->getPost('message');
            $userId = $request->getPost('userId');
        // --------------------------------

        $params = array(
            'message' => $message,
            'user' => $userId,
            'user_chat_id' => $userId
        );
        
        if ($chat->create($params)) {
            $result = 1;
        }

        if ($request->isXmlHttpRequest()) { 
            $this->getResponse()->setHeader('Content-type','application/json')->setBody('{ "result": '.$result.' }');
        } else {
            return $this->_redirect('user');
        }
    }

    // Get new messagge to refresh chat
    public function getAction()
    {
        $userId = $this->_authService->getIdentity()->user_id;
        $request = $this->getRequest();
        $this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $chat = new Application_Model_Chat();
        $result = 0;

        // --------------------------------
            $data = $request->getParam('lastData');
        // --------------------------------

        $newMessages = $this->extractResult($chat->getLastMessage($userId, $data));

        if ($request->isXmlHttpRequest()) {
            $this->getResponse()->setHeader('Content-type','application/json')->setBody('{ "result": '.$result.', "messages": '.json_encode($newMessages).' }');
        } else {
            return $this->_redirect('user');
        }
    }

    public function profileAction()
    {
        
    }

    public function logoutAction()
    {
        $this->_authService->clear();
        return $this->_redirect('login');
    }

    // -----------------------------------
    /**
    * Clean an prettifier SQL query result
    */
    public function extractResult($result){
        $data = [];
        $rowsetArray = $result->toArray();
        foreach ($rowsetArray as $column => $value) {
            $data[$column] = $value;
        }
        return $data;
    }

}

