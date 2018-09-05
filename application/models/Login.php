<?php

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
	* Get the user information by username
	*/
	public function getUserByName($username)
	{
		// return: The row of matched username
		return $this->_dbTable
					->fetchRow(
						$this->_dbTable
						->select()
						->where('username = ?', $username)
					);
	}
}

