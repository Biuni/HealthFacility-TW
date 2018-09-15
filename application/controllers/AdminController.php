<?php

/**
* AdminController
* 
* This is the controller class
* of the admin's reserved area.
*
* @author     Gianluca Bonifazi
* @category   controllers 
* @copyright  Univpm (c) 2018
*/

class AdminController extends Zend_Controller_Action
{
    /**
    * Initialize action controller
    */
    public function init()
    {
        // Set the admin layout
        $this->_helper->layout->setLayout('admin');
        // Initialize the auth service
        $this->_authService = new Application_Service_Auth();
    }

    /**
    * indexAction
    *
    * @link       /admin
    * @category   actions
    */
    public function indexAction()
    {
        // Initializes the models useful for the action
        $booking = new Application_Model_Booking();
        $service = new Application_Model_Service();
        $department = new Application_Model_Department();
        
        // Get the POST request and check if
        // the time interval was setted
        $request = $this->getRequest();
        $dateStart = $request->getPost('dateStart', null);
        $dateEnd = $request->getPost('dateEnd', null);
        if ($dateStart != null || $dateEnd != null) {
            $this->view->assign('dateStart', $dateStart);
            $this->view->assign('dateEnd', $dateEnd);
        }

        // Get the numbers of bookings
        // per month (used to print the chart)
        $allReservations = $this->extractResult($booking->getAllReservations($dateStart, $dateEnd));
        $labels = array('Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre');
        $jan = $feb = $mar = $apr = $may = $jun = $jul = $aug = $sept = $oct = $nov = $dec = 0;
        foreach ($allReservations as $value) {
            $monthNum = date('n', strtotime($value['date']));
            // January
            if ($monthNum - 1 == 0) {
                $jan++;
            }
            // February
            if ($monthNum - 1 == 1) {
                $feb++;
            }
            // March
            if ($monthNum - 1 == 2) {
                $mar++;
            }
            // April
            if ($monthNum - 1 == 3) {
                $apr++;
            }
            // May
            if ($monthNum - 1 == 4) {
                $may++;
            }
            // June
            if ($monthNum - 1 == 5) {
                $jun++;
            }
            // July
            if ($monthNum - 1 == 6) {
                $jul++;
            }
            // August
            if ($monthNum - 1 == 7) {
                $aug++;
            }
            // September
            if ($monthNum - 1 == 8) {
                $sept++;
            }
            // October
            if ($monthNum - 1 == 9) {
                $oct++;
            }
            // November
            if ($monthNum - 1 == 10) {
                $nov++;
            }
            // December
            if ($monthNum - 1 == 11) {
                $dec++;
            }
        }

        // Assign the view's variables used by 
        // Javascript to print the Chart
        $this->view->assign('dataOfBookingPerMonth', array($jan,$feb,$mar,$apr,$may,$jun,$jul,$aug,$sept,$oct,$nov,$dec));
        $this->view->assign('labelsOfBookingPerMonth', $labels);
        
        // ------------------------------

        // Get the numbers of bookings
        // per service (used to print the chart)
        $allServices = $this->extractResult($service->get());
        $labels2 = array();
        $serviceCount = array();
        foreach ($allServices as $value) {
            // Create the label array
            $labels2[] = $value['name'];
            $getNumOfService = $this->extractResult($booking->countPerService($value['service_id'], $dateStart, $dateEnd));
            // Create the data array
            $serviceCount[] = $getNumOfService['rows'];
        }
        
        // Assign the view's variables used by 
        // Javascript to print the Chart
        $this->view->assign('dataOfBookingPerService', $serviceCount);
        $this->view->assign('labelsOfBookingPerService', $labels2);
        
        // ---------------------------------

        // Get the numbers of bookings
        // per department (used to print the chart)
        $allDepartment = $this->extractResult($department->get());
        $labels3 = array();
        $departmentCount = array();
        foreach ($allDepartment as $value) {
            // Create the label array
            $labels3[] = $value['name'];
            $countPerDepartment = $booking->countPerDepartment($value['department_id'], $dateStart, $dateEnd);
            // Create the data array
            if ($countPerDepartment['rows'] == null) {
                $departmentCount[] = 0;
            } else {
                $departmentCount[] = $countPerDepartment['rows'];
            }
        }
        
        // Assign the view's variables used by 
        // Javascript to print the Chart
        $this->view->assign('dataOfBookingPerDepartment', $departmentCount);
        $this->view->assign('labelsOfBookingPerDepartment', $labels3);
        
    }
    
