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

    // List all users and update or delete it
    public function userAction()
    {
        $user = new Application_Model_User();
        $this->view->assign(
            'allUser',
            $this->extractResult($user->get())
        );
    }

    // List all staffs and update or delete it
    public function staffAction()
    {
        $staff = new Application_Model_Staff();
        $this->view->assign(
            'allStaff',
            $this->extractResult($staff->get())
        );
    }

    // Insert a new staff into database
    public function insertstaffAction()
    {
        // action body
    }

    // List all departments and update or delete it
    public function departmentAction()
    {
        $department = new Application_Model_Department();
        $this->view->assign(
            'allDepartment',
            $this->extractResult($department->get())
        );
    }

    // Insert a new department into database
    public function insertdepartmentAction()
    {
        // action body
    }

    // List all services and update or delete it
    public function serviceAction()
    {
        $department = new Application_Model_Service();
        $this->view->assign(
            'allService',
            $this->extractResult($department->getServiceInfo())
        );
    }

    // Insert a new service into database
    public function insertserviceAction()
    {
        // action body
    }

    // List all reservations and update or delete it
    public function bookingAction()
    {
        $booking = new Application_Model_Booking();
        $this->view->assign(
            'allReservations',
            $this->extractResult($booking->getAllReservations())
        );
    }

    // Insert a new reservation into database
    public function insertbookingAction()
    {
        // action body
    }

    // List all faq and update or delete it
    public function faqAction()
    {
        $faq = new Application_Model_Faq();
        $this->view->assign(
            'allFaq',
            $this->extractResult($faq->get())
        );
    }

    // Insert a new faq into database
    public function insertfaqAction()
    {
        $request = $this->getRequest();
        $faq = new Application_Model_Faq();

        // -----------------------------------
        $question = $request->getPost('question');
        $answer = $request->getPost('answer');
        // -----------------------------------

        if ($question != null && $question != '' &&
            $answer   != null && $answer   != '') {
            $faq->create(array(
                'question' => $question,
                'answer' => $answer
            ));
            return $this->_redirect('admin/insertfaq?ok=1');
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

