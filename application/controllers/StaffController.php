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
        $userId = $this->_authService->getIdentity()->user_id;
        $staff = new Application_Model_Staff();

        $allAppointments = $this->extractResult($staff->getAppointments($userId));
        $day = $week = $month = $year = 0;
        $thisDay = date('Y-m-d');
        $thisMonth = date('Y-m');
        $thisYear = date('Y');
        $lastSunday = date('Y-m-d', strtotime('sunday last week'));
        $thisSunday = date('Y-m-d', strtotime('sunday this week'));
        foreach ($allAppointments as $value) {
            $createDate = new DateTime($value['date']);
            if ($createDate->format('Y-m-d') == $thisDay) { $day++; }
            if ($createDate->format('Y-m') == $thisMonth) { $month++; }
            if ($createDate->format('Y') == $thisYear) { $year++; }
            if ($createDate->format('Y-m-d') > $lastSunday &&
                $createDate->format('Y-m-d') <= $thisSunday) { $week++; }
        }

        $this->view->assign(
            'details',
            $this->extractResult($staff->find($userId))
        );
        $this->view->assign(
            'day',
            $day
        );
        $this->view->assign(
            'week',
            $week
        );
        $this->view->assign(
            'month',
            $month
        );
        $this->view->assign(
            'year',
            $year
        );
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
            return $this->_redirect('staff');
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
            return $this->_redirect('staff');
        }
    }

    // View the agenda
    public function agendaAction()
    {
        $userId = $this->_authService->getIdentity()->user_id;
        $dateToday = date('Y-m-d');
        $staff = new Application_Model_Staff();

        $this->view->assign(
            'appointmentsToday',
            $this->extractResult($staff->getAppointmentsToday($userId, $dateToday))
        );
        $this->view->assign(
            'dateToday',
            $dateToday
        );
    }

    // Get agenda by date
    public function readagendaAction()
    {
        $request = $this->getRequest();
        $this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $userId = $this->_authService->getIdentity()->user_id;
        $staff = new Application_Model_Staff();
        $result = 0;

        // --------------------------------
            $data = $request->getParam('data');
        // --------------------------------
        
        $appointments = $this->extractResult($staff->getAppointmentsToday($userId, $data));
        if (!empty($appointments)) {
            $result = 1;
        }

        if ($request->isXmlHttpRequest()) { 
            $this->getResponse()->setHeader('Content-type','application/json')->setBody('{ "result": '.$result.', "message": '.json_encode($appointments).' }');
        } else {
            return $this->_redirect('staff');
        }
    }

    public function deletebookAction()
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
            $message = 'Prenotazione cancellata con successo!';
        } else {
            $message = 'Prenotazione NON cancellata! Riprova.';
        }

        if ($request->isXmlHttpRequest()) { 
            $this->getResponse()->setHeader('Content-type','application/json')->setBody('{ "result": '.$result.', "message": "'.$message.'" }');
        } else {
            return $this->_redirect('staff');
        }
    }

    public function updatebookAction()
    {
        $request = $this->getRequest();
        $this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $booking = new Application_Model_Booking();
        $service = new Application_Model_Service();
        $result = 0;

        // --------------------------------
            $bookingId = $request->getPost('bookingId');
            $serviceId = $request->getPost('seriviceId');
            $date = $request->getPost('date');
        // --------------------------------

        // Check if the service is open in the booking's hour
        if ($service->checkServiceOpen($serviceId, $date)) {
            // Check if the service haven't other reservation
            if ($booking->checkServiceReservation($serviceId, $date)) {

                // Insert new reservation into database
                $updateReservation = $booking->update(array(
                    'date' => $date
                ), $bookingId);

                if ($updateReservation) {
                    $message = 'Prenotazione modificata!<br>La nuova data sarà: <strong>'.$date.'</strong>';
                    $result = 1;
                } else {
                    $message = '<strong>ATTENZIONE!</strong> E\' stato riscontrato un errore nella modifica! Riprovare.';
                }
            } else {
                $message = '<strong>ATTENZIONE!</strong> Data <u>non disponibile</u> perchè già prenotata da un altro utente.';
            }
        } else {
            $message = '<strong>ATTENZIONE!</strong> Questo orario non è disponibile.';
        }

        if ($request->isXmlHttpRequest()) { 
            $this->getResponse()->setHeader('Content-type','application/json')->setBody('{ "result": '.$result.', "message": "'.$message.'" }');
        } else {
            return $this->_redirect('department');
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

