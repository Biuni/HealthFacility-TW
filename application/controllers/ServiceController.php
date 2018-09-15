<?php

/**
* ServiceController
* 
* This is the controller class
* of the service page.
*
* @author     Gianluca Bonifazi
* @category   controllers 
* @copyright  Univpm (c) 2018
*/

class ServiceController extends Zend_Controller_Action
{
    /**
    * Initialize action controller
    */
    public function init()
    {}
    
    /**
    * indexAction
    *
    * @link       /service/index/id/:id
    * @category   actions
    */
    public function indexAction()
    {
        // Initializes the models useful for the action
        $service = new Application_Model_Service();
        $staff = new Application_Model_Staff();
        $department = new Application_Model_Department();
        
        // GET the id parameter
        $id = $this->_getParam('id', null);

        // Fetch de service information by id
        $serviceDetails = $service->find($id);
        
        // Assign the view's variable used
        // to print the query results
        $this->view->assign('service', $this->extractResult($serviceDetails));
        $this->view->assign('staff', $this->extractResult($staff->find($serviceDetails[0]['staff'])));
        $this->view->assign('department', $this->extractResult($department->find($serviceDetails[0]['department'])));
    }

    /**
    * bookingAction
    *
    * @package    AJAX
    * (Check booking availability and insert)
    *
    * @link       /service/booking
    * @category   actions
    */
    public function bookingAction()
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
                    
                    // Insert new reservation into database
                    $newReservation = $booking->create(array(
                        'user' => $userId,
                        'service' => $serviceId,
                        'date' => $date
                    ));
                    
                    if ($newReservation) {
                        $message = 'Prenotazione confermata!<br>Data: <strong>' . $date . '</strong>';
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
        
        // Check if the request come
        // from an AJAX function
        if ($request->isXmlHttpRequest()) {
            $this->getResponse()->setHeader('Content-type', 'application/json')->setBody('{ "result": ' . $result . ', "message": "' . $message . '" }');
        } else {
            return $this->_redirect('department');
        }
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

