<?php

class Application_Model_Register
{

	private $_dbTable;
	private $_primaryKey;

	public function __construct()
	{
		$this->_dbTable = new Application_Model_DbTable_User();
		$this->_primaryKey = $this->_dbTable->getPrimary();
	}

	/**
	* Inserts a new user into database.
	*/
	public function create($array)
	{
		// return: The primary key of the row inserted.
		return $this->_dbTable->insert($array);
	}
}