    /**
    * userAction
    *
    * @link       /admin/user
    * @category   actions
    */
    public function userAction()
    {
        // Initialize the user model
        $user = new Application_Model_User();
        // Get all user and assign it to variable
        // who will used in the view to create the list
        $this->view->assign('allUser', $this->extractResult($user->get()));
    }
    
    /**
    * userstatsAction
    *
    * @link       /admin/user
    * @category   actions
    */
    public function userstatsAction()
    {
        // Initializes the models useful for the action
        $booking = new Application_Model_Booking();
        $service = new Application_Model_Service();
        $department = new Application_Model_Department();
        
        // Read the GET request and assign
        // the id into a variable
        $request = $this->getRequest();
        $userId = $request->getParam('id', null);
        
        // Get the numbers of bookings
        // per month (used to print the chart)
        $allReservations = $booking->getAllReservationsByUser($userId);
        $labels = array('Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre');
        $jan = $feb = $mar = $apr = $may = $jun = $jul = $aug = $sept = $oct = $nov = $dec = 0;
        foreach ($allReservations as $values) {
            $monthNum = date('n', strtotime($values['date']));
            // January
            if ($monthNum - 1 == 0) {
                $jan++;
            }
            // February
            if ($monthNum - 1 == 1) {
                $feb++;
            }
            // March
            if ($monthNum - 1 == 2) {
                $mar++;
            }
            // April
            if ($monthNum - 1 == 3) {
                $apr++;
            }
            // May
            if ($monthNum - 1 == 4) {
                $may++;
            }
            // June
            if ($monthNum - 1 == 5) {
                $jun++;
            }
            // July
            if ($monthNum - 1 == 6) {
                $jul++;
            }
            // August
            if ($monthNum - 1 == 7) {
                $aug++;
            }
            // September
            if ($monthNum - 1 == 8) {
                $sept++;
            }
            // October
            if ($monthNum - 1 == 9) {
                $oct++;
            }
            // November
            if ($monthNum - 1 == 10) {
                $nov++;
            }
            // December
            if ($monthNum - 1 == 11) {
                $dec++;
            }
        }

        // Assign the view's variables used by 
        // Javascript to print the Chart
        $this->view->assign('dataOfUserPerMonth', array($jan,$feb,$mar,$apr,$may,$jun,$jul,$aug,$sept,$oct,$nov,$dec));
        $this->view->assign('labelsOfUserPerMonth', $labels);
        
        // ------------------------------

        // Get the numbers of bookings
        // per service (used to print the chart)
        $allServices = $this->extractResult($service->get());
        $labels2 = array();
        $serviceCount = array();
        foreach ($allServices as $value) {
            // Create the label array
            $labels2[] = $value['name'];
            $getNumOfService = $this->extractResult($booking->countPerServicePerUser($value['service_id'], $userId));
            // Create the data array
            $serviceCount[] = $getNumOfService['rows'];
        }
        
        // Assign the view's variables used by 
        // Javascript to print the Chart
        $this->view->assign('dataOfUserPerService', $serviceCount);
        $this->view->assign('labelsOfUserPerService', $labels2);
        
        // ---------------------------------

        // Get the numbers of bookings
        // per department (used to print the chart)
        $allDepartment = $this->extractResult($department->get());
        $labels3 = array();
        $departmentCount = array();
        foreach ($allDepartment as $value) {
            // Create the label array
            $labels3[] = $value['name'];
            $countPerDepartment = $booking->countPerDepartmentPerUser($value['department_id'], $userId);
            // Create the data array
            if ($countPerDepartment['rows'] == null) {
                $departmentCount[] = 0;
            } else {
                $departmentCount[] = $countPerDepartment['rows'];
            }
        }
        
        // Assign the view's variables used by 
        // Javascript to print the Chart
        $this->view->assign('dataOfUserPerDepartment', $departmentCount);
        $this->view->assign('labelsOfUserPerDepartment', $labels3);
    }
    
