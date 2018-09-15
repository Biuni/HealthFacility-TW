<?php

/**
* StaffController
* 
* This is the controller class
* of the staff's reserved area.
*
* @author     Gianluca Bonifazi
* @category   controllers 
* @copyright  Univpm (c) 2018
*/

class StaffController extends Zend_Controller_Action
{
    /**
    * Initialize action controller
    */
    public function init()
    {
        // Set the staff layout
        $this->_helper->layout->setLayout('staff');
        // Initialize the auth service
        $this->_authService = new Application_Service_Auth();
    }

    /**
    * indexAction
    *
    * @link       /staff
    * @category   actions
    */
    public function indexAction()
    {
        // Initialize the staff model
        $staff = new Application_Model_Staff();
        
        // Get the userId
        $userId = $this->_authService->getIdentity()->user_id;
        // Get all the staff appointments
        $allAppointments = $this->extractResult($staff->getAppointments($userId));
        $day = $week = $month = $year = 0;
        $thisDay = date('Y-m-d');
        $thisMonth = date('Y-m');
        $thisYear = date('Y');
        $lastSunday = date('Y-m-d', strtotime('sunday last week'));
        $thisSunday = date('Y-m-d', strtotime('sunday this week'));
        foreach ($allAppointments as $value) {
            $createDate = new DateTime($value['date']);
            // If appointment's date is euqal to today date
            if ($createDate->format('Y-m-d') == $thisDay) {
                $day++;
            }
            // If appointment's date is euqal to this month
            if ($createDate->format('Y-m') == $thisMonth) {
                $month++;
            }
            // If appointment's date is euqal to this year
            if ($createDate->format('Y') == $thisYear) {
                $year++;
            }
            // If appointment's date is in this week
            if ($createDate->format('Y-m-d') > $lastSunday && $createDate->format('Y-m-d') <= $thisSunday) {
                $week++;
            }
        }
        
        // Get staff information and assign it to variable
        // who will used to print the name of doctor
        $this->view->assign('details', $this->extractResult($staff->find($userId)));
        // Aassign the result of counts to variables
        // who will used to print the number of appointments
        $this->view->assign('day', $day);
        $this->view->assign('week', $week);
        $this->view->assign('month', $month);
        $this->view->assign('year', $year);
    }

    /**
    * userlistAction
    *
    * @link       /staff/userlist
    * @category   actions
    */
    public function userlistAction()
    {
        // Initialize the user model
        $user = new Application_Model_User();
        // Get all user information and assign it to variable
        // who will used to print the list of chats
        $this->view->assign('userList', $this->extractResult($user->get()));
    }
    
    /**
    * chatAction
    *
    * @link       /staff/chat/id/:id
    * @category   actions
    */
    public function chatAction()
    {
        // Initialize the chat model
        $chat = new Application_Model_Chat();

        // GET the id parameter
        $id = $this->_getParam('id', 1);
        $userId = $this->_authService->getIdentity()->user_id;

        // Fetch all chat messagges
        $allMessages = $this->extractResult($chat->get($id));
        
        // Assign result to variable
        // who will used to print the list of message
        $this->view->assign('userId', $id);
        $this->view->assign('messages', $allMessages);
    }
    
    /**
    * sendAction
    *
    * @package    AJAX
    * (Insert new message to database)
    *
    * @link       /staff/send
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
        $userChatId = $request->getPost('userChatId', null);
        
        // Push values into an array
        $params = array(
            'message' => htmlspecialchars($message),
            'user' => $userId,
            'user_chat_id' => $userChatId
        );
        
        // Insert the record in the database
        if ($chat->create($params)) {
            $result = 1;
        }
        
        // Check if the request come
        // from an AJAX function
        if ($request->isXmlHttpRequest()) {
            $this->getResponse()->setHeader('Content-type', 'application/json')->setBody('{ "result": ' . $result . ' }');
        } else {
            return $this->_redirect('staff');
        }
    }
    
    /**
    * getAction
    *
    * @package    AJAX
    * (Get new messagge to
    * refresh chat messages)
    *
    * @link       /staff/get
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
        
        // Get the POST request
        $request = $this->getRequest();

        // Read all values into the POST request
        $data = $request->getParam('lastData', null);
        $userId = $request->getParam('userChatId', null);
        
        // Get all messages written after the date stored in $data
        $newMessages = $this->extractResult($chat->getLastMessage($userId, $data));
        
        // Check if the request come
        // from an AJAX function
        if ($request->isXmlHttpRequest()) {
            $this->getResponse()->setHeader('Content-type', 'application/json')->setBody('{ "result": ' . $result . ', "messages": ' . json_encode($newMessages) . ' }');
        } else {
            return $this->_redirect('staff');
        }
    }
    
    /**
    * agendaAction
    *
    * @link       /staff/agenda
    * @category   actions
    */
    public function agendaAction()
    {
        // Initialize the staff model
        $staff = new Application_Model_Staff();
        // Get the userId
        $userId = $this->_authService->getIdentity()->user_id;
        $dateToday = date('Y-m-d');
        
        // Assign the result to variable
        // who will used to print the list of 
        // bookings in the agenda today
        $this->view->assign('appointmentsToday', $this->extractResult($staff->getAppointmentsToday($userId, $dateToday)));
        $this->view->assign('dateToday', $dateToday);
    }
    
