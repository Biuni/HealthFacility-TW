<?php

/**
* Application_Model_Staff
* 
* This is the model class
* of the staff.
*
* @author     Gianluca Bonifazi
* @category   models 
* @copyright  Univpm (c) 2018
*/

class Application_Model_Staff
{
    private $_dbTable;
    private $_primaryKey;
    
    public function __construct()
    {
        $this->_dbTable = new Application_Model_DbTable_User();
        $this->_primaryKey = $this->_dbTable->getPrimary();
    }
    
    /**
    * Fetches all people on the staff.
    */
    public function get()
    {
        $select = $this->_dbTable->select()->from('user', array(
            'user_id',
            'username',
            'name',
            'surname',
            'code',
            'email',
            'role'
        ))->where("role = 'staff'");

        // return: The row results of the Zend_Db_Adapter fetch mode.
        return $this->_dbTable->fetchAll($select);
    }
    
    /**
    * Fetches staff by primary key. The argument specifies
    * one or more primary key value(s). To find multiple
    * rows by primary key, the argument must be an array.
    */
    public function find($id)
    {
        // return: Row(s) matching the criteria.
        return $this->_dbTable->find($id);
    }
    
    /**
    * Inserts a new staff.
    */
    public function create($array)
    {
        // return: The primary key of the row inserted.
        return $this->_dbTable->insert($array);
    }
    
    /**
    * Updates existing staff.
    */
    public function update($array, $id)
    {
        // return: The number of rows updated.
        return $this->_dbTable->update($array, "$this->_primaryKey = $id");
    }
    
    /**
    * Deletes existing staff.
    */
    public function delete($id)
    {
        // return: The number of rows deleted.
        return $this->_dbTable->delete("$this->_primaryKey = $id");
    }
    
    /**
    * getAppointments
    *
    * Get the appointments
    * of the staff using id
    *
    * @return     date|service|staff|service_id
    * @category   query
    */
    public function getAppointments($staff_id)
    {
        $select = $this->_dbTable->select()->from(array(
            'b' => 'booking'
        ), array(
            'b.date',
            'b.service'
        ))->joinInner(array(
            's' => 'service'
        ), 'b.service = s.service_id', array(
            's.staff',
            's.service_id'
        ))->where('s.staff = ?', $staff_id)->setIntegrityCheck(false);
        
        return $this->_dbTable->fetchAll($select);
    }
    
    /**
    * getAppointmentsToday
    *
    * Get the appointments
    * of the staff using id
    * and using a date.
    *
    * @return     date|booking_id|name|service_id
    *			  patient_name|patient_surname|user_id
    * @category   query
    */
    public function getAppointmentsToday($staff_id, $data)
    {
        $select = $this->_dbTable->select()->from(array(
            'b' => 'booking'
        ), array(
            'b.date',
            'b.booking_id'
        ))->joinInner(array(
            's' => 'service'
        ), 'b.service = s.service_id', array(
            's.name',
            's.service_id'
        ))->joinInner(array(
            'u' => 'user'
        ), 'b.user = u.user_id', array(
            'patient_name' => 'u.name',
            'patient_surname' => 'u.surname',
            'u.user_id'
        ))->where('s.staff = ?', $staff_id)->where('b.date >= ?', $data . ' 00:00')->where('b.date <= ?', $data . ' 23:59')->setIntegrityCheck(false);
        
        return $this->_dbTable->fetchAll($select);
    }
}