    /**
    * staffAction
    *
    * @link       /admin/staff
    * @category   actions
    */
    public function staffAction()
    {
        // Initialize the staff model
        $staff = new Application_Model_Staff();
        // Get all staff and assign it to variable
        // who will used in the view to create the list
        $this->view->assign('allStaff', $this->extractResult($staff->get()));
    }
    
    /**
    * insertstaffAction
    *
    * @link       /admin/insertstaff
    * @category   actions
    */
    public function insertstaffAction()
    {
        // Initialize the staff model
        $staff = new Application_Model_Staff();
        
        // Get the POST request
        $request = $this->getRequest();

        // Read all values into the POST request
        $username = $request->getPost('username', null);
        $password = $request->getPost('password', null);
        $name = $request->getPost('name', null);
        $surname = $request->getPost('surname', null);
        $code = $request->getPost('code', null);
        $email = $request->getPost('email', null);
        
        // Check the validity of the values
        if ($username   != null && $username    != '' &&
            $password   != null && $password    != '' &&
            $name       != null && $name        != '' &&
            $surname    != null && $surname     != '' &&
            $code       != null && $code        != '' &&
            $email      != null && $email       != '') {
            // Try to create a new record
            // in the database with the new staff
            try {
                $staff->create(array(
                    'username'  => $username,
                    'password'  => hash('SHA256', $password),
                    'role'      => 'staff',
                    'email'     => $email,
                    'name'      => $name,
                    'surname'   => $surname,
                    'code'      => $code
                ));
                return $this->_redirect('admin/insertstaff?ok=1');
            }
            catch (Exception $e) {
                // If username or email are already used
                // print an error.
                return $this->_redirect('admin/insertstaff?err=1');
            }
        }
    }
    
    /**
    * departmentAction
    *
    * @link       /admin/department
    * @category   actions
    */
    public function departmentAction()
    {
        // Initialize the department model
        $department = new Application_Model_Department();
        // Get all departments and assign it to variable
        // who will used in the view to create the list
        $this->view->assign('allDepartment', $this->extractResult($department->get()));
    }
    
    /**
    * insertdepartmentAction
    *
    * @link       /admin/insertdepartment
    * @category   actions
    */
    public function insertdepartmentAction()
    {
        // Initialize the department model
        $department = new Application_Model_Department();
        
        // Get the POST request
        $request = $this->getRequest();

        // Read all values into the POST request
        $name = $request->getPost('name', null);
        $description = $request->getPost('description', null);
        $place = $request->getPost('place', null);
        
        // Check the validity of the values
        if ($description != null && $description != '' &&
            $name        != null && $name        != '' &&
            $place       != null && $place       != '') {
            // Create a new record in the
            // database with the new department
            $department->create(array(
                'name' => $name,
                'description' => $description,
                'place' => $place
            ));
            return $this->_redirect('admin/insertdepartment?ok=1');
        }
    }
    
    /**
    * serviceAction
    *
    * @link       /admin/service
    * @category   actions
    */
    public function serviceAction()
    {
        // Initialize the service model
        $service = new Application_Model_Service();
        // Get all services and assign it to variable
        // who will used in the view to create the list
        $this->view->assign('allService', $this->extractResult($service->getServiceInfo()));
    }
    