    /**
    * readagendaAction
    *
    * @package    AJAX
    * (Read the bookings by date)
    *
    * @link       /staff/readagenda
    * @category   actions
    */
    public function readagendaAction()
    {
        // Disable the layout and
        // the view's rendering
        $this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        // Initialize the chat model
        $staff = new Application_Model_Staff();
        // Create and set the result variable to 0
        // (if the action goes well it will update to 1)
        $result = 0;
        // Get the userId
        $userId = $this->_authService->getIdentity()->user_id;
        
        // Get the POST request
        $request = $this->getRequest();

        // Read all values into the POST request
        $data = $request->getParam('data', null);
        
        // Get all the appointments booked in the date
        $appointments = $this->extractResult($staff->getAppointmentsToday($userId, $data));
        if (!empty($appointments)) {
            $result = 1;
        }
        
        // Check if the request come
        // from an AJAX function
        if ($request->isXmlHttpRequest()) {
            $this->getResponse()->setHeader('Content-type', 'application/json')->setBody('{ "result": ' . $result . ', "message": ' . json_encode($appointments) . ' }');
        } else {
            return $this->_redirect('staff');
        }
    }
    
    /**
    * deletebookAction
    *
    * @package    AJAX
    * (Delete the booking)
    *
    * @link       /staff/deletebook
    * @category   actions
    */
    public function deletebookAction()
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
        
        // Delete the appointment
        if ($booking->delete($bookingId)) {
            $result = 1;
            $message = 'Prenotazione cancellata con successo!';
        } else {
            $message = 'Prenotazione NON cancellata! Riprova.';
        }
        
        // Check if the request come
        // from an AJAX function
        if ($request->isXmlHttpRequest()) {
            $this->getResponse()->setHeader('Content-type', 'application/json')->setBody('{ "result": ' . $result . ', "message": "' . $message . '" }');
        } else {
            return $this->_redirect('staff');
        }
    }
    
    /**
    * updatebookAction
    *
    * @package    AJAX
    * (Update the booking)
    *
    * @link       /staff/updatebook
    * @category   actions
    */
    public function updatebookAction()
    {
        // Disable the layout and
        // the view's rendering
        $this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        // Initialize the booking and service model
        $booking = new Application_Model_Booking();
        $service = new Application_Model_Service();
        // Create and set the result variable to 0
        // (if the action goes well it will update to 1)
        $result = 0;
        
        // Get the POST request
        $request = $this->getRequest();

        // Read all values into the POST request
        $bookingId = $request->getPost('bookingId', null);
        $serviceId = $request->getPost('seriviceId', null);
        $date = $request->getPost('date', null);
        
        // Check if the service is open in the booking's hour
        if ($service->checkServiceOpen($serviceId, $date)) {
            // Check if the service haven't other reservation
            if ($booking->checkServiceReservation($serviceId, $date)) {
                
                // Update the reservation into database
                $updateReservation = $booking->update(array(
                    'date' => $date
                ), $bookingId);
                
                if ($updateReservation) {
                    $message = 'Prenotazione modificata!<br>La nuova data sarà: <strong>' . $date . '</strong>';
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
        
        // Check if the request come
        // from an AJAX function
        if ($request->isXmlHttpRequest()) {
            $this->getResponse()->setHeader('Content-type', 'application/json')->setBody('{ "result": ' . $result . ', "message": "' . $message . '" }');
        } else {
            return $this->_redirect('department');
        }
    }
    
    /**
    * logoutAction
    *
    * @link       /staff/logout
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

