<?php

class Application_Model_DbTable_User extends Zend_Db_Table_Abstract
{

    protected $_name = 'user';
    protected $_primary = 'user_id';

	/**
	* Get the name of primary key.
	*/
    public function getPrimary()
    {
    	return $this->_primary;
    }

}