    /**
    * insertserviceAction
    *
    * @link       /admin/insertservice
    * @category   actions
    */
    public function insertserviceAction()
    {
        // Initialize the service model
        $service = new Application_Model_Service();
        
        // Get the POST request
        $request = $this->getRequest();

        // Read all values into the POST request
        $name = $request->getPost('name', null);
        $department = $request->getPost('department', null);
        $description = $request->getPost('description', null);
        $prescriptions = $request->getPost('prescriptions', null);
        $staff = $request->getPost('staff', null);

        // Create the schedule JSON
        $schedule = array(
            'schedule' => array(
                array(
                    'day' => 'Domenica',
                    'm_opening' => ($request->getPost('m_opening-0') == '') ? null : $request->getPost('m_opening-0'),
                    'm_closing' => ($request->getPost('m_closing-0') == '') ? null : $request->getPost('m_closing-0'),
                    'a_opening' => ($request->getPost('a_opening-0') == '') ? null : $request->getPost('a_opening-0'),
                    'a_closing' => ($request->getPost('a_closing-0') == '') ? null : $request->getPost('a_closing-0')
                ),
                array(
                    'day' => 'Lunedì',
                    'm_opening' => ($request->getPost('m_opening-1') == '') ? null : $request->getPost('m_opening-1'),
                    'm_closing' => ($request->getPost('m_closing-1') == '') ? null : $request->getPost('m_closing-1'),
                    'a_opening' => ($request->getPost('a_opening-1') == '') ? null : $request->getPost('a_opening-1'),
                    'a_closing' => ($request->getPost('a_closing-1') == '') ? null : $request->getPost('a_closing-1')
                ),
                array(
                    'day' => 'Martedì',
                    'm_opening' => ($request->getPost('m_opening-2') == '') ? null : $request->getPost('m_opening-2'),
                    'm_closing' => ($request->getPost('m_closing-2') == '') ? null : $request->getPost('m_closing-2'),
                    'a_opening' => ($request->getPost('a_opening-2') == '') ? null : $request->getPost('a_opening-2'),
                    'a_closing' => ($request->getPost('a_closing-2') == '') ? null : $request->getPost('a_closing-2')
                ),
                array(
                    'day' => 'Mercoledì',
                    'm_opening' => ($request->getPost('m_opening-3') == '') ? null : $request->getPost('m_opening-3'),
                    'm_closing' => ($request->getPost('m_closing-3') == '') ? null : $request->getPost('m_closing-3'),
                    'a_opening' => ($request->getPost('a_opening-3') == '') ? null : $request->getPost('a_opening-3'),
                    'a_closing' => ($request->getPost('a_closing-3') == '') ? null : $request->getPost('a_closing-3')
                ),
                array(
                    'day' => 'Giovedì',
                    'm_opening' => ($request->getPost('m_opening-4') == '') ? null : $request->getPost('m_opening-4'),
                    'm_closing' => ($request->getPost('m_closing-4') == '') ? null : $request->getPost('m_closing-4'),
                    'a_opening' => ($request->getPost('a_opening-4') == '') ? null : $request->getPost('a_opening-4'),
                    'a_closing' => ($request->getPost('a_closing-4') == '') ? null : $request->getPost('a_closing-4')
                ),
                array(
                    'day' => 'Venerdì',
                    'm_opening' => ($request->getPost('m_opening-5') == '') ? null : $request->getPost('m_opening-5'),
                    'm_closing' => ($request->getPost('m_closing-5') == '') ? null : $request->getPost('m_closing-5'),
                    'a_opening' => ($request->getPost('a_opening-5') == '') ? null : $request->getPost('a_opening-5'),
                    'a_closing' => ($request->getPost('a_closing-5') == '') ? null : $request->getPost('a_closing-5')
                ),
                array(
                    'day' => 'Sabato',
                    'm_opening' => ($request->getPost('m_opening-6') == '') ? null : $request->getPost('m_opening-6'),
                    'm_closing' => ($request->getPost('m_closing-6') == '') ? null : $request->getPost('m_closing-6'),
                    'a_opening' => ($request->getPost('a_opening-6') == '') ? null : $request->getPost('a_opening-6'),
                    'a_closing' => ($request->getPost('a_closing-6') == '') ? null : $request->getPost('a_closing-6')
                )
            )
        );
        
        // Check the validity of the values
        if ($description != null && $description != '' &&
            $name        != null && $name        != '' &&
            $staff       != null && $staff       != '' &&
            $department  != null && $department  != '') {
            // Create a new record in the
            // database with the new service
            $service->create(array(
                'name' => $name,
                'department' => $department,
                'description' => $description,
                'staff' => $staff,
                'schedule' => json_encode($schedule),
                'prescriptions' => ($prescriptions == '') ? 'Nessuna prescrizione.' : $prescriptions
            ));
            return $this->_redirect('admin/insertservice?ok=1');
        }

        // ---------------------------------
        
        // Initialize the department and the staff model
        $department = new Application_Model_Department();
        $staff = new Application_Model_Staff();
        // Get all departments and staff users and assign it to
        // two variables who will used in the view to create the
        // options in the HTML select elements
        $this->view->assign('allDepartment', $this->extractResult($department->get()));
        $this->view->assign('allStaff', $this->extractResult($staff->get()));
    }
    
