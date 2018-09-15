<?php

/**
* DepartmentController
* 
* This is the controller class
* of the department page.
*
* @author     Gianluca Bonifazi
* @category   controllers 
* @copyright  Univpm (c) 2018
*/

class DepartmentController extends Zend_Controller_Action
{
    /**
    * Initialize action controller
    */
    public function init()
    {}
    
    /**
    * indexAction
    *
    * @link       /department
    * @link       /department/index/page/:id
    * @category   actions
    */
    public function indexAction()
    {
        // Initialize the department model
        $department = new Application_Model_Department();
        
        // Get the page parameter, if isn't
        // setted the variable value is 1
        $page = $this->_getParam('page', 1);
        // Get the departments using paginator
        $paginator = $department->paginator($page);
        // Count the number of departments
        $numberOfDepartments = $this->extractResult($department->countDepartments());
        
        // Assign the view's variables used
        // to print the results
        $this->view->assign('department', $paginator);
        $this->view->assign('totalRows', $numberOfDepartments['rows']);
        $this->view->assign('page', $page);
    }
    
    /**
    * detailsAction
    *
    * @link       department/details/id/:id
    * @category   actions
    */
    public function detailsAction()
    {
        // Initialize the department model
        $department = new Application_Model_Department();
        // Initialize the service model
        $service = new Application_Model_Service();

        // Get the id parameter, if isn't
        // setted the variable value is null
        $id = $this->_getParam('id', null);
        
        // Assign the view's variables with
        // department information and service list
        $this->view->assign('department', $this->extractResult($department->find($id)));
        $this->view->assign('service', $this->extractResult($service->selectByDepartment($id)));
        
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

