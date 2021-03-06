<?php

/**
* Application_Model_User
* 
* This is the model class
* of the user.
*
* @author     Gianluca Bonifazi
* @category   models 
* @copyright  Univpm (c) 2018
*/

class Application_Model_User
{
    private $_dbTable;
    private $_primaryKey;
    
    public function __construct()
    {
        $this->_dbTable = new Application_Model_DbTable_User();
        $this->_primaryKey = $this->_dbTable->getPrimary();
    }
    
    /**
    * Fetches all users.
    */
    public function get()
    {
        $select = $this->_dbTable->select()->from('user', array(
            'user_id',
            'username',
            'role',
            'name',
            'surname',
            'code',
            'email'
        ))->where("role = 'user'");

        // return: The row results of the Zend_Db_Adapter fetch mode.
        return $this->_dbTable->fetchAll($select);
    }
    
    /**
    * Fetches users by primary key. The argument specifies
    * one or more primary key value(s). To find multiple
    * rows by primary key, the argument must be an array.
    */
    public function find($id)
    {
        // return: Row(s) matching the criteria.
        return $this->_dbTable->find($id);
    }
    
    /**
    * Inserts a new user.
    */
    public function create($array)
    {
        // return: The primary key of the row inserted.
        return $this->_dbTable->insert($array);
    }
    
    /**
    * Updates existing user.
    */
    public function update($array, $id)
    {
        // return: The number of rows updated.
        return $this->_dbTable->update($array, "$this->_primaryKey = $id");
    }
    
    /**
    * Deletes existing user.
    */
    public function delete($id)
    {
        // return: The number of rows deleted.
        return $this->_dbTable->delete("$this->_primaryKey = $id");
    }
    
    /**
    * extractBooking
    *
    * Get the appointments
    * of the user using the id
    * and ">" or "<" of date today.
    *
    * @return     date|booking_id|department|service|name|surname
    * @category   query
    */
    public function extractBooking($user_id, $when)
    {
        $dateTime = new DateTime();
        $dateTime->setTimezone(new DateTimeZone('Europe/Rome'));
        $dateNow = $dateTime->format("Y-m-d H:i");
        
        $select = $this->_dbTable->select()->from(array(
            'b' => 'booking'
        ), array(
            'b.date',
            'b.booking_id'
        ))->joinInner(array(
            's' => 'service'
        ), 'b.service = s.service_id', array(
            'service' => 's.name'
        ))->joinInner(array(
            'd' => 'department'
        ), 's.department = d.department_id', array(
            'department' => 'd.name'
        ))->joinInner(array(
            'u' => 'user'
        ), 's.staff = u.user_id', array(
            'u.name',
            'u.surname'
        ))->where('b.user = ?', $user_id)->where('b.date ' . $when . ' ?', $dateNow)->order('b.date ASC')->setIntegrityCheck(false);
        
        return $this->_dbTable->fetchAll($select);
    }
    
    /**
    * checkPassword
    *
    * Check if the passwortd
    * is correct.
    *
    * @return     Boolean
    * @category   query
    */
    public function checkPassword($user_id, $password)
    {
        $result = 0;
        $select = $this->_dbTable->select()->from('user', array(
            'password'
        ))->where('password = "' . hash('SHA256', $password) . '"')->where('user_id = ' . $user_id);

        $row = $this->_dbTable->fetchAll($select);

        foreach ($row->toArray() as $key => $value) {
            $result = 1;
        }
        
        return $result;
    }
}

