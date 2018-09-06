<?php

class ServiceController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
		$id = $this->_getParam('id', null);
        $service = new Application_Model_Service();
        $staff = new Application_Model_Staff();
        $department = new Application_Model_Department();

    	$serviceDetails = $service->find($id);

    	$this->view->assign(
    		'service',
    		$this->extractResult($serviceDetails)
    	);
        $this->view->assign(
            'staff',
            $this->extractResult($staff->find($serviceDetails[0]['staff']))
        );
        $this->view->assign(
            'department',
            $this->extractResult($department->find($serviceDetails[0]['department']))
        );
    }

    public function bookingAction()
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

                    // Insert new reservation into database
                    $newReservation = $booking->create(array(
                        'user'      => $userId,
                        'service'   => $serviceId,
                        'date'      => $date
                    ));

                    if ($newReservation) {
                        $message = 'Prenotazione confermata!<br>Data: <strong>'.$date.'</strong>';
                        $result = 1;
                    } else {
                        $message = '<strong>ATTENZIONE!</strong> E\' stato riscontrato un errore nella prenotazione! Riprovare.';
                    }
                } else {
                    $message = '<strong>ATTENZIONE!</strong> Data <u>non disponibile</u> perchè già prenotata da un altro utente.';
                }
            } else {
                $message = '<strong>ATTENZIONE!</strong> Hai già un altro appuntamento per quest\'ora.';
            }
        } else {
            $message = '<strong>ATTENZIONE!</strong> Questo orario non è disponibile a prenotazioni.';
        }

        if ($request->isXmlHttpRequest()) { 
            $this->getResponse()->setHeader('Content-type','application/json')->setBody('{ "result": '.$result.', "message": "'.$message.'" }');
        } else {
            return $this->_redirect('department');
        }
    }

    // -----------------------------------
	/**
	* Clean an prettifier SQL query result
	*/
    public function extractResult($result)
    {
    	$data = [];
    	$rowsetArray = $result->toArray();
		foreach ($rowsetArray as $column => $value) {
			$data[$column] = $value;
		}
		return $data;
    }

}

