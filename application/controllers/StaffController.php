<?php

class StaffController extends Zend_Controller_Action
{

    public function init()
    {
		$this->_helper->layout->setLayout('staff');
        $this->_authService = new Application_Service_Auth();
    }

    public function indexAction()
    {
        // action body
    }

    // List of all user
    public function userlistAction()
    {
        $user = new Application_Model_User();

        $this->view->assign(
            'userList',
            $this->extractResult($user->get())
        );
    }

    // Open a chat with an user
    public function chatAction()
    {
        $id = $this->_getParam('id', 1);

        $userId = $this->_authService->getIdentity()->user_id;
        $chat = new Application_Model_Chat();
        $allMessages = $this->extractResult($chat->get($id));

        $this->view->assign(
            'userId',
            $id
        );
        $this->view->assign(
            'messages',
            $allMessages
        );
    }

    // Insert a new messagge into database
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
            $userChatId = $request->getPost('userChatId');
        // --------------------------------

        $params = array(
            'message' => $message,
            'user' => $userId,
            'user_chat_id' => $userChatId
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

    // Get new messagge to refresh chat messages
    public function getAction()
    {
        $request = $this->getRequest();
        $this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $chat = new Application_Model_Chat();
        $result = 0;

        // --------------------------------
            $data = $request->getParam('lastData');
            $userId = $request->getParam('userChatId');
        // --------------------------------

        $newMessages = $this->extractResult($chat->getLastMessage($userId, $data));

        if ($request->isXmlHttpRequest()) {
            $this->getResponse()->setHeader('Content-type','application/json')->setBody('{ "result": '.$result.', "messages": '.json_encode($newMessages).' }');
        } else {
            return $this->_redirect('user');
        }
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

