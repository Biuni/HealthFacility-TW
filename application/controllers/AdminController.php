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
        $booking = new Application_Model_Booking();
        $service = new Application_Model_Service();
        $department = new Application_Model_Department();

        $request = $this->getRequest();
        $dateStart = $request->getPost('dateStart', null);
        $dateEnd   = $request->getPost('dateEnd', null);
        if ($dateStart != null || $dateEnd != null) {
            $this->view->assign('dateStart', $dateStart);
            $this->view->assign('dateEnd', $dateEnd);
        }
        
        // **************
        // * GRAPH DATA *
        // **************

        // ----------------------------
        // Number of bookings per month
        // ----------------------------
        $allReservations = $this->extractResult($booking->getAllReservations($dateStart, $dateEnd));
        $labels = array('Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre');
        $jan = $feb = $mar = $apr = $may = $jun = $jul = $aug = $sept = $oct = $nov = $dec = 0;
        foreach ($allReservations as $value) {
            $monthNum = date('n', strtotime($value['date']));
            // January
            if ($monthNum - 1 == 0) { $jan++; }
            // February
            if ($monthNum - 1 == 1) { $feb++; }
            // March
            if ($monthNum - 1 == 2) { $mar++; }
            // April
            if ($monthNum - 1 == 3) { $apr++; }
            // May
            if ($monthNum - 1 == 4) { $may++; }
            // June
            if ($monthNum - 1 == 5) { $jun++; }
            // July
            if ($monthNum - 1 == 6) { $jul++; }
            // August
            if ($monthNum - 1 == 7) { $aug++; }
            // September
            if ($monthNum - 1 == 8) { $sept++; }
            // October
            if ($monthNum - 1 == 9) { $oct++; }
            // November
            if ($monthNum - 1 == 10) { $nov++; }
            // December
            if ($monthNum - 1 == 11) { $dec++; }
        }
        $this->view->assign(
            'dataOfBookingPerMonth',
            array($jan, $feb, $mar, $apr, $may, $jun, $jul, $aug, $sept, $oct, $nov, $dec)
        );
        $this->view->assign(
            'labelsOfBookingPerMonth',
            $labels
        );

        // ------------------------------
        // Number of bookings per service
        // ------------------------------
        $allServices = $this->extractResult($service->get());
        $labels2 = array();
        $serviceCount = array();
        foreach ($allServices as $value) {
            $labels2[] = $value['name'];
            $getNumOfService = $this->extractResult($booking->countPerService($value['service_id'], $dateStart, $dateEnd));
            $serviceCount[] = $getNumOfService['rows'];
        }

        $this->view->assign(
            'dataOfBookingPerService',
            $serviceCount
        );
        $this->view->assign(
            'labelsOfBookingPerService',
            $labels2
        );

        // ---------------------------------
        // Number of bookings per department
        // ---------------------------------
        $allDepartment = $this->extractResult($department->get());
        $labels3 = array();
        $departmentCount = array();
        foreach ($allDepartment as $value) {
            $labels3[] = $value['name'];
            $countPerDepartment = $booking->countPerDepartment($value['department_id'], $dateStart, $dateEnd);
            if ($countPerDepartment['rows'] == null) {
                $departmentCount[] = 0;
            } else {
                $departmentCount[] = $countPerDepartment['rows'];
            }
        }

        $this->view->assign(
            'dataOfBookingPerDepartment',
            $departmentCount
        );
        $this->view->assign(
            'labelsOfBookingPerDepartment',
            $labels3
        );

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
        $request = $this->getRequest();
        $staff = new Application_Model_Staff();

        // -----------------------------------
        $username = $request->getPost('username');
        $password = $request->getPost('password');
        $name = $request->getPost('name');
        $surname = $request->getPost('surname');
        $code = $request->getPost('code');
        $email = $request->getPost('email');
        // -----------------------------------

        if ($username != null && $username != '' &&
            $password != null && $password != '' &&
            $name     != null && $name     != '' &&
            $surname  != null && $surname  != '' &&
            $code     != null && $code     != '' &&
            $email    != null && $email    != '') {
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
            } catch(Exception $e){
                return $this->_redirect('admin/insertstaff?err=1');
            }
        }
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
        $request = $this->getRequest();
        $department = new Application_Model_Department();

        // -----------------------------------
        $name = $request->getPost('name');
        $description = $request->getPost('description');
        $place = $request->getPost('place');
        // -----------------------------------

        if ($description != null && $description != '' &&
            $name        != null && $name        != '' &&
            $place       != null && $place       != '') {
            $department->create(array(
                'name' => $name,
                'description' => $description,
                'place' => $place
            ));
            return $this->_redirect('admin/insertdepartment?ok=1');
        }
    }

    // List all services and update or delete it
    public function serviceAction()
    {
        $service = new Application_Model_Service();
        $this->view->assign(
            'allService',
            $this->extractResult($service->getServiceInfo())
        );
    }

    // Insert a new service into database
    public function insertserviceAction()
    {
        $request = $this->getRequest();
        $service = new Application_Model_Service();

        // -----------------------------------
        $name = $request->getPost('name');
        $department = $request->getPost('department');
        $description = $request->getPost('description');
        $prescriptions = $request->getPost('prescriptions');
        $staff = $request->getPost('staff');
        // -----------------------------------

        $schedule = array('schedule' => array(
            array(
                'day' => 'Domenica',
                'm_opening' => ($request->getPost('m_opening-0') == '') ? null : $request->getPost('m_opening-0'),
                'm_closing' => ($request->getPost('m_closing-0') == '') ? null : $request->getPost('m_closing-0'),
                'a_opening' => ($request->getPost('a_opening-0') == '') ? null : $request->getPost('a_opening-0'),
                'a_closing' => ($request->getPost('a_closing-0') == '') ? null : $request->getPost('a_closing-0'),
            ),
            array(
                'day' => 'Lunedì',
                'm_opening' => ($request->getPost('m_opening-1') == '') ? null : $request->getPost('m_opening-1'),
                'm_closing' => ($request->getPost('m_closing-1') == '') ? null : $request->getPost('m_closing-1'),
                'a_opening' => ($request->getPost('a_opening-1') == '') ? null : $request->getPost('a_opening-1'),
                'a_closing' => ($request->getPost('a_closing-1') == '') ? null : $request->getPost('a_closing-1'),
            ),
            array(
                'day' => 'Martedì',
                'm_opening' => ($request->getPost('m_opening-2') == '') ? null : $request->getPost('m_opening-2'),
                'm_closing' => ($request->getPost('m_closing-2') == '') ? null : $request->getPost('m_closing-2'),
                'a_opening' => ($request->getPost('a_opening-2') == '') ? null : $request->getPost('a_opening-2'),
                'a_closing' => ($request->getPost('a_closing-2') == '') ? null : $request->getPost('a_closing-2'),
            ),
            array(
                'day' => 'Mercoledì',
                'm_opening' => ($request->getPost('m_opening-3') == '') ? null : $request->getPost('m_opening-3'),
                'm_closing' => ($request->getPost('m_closing-3') == '') ? null : $request->getPost('m_closing-3'),
                'a_opening' => ($request->getPost('a_opening-3') == '') ? null : $request->getPost('a_opening-3'),
                'a_closing' => ($request->getPost('a_closing-3') == '') ? null : $request->getPost('a_closing-3'),
            ),
            array(
                'day' => 'Giovedì',
                'm_opening' => ($request->getPost('m_opening-4') == '') ? null : $request->getPost('m_opening-4'),
                'm_closing' => ($request->getPost('m_closing-4') == '') ? null : $request->getPost('m_closing-4'),
                'a_opening' => ($request->getPost('a_opening-4') == '') ? null : $request->getPost('a_opening-4'),
                'a_closing' => ($request->getPost('a_closing-4') == '') ? null : $request->getPost('a_closing-4'),
            ),
            array(
                'day' => 'Venerdì',
                'm_opening' => ($request->getPost('m_opening-5') == '') ? null : $request->getPost('m_opening-5'),
                'm_closing' => ($request->getPost('m_closing-5') == '') ? null : $request->getPost('m_closing-5'),
                'a_opening' => ($request->getPost('a_opening-5') == '') ? null : $request->getPost('a_opening-5'),
                'a_closing' => ($request->getPost('a_closing-5') == '') ? null : $request->getPost('a_closing-5'),
            ),
            array(
                'day' => 'Sabato',
                'm_opening' => ($request->getPost('m_opening-6') == '') ? null : $request->getPost('m_opening-6'),
                'm_closing' => ($request->getPost('m_closing-6') == '') ? null : $request->getPost('m_closing-6'),
                'a_opening' => ($request->getPost('a_opening-6') == '') ? null : $request->getPost('a_opening-6'),
                'a_closing' => ($request->getPost('a_closing-6') == '') ? null : $request->getPost('a_closing-6'),
            )
        ));

        if ($description != null && $description != '' &&
            $name        != null && $name        != '' &&
            $staff       != null && $staff       != '' &&
            $department  != null && $department  != '') {
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

        $department = new Application_Model_Department();
        $this->view->assign(
            'allDepartment',
            $this->extractResult($department->get())
        );
        $staff = new Application_Model_Staff();
        $this->view->assign(
            'allStaff',
            $this->extractResult($staff->get())
        );
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
        $request = $this->getRequest();
        $booking = new Application_Model_Booking();

        // -----------------------------------
        $user = $request->getPost('user');
        $service = $request->getPost('service');
        $date = $request->getPost('date');
        // -----------------------------------

        if ($service != null && $service != '' &&
            $user    != null && $user    != '' &&
            $date    != null && $date    != '') {
            $booking->create(array(
                'user' => $user,
                'service' => $service,
                'date' => $date
            ));
            return $this->_redirect('admin/insertbooking?ok=1');
        }

        $user = new Application_Model_User();
        $this->view->assign(
            'allUser',
            $this->extractResult($user->get())
        );
        $service = new Application_Model_Service();
        $this->view->assign(
            'allService',
            $this->extractResult($service->get())
        );
    }

    // Check the date by ajax
    public function insertbookingadminAction()
    {
        $request = $this->getRequest();
        $this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $booking = new Application_Model_Booking();
        $service = new Application_Model_Service();
        $result = 0;

        // --------------------------------
            $userId = $request->getPost('userId');
            $serviceId = $request->getPost('seriviceId');
            $date = $request->getPost('date');
        // --------------------------------

        // Check if the service is open in the booking's hour
        if ($service->checkServiceOpen($serviceId, $date)) {
            // Check if the user haven't other reservation
            if ($booking->checkUserReservation($userId, $date)) {
                // Check if the service haven't other reservation
                if ($booking->checkServiceReservation($serviceId, $date)) {
                    $message = 'La data: <strong>'.$date.'</strong> è disponibile!';
                    $result = 1;
                } else {
                    $message = '<strong>ATTENZIONE!</strong> La data <strong>'.$date.'</strong> <u>non è disponibile</u> perchè già prenotata da un altro utente.';
                }
            } else {
                $message = '<strong>ATTENZIONE!</strong> L\'utente già un altro appuntamento per la data <strong>'.$date.'</strong>.';
            }
        } else {
            $message = '<strong>ATTENZIONE!</strong> Questo orario <strong>'.$date.'</strong> non è disponibile a prenotazioni.';
        }

        if ($request->isXmlHttpRequest()) { 
            $this->getResponse()->setHeader('Content-type','application/json')->setBody('{ "result": '.$result.', "message": "'.$message.'" }');
        } else {
            return $this->_redirect('department');
        }
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

    // Delete the record choosed
    public function deleterecordAction()
    {
        $request = $this->getRequest();
        $this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $result = 0;
        $message = 'Record NON cancellato!';

        // --------------------------------
            $typeOfRecord = $request->getPost('type', null);
            $primaryKey = $request->getPost('id', null);
        // --------------------------------

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

        if ($primaryKey   != null &&
            $typeOfRecord != null) {

            if ($model->delete(intval($primaryKey))) {
                $message = 'Record cancellato correttamente!';
                $result = 1;
            }

        }

        if ($request->isXmlHttpRequest()) { 
            $this->getResponse()->setHeader('Content-type','application/json')->setBody('{ "result": '.$result.', "message": "'.$message.'" }');
        } else {
            return $this->_redirect('admin');
        }

    }

    // Get the record to modify
    public function getrecordAction()
    {
        $request = $this->getRequest();
        $this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $result = 0;
        $value = null;

        // --------------------------------
            $typeOfRecord = $request->getPost('type', null);
            $primaryKey = $request->getPost('id', null);
        // --------------------------------

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

        if ($primaryKey   != null &&
            $typeOfRecord != null) {
            $result = 1;
            if ($typeOfRecord == 'service') {
                $value = $this->extractResult($model->find($primaryKey));
                $department = new Application_Model_Department();
                $staff = new Application_Model_Staff();
                $value['department_list'] = $this->extractResult($department->get());
                $value['staff_list'] = $this->extractResult($staff->get());
            } else {
                $value = $this->extractResult($model->find($primaryKey));
            }
        }

        if ($request->isXmlHttpRequest()) { 
            $this->getResponse()->setHeader('Content-type','application/json')->setBody('{ "result": '.$result.', "message": '.json_encode($value).', "record": "'.$typeOfRecord.'" }');
        } else {
            return $this->_redirect('admin');
        }

    }

    // Get the record to modify
    public function updaterecordAction()
    {
        $request = $this->getRequest();
        $this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $result = 0;
        $message = 'Record NON aggiornato! Riprova.';

        // --------------------------------
            $typeOfRecord = $request->getPost('type', null);
            $newData = $request->getPost('jsonData', null);
            $newDataId = $request->getPost('id', null);
        // --------------------------------

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

        if ($newData != null && $newDataId != null) {

            if ($typeOfRecord == 'booking') {
                $service = new Application_Model_Service();
                // Check if the service is open in the booking's hour
                if ($service->checkServiceOpen($newData['service'], $newData['date'])) {
                    // Check if the user haven't other reservation
                    if ($model->checkUserReservation($newData['user'], $newData['date'])) {
                        // Check if the service haven't other reservation
                        if ($model->checkServiceReservation($newData['service'], $newData['date'])) {
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
                    if ($model->update($newData, $newDataId) == 1) {
                        $result = 1;
                        $message = 'Record aggiornato correttamente!';
                    }
                } catch(Exception $e){
                    $message = 'Username o email già utilizzata!';
                }
            }
        }

        if ($request->isXmlHttpRequest()) { 
            $this->getResponse()->setHeader('Content-type','application/json')->setBody('{ "result": '.$result.', "message": "'.$message.'" }');
        } else {
            return $this->_redirect('admin');
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
        $data = array();
        $rowsetArray = $result->toArray();
        foreach ($rowsetArray as $column => $value) {
            $data[$column] = $value;
        }
        return $data;
    }

}