    /**
    * bookingAction
    *
    * @link       /admin/booking
    * @category   actions
    */
    public function bookingAction()
    {
        // Initialize the booking model
        $booking = new Application_Model_Booking();
        // Get all bookings and assign it to variable
        // who will used in the view to create the list
        $this->view->assign('allReservations', $this->extractResult($booking->getAllReservations()));
    }
    
    /**
    * insertbookingAction
    *
    * @link       /admin/insertbooking
    * @category   actions
    */
    public function insertbookingAction()
    {
        // Initialize the booking model
        $booking = new Application_Model_Booking();
        
        // Get the POST request
        $request = $this->getRequest();

        // Read all values into the POST request
        $user = $request->getPost('user', null);
        $service = $request->getPost('service', null);
        $date = $request->getPost('date', null);
        
        // Check the validity of the values
        if ($service != null && $service != '' &&
            $user    != null && $user    != '' &&
            $date    != null && $date    != '') {
            // Create a new record in the
            // database with the new booking
            $booking->create(array(
                'user' => $user,
                'service' => $service,
                'date' => $date
            ));
            return $this->_redirect('admin/insertbooking?ok=1');
        }

        // ---------------------------------
        
        // Initialize the user and the service model
        $service = new Application_Model_Service();
        $user = new Application_Model_User();

        // Get all users and services and assign it to
        // two variables who will used in the view to create the
        // options in the HTML select elements
        $this->view->assign('allUser', $this->extractResult($user->get()));
        $this->view->assign('allService', $this->extractResult($service->get()));
    }
    
    /**
    * insertbookingadminAction
    *
    * @package    AJAX
    * (Check booking availability)
    *
    * @link       /admin/insertbookingadmin
    * @category   actions
    */
    public function insertbookingadminAction()
    {
        // Disable the layout and
        // the view's rendering
        $this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        // Initialize booking and service model
        $booking = new Application_Model_Booking();
        $service = new Application_Model_Service();
        // Create and set the result variable to 0
        // (if the action goes well it will update to 1)
        $result = 0;
        
        // Get the POST request
        $request = $this->getRequest();

        // Read all values into the POST request
        $userId = $request->getPost('userId', null);
        $serviceId = $request->getPost('seriviceId', null);
        $date = $request->getPost('date', null);
        
        // Check if the service is open in the booking's hour
        if ($service->checkServiceOpen($serviceId, $date)) {
            // Check if the user haven't other reservation
            if ($booking->checkUserReservation($userId, $date)) {
                // Check if the service haven't other reservation
                if ($booking->checkServiceReservation($serviceId, $date)) {
                    $message = 'La data: <strong>' . $date . '</strong> è disponibile!';
                    $result = 1;
                } else {
                    $message = '<strong>ATTENZIONE!</strong> La data <strong>' . $date . '</strong> <u>non è disponibile</u> perchè già prenotata da un altro utente.';
                }
            } else {
                $message = '<strong>ATTENZIONE!</strong> L\'utente già un altro appuntamento per la data <strong>' . $date . '</strong>.';
            }
        } else {
            $message = '<strong>ATTENZIONE!</strong> Questo orario <strong>' . $date . '</strong> non è disponibile a prenotazioni.';
        }
        
        // Check if the request come
        // from an AJAX function
        if ($request->isXmlHttpRequest()) {
            $this->getResponse()->setHeader('Content-type', 'application/json')->setBody('{ "result": ' . $result . ', "message": "' . $message . '" }');
        } else {
            return $this->_redirect('admin');
        }
    }
    
    /**
    * faqAction
    *
    * @link       /admin/faq
    * @category   actions
    */
    public function faqAction()
    {
        // Initialize the faq model
        $faq = new Application_Model_Faq();
        // Get all faq and assign it to variable
        // who will used in the view to create the list
        $this->view->assign('allFaq', $this->extractResult($faq->get()));
    }
    
