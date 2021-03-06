<?php

/**
* Application_Model_Department
* 
* This is the model class
* of the department.
*
* @author     Gianluca Bonifazi
* @category   models 
* @copyright  Univpm (c) 2018
*/

class Application_Model_Department
{
    private $_dbTable;
    private $_primaryKey;
    
    public function __construct()
    {
        $this->_dbTable = new Application_Model_DbTable_Department();
        $this->_primaryKey = $this->_dbTable->getPrimary();
    }
    
    /**
    * Fetches all departments.
    */
    public function get()
    {
        // return: The row results of the Zend_Db_Adapter fetch mode.
        return $this->_dbTable->fetchAll();
    }
    
    /**
    * Fetches departments by primary key. The argument specifies
    * one or more primary key value(s). To find multiple
    * rows by primary key, the argument must be an array.
    */
    public function find($id)
    {
        // return: Row(s) matching the criteria.
        return $this->_dbTable->find($id);
    }
    
    /**
    * Inserts a new department.
    */
    public function create($array)
    {
        // return: The primary key of the row inserted.
        return $this->_dbTable->insert($array);
    }
    
    /**
    * Updates existing department.
    */
    public function update($array, $id)
    {
        // return: The number of rows updated.
        return $this->_dbTable->update($array, "$this->_primaryKey = $id");
    }
    
    /**
    * Deletes existing department.
    */
    public function delete($id)
    {
        // return: The number of rows deleted.
        return $this->_dbTable->delete("$this->_primaryKey = $id");
    }
    
    /**
    * paginator
    *
    * Get departments information
    * by page id.
    * Three departments per page.
    *
    * @return     The information of departments
    * @category   query
    */
    public function paginator($page)
    {
        $adapter = new Zend_Paginator_Adapter_DbSelect($this->_dbTable->select()->from('department'));
        $paginator = new Zend_Paginator($adapter);
        
        $paginator->setItemCountPerPage(3)->setCurrentPageNumber($page);
        
        return $paginator;
    }
    
    /**
    * countDepartments
    *
    * Count the number of departments.
    *
    * @return     rows
    * @category   query
    */
    public function countDepartments()
    {
        $countRows = $this->_dbTable->select()->from('department', array(
            'rows' => 'COUNT(*)'
        ));

        return $this->_dbTable->fetchRow($countRows);
    }
}

