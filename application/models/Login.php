<?php

/**
* Application_Model_Login
* 
* This is the model class
* of the login.
*
* @author     Gianluca Bonifazi
* @category   models 
* @copyright  Univpm (c) 2018
*/

class Application_Model_Login
{
    private $_dbTable;
    private $_primaryKey;
    
    public function __construct()
    {
        $this->_dbTable = new Application_Model_DbTable_User();
        $this->_primaryKey = $this->_dbTable->getPrimary();
    }
    
    /**
    * paginator
    *
    * Get the user information
    * by username.
    *
    * @return     The row of matched username
    * @category   query
    */
    public function getUserByName($username)
    {
        return $this->_dbTable->fetchRow($this->_dbTable->select()->where('username = ?', $username));
    }
}