    /**
    * insertfaqAction
    *
    * @link       /admin/insertfaq
    * @category   actions
    */
    public function insertfaqAction()
    {
        // Initialize the faq model
        $faq = new Application_Model_Faq();
        
        // Get the POST request
        $request = $this->getRequest();

        // Read all values into the POST request
        $question = $request->getPost('question', null);
        $answer = $request->getPost('answer', null);
        
        // Check the validity of the values
        if ($question   != null && $question != '' &&
            $answer     != null && $answer   != '') {
            // Create a new record in the
            // database with the new faq
            $faq->create(array(
                'question' => $question,
                'answer' => $answer
            ));
            return $this->_redirect('admin/insertfaq?ok=1');
        }
        
    }
    
    /**
    * deleterecordAction
    *
    * @package    AJAX
    * (Delete a record in the database)
    *
    * @link       /admin/deleterecord
    * @category   actions
    */
    public function deleterecordAction()
    {
        // Disable the layout and
        // the view's rendering
        $this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        // Get the POST request
        $request = $this->getRequest();

        // Read all values into the POST request
        $typeOfRecord = $request->getPost('type', null);
        $primaryKey = $request->getPost('id', null);

        // Create and set the result variable to 0
        // (if the action goes well it will update to 1)
        $result = 0;
        // Create and set the message variable
        // (if the action goes well it will update)
        $message = 'Record NON cancellato!';
        
        // Using the typeOfRecord variable
        // to initialize the right model
        if ($typeOfRecord != null) {
            switch ($typeOfRecord) {
                case 'faq':
                    $model = new Application_Model_Faq();
                    break;
                case 'booking':
                    $model = new Application_Model_Booking();
                    break;
                case 'service':
                    $model = new Application_Model_Service();
                    break;
                case 'department':
                    $model = new Application_Model_Department();
                    break;
                case 'staff':
                    $model = new Application_Model_Staff();
                    break;
                case 'user':
                    $model = new Application_Model_User();
                    break;
            }
        }
        
        // Check the validity of the values
        if ($primaryKey != null && $typeOfRecord != null) {
            // Delete the record in the database
            if ($model->delete(intval($primaryKey))) {
                $message = 'Record cancellato correttamente!';
                $result = 1;
            }
        }
        
        // Check if the request come
        // from an AJAX function
        if ($request->isXmlHttpRequest()) {
            $this->getResponse()->setHeader('Content-type', 'application/json')->setBody('{ "result": ' . $result . ', "message": "' . $message . '" }');
        } else {
            return $this->_redirect('admin');
        }
        
    }
    
    /**
    * getrecordAction
    *
    * @package    AJAX
    * (Get the values of a record)
    *
    * @link       /admin/getrecord
    * @category   actions
    */
    public function getrecordAction()
    {
        // Disable the layout and
        // the view's rendering
        $this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        
        // Get the POST request
        $request = $this->getRequest();

        // Read all values into the POST request
        $typeOfRecord = $request->getPost('type', null);
        $primaryKey = $request->getPost('id', null);
        
        // Create and set the result variable to 0
        // (if the action goes well it will update to 1)
        $result = 0;
        // Create and set the value variable
        // (if the action goes well it will update)
        $value = null;
        
        // Using the typeOfRecord variable
        // to initialize the right model
        if ($typeOfRecord != null) {
            switch ($typeOfRecord) {
                case 'faq':
                    $model = new Application_Model_Faq();
                    break;
                case 'booking':
                    $model = new Application_Model_Booking();
                    break;
                case 'service':
                    $model = new Application_Model_Service();
                    break;
                case 'department':
                    $model = new Application_Model_Department();
                    break;
                case 'staff':
                    $model = new Application_Model_Staff();
                    break;
                case 'user':
                    $model = new Application_Model_User();
                    break;
            }
        }
        
        // Check the validity of the values
        if ($primaryKey != null && $typeOfRecord != null) {
            // If the record to get is service
            if ($typeOfRecord == 'service') {
                // Initiaize the department and staff model
                $department = new Application_Model_Department();
                $staff = new Application_Model_Staff();
                // Get the fields by the primary key
                $value = $this->extractResult($model->find($primaryKey));
                // Get the departments list and push it into array
                $value['department_list'] = $this->extractResult($department->get());
                // Get the staff list and push it into array
                $value['staff_list'] = $this->extractResult($staff->get());
            } else {
                // If the record to get is NOT service
                // Get the fields by the primary key
                $value = $this->extractResult($model->find($primaryKey));
            }

            $result = 1;
        }
        
        // Check if the request come
        // from an AJAX function
        if ($request->isXmlHttpRequest()) {
            $this->getResponse()->setHeader('Content-type', 'application/json')->setBody('{ "result": ' . $result . ', "message": ' . json_encode($value) . ', "record": "' . $typeOfRecord . '" }');
        } else {
            return $this->_redirect('admin');
        }
        
    }
    
