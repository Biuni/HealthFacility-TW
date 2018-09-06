<?php

class Application_Model_DbTable_Booking extends Zend_Db_Table_Abstract
{

    protected $_name = 'booking';
    protected $_primary = 'booking_id';

	/**
	* Get the name of primary key.
	*/
    public function getPrimary()
    {
    	return $this->_primary;
    }

}