    /**
    * updaterecordAction
    *
    * @package    AJAX
    * (Update the values of a record)
    *
    * @link       /admin/updaterecord
    * @category   actions
    */
    public function updaterecordAction()
    {
        // Disable the layout and
        // the view's rendering
        $this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        
        // Get the POST request
        $request = $this->getRequest();

        // Read all values into the POST request
        $typeOfRecord = $request->getPost('type', null);
        $newData = $request->getPost('jsonData', null);
        $newDataId = $request->getPost('id', null);

        // Create and set the result variable to 0
        // (if the action goes well it will update to 1)
        $result = 0;
        // Create and set the message variable
        // (if the action goes well it will update)
        $message = 'Record NON aggiornato! Riprova.';
        
        // Using the typeOfRecord variable
        // to initialize the right model
        if ($typeOfRecord != null) {
            switch ($typeOfRecord) {
                case 'faq':
                    $model = new Application_Model_Faq();
                    break;
                case 'booking':
                    $model = new Application_Model_Booking();
                    break;
                case 'service':
                    $model = new Application_Model_Service();
                    break;
                case 'department':
                    $model = new Application_Model_Department();
                    break;
                case 'staff':
                    $model = new Application_Model_Staff();
                    break;
                case 'user':
                    $model = new Application_Model_User();
                    break;
            }
        }
        
        // Check the validity of the values
        if ($newData != null && $newDataId != null) {
            
            // If the record to update is a booking
            if ($typeOfRecord == 'booking') {
                // Initialize service model
                $service = new Application_Model_Service();
                // Check if the service is open in the booking's hour
                if ($service->checkServiceOpen($newData['service'], $newData['date'])) {
                    // Check if the user haven't other reservation
                    if ($model->checkUserReservation($newData['user'], $newData['date'])) {
                        // Check if the service haven't other reservation
                        if ($model->checkServiceReservation($newData['service'], $newData['date'])) {
                            // Update the record in the database
                            if ($model->update($newData, $newDataId) == 1) {
                                $result = 1;
                                $message = 'Record aggiornato correttamente!';
                            }
                        } else {
                            $message = '<strong>ATTENZIONE!</strong> Data <u>non disponibile</u> perchè già prenotata da un altro utente.';
                        }
                    } else {
                        $message = '<strong>ATTENZIONE!</strong> L\'utente ha già un altro appuntamento per quest\'ora.';
                    }
                } else {
                    $message = '<strong>ATTENZIONE!</strong> Questo orario non è disponibile a prenotazioni.';
                }
            } else {
                try {
                    // Update the record in the database
                    if ($model->update($newData, $newDataId) == 1) {
                        $result = 1;
                        $message = 'Record aggiornato correttamente!';
                    }
                }
                catch (Exception $e) {
                    $message = 'Username o email già utilizzata!';
                }
            }
        }
        
        // Check if the request come
        // from an AJAX function
        if ($request->isXmlHttpRequest()) {
            $this->getResponse()->setHeader('Content-type', 'application/json')->setBody('{ "result": ' . $result . ', "message": "' . $message . '" }');
        } else {
            return $this->_redirect('admin');
        }
        
    }
    
    /**
    * logoutAction
    *
    * @link       /admin/logout
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

