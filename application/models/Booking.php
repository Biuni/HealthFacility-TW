<?php

class Application_Model_Booking
{
	private $_dbTable;
	private $_primaryKey;

	public function __construct()
	{
		$this->_dbTable = new Application_Model_DbTable_Booking();
		$this->_primaryKey = $this->_dbTable->getPrimary();
	}

	/**
	* Fetches all reservation.
	*/
	public function get($param)
	{
		// return: The row results of the Zend_Db_Adapter fetch mode.
		return $this->_dbTable->fetchAll($param);
	}

	/**
	* Fetches reservations by primary key. The argument specifies
	* one or more primary key value(s). To find multiple
	* rows by primary key, the argument must be an array.
	*/
	public function find($id)
	{
		// return: Row(s) matching the criteria.
		return $this->_dbTable->find($id);
	}

	/**
	* Inserts a new reservation.
	*/
	public function create($array)
	{
		// return: The primary key of the row inserted.
		return $this->_dbTable->insert($array);
	}

	/**
	* Updates existing reservation.
	*/
	public function update($array, $id)
	{
		// return: The number of rows updated.
		return $this->_dbTable->update($array, "$this->_primaryKey = $id");
	}

	/**
	* Deletes existing reservation.
	*/
	public function delete($id)
	{
		// return: The number of rows deleted.
		return $this->_dbTable->delete("$this->_primaryKey = $id");
	}


	// -----------------------------
	// Custom method for this model
	// -----------------------------

	/**
	* Booking a new service
	*/
	public function checkUserReservation($user_id, $date)
	{
		$result = 1;
		$checkUser = $this->_dbTable->select()
									->from('booking', array('date'))
									->where('user = ?', $user_id);
		$userReservation = $this->_dbTable->fetchAll($checkUser);

    	$rowsetArray = $userReservation->toArray();
		foreach ($rowsetArray as $key => $value) {
			if ($value['date'] === $date) {
				$result = 0;
			}
		}

		return $result;
	}

	/**
	* Booking a new service
	*/
	public function checkServiceReservation($service_id, $date)
	{
		$result = 1;
		$checkService = $this->_dbTable->select()
										->from('booking', array('date'))
										->where('service = ?', $service_id);
		$serviceReservation = $this->_dbTable->fetchAll($checkService);

    	$rowsetArray = $serviceReservation->toArray();
		foreach ($rowsetArray as $key => $value) {
			if ($value['date'] === $date) {
				$result = 0;
			}
		}

		return $result;
	}

	/**
	* Booking a new service
	*/
	public function getAllReservations()
	{
		$select = $this->_dbTable->select()
				->from(array('b' => 'booking'), array('b.date', 'b.booking_id'))
				->joinInner(array('s' => 'service'), 's.service_id = b.service', array('service' => 's.name'))
				->joinInner(array('u' => 'user'), 'b.user = u.user_id', array('u.name','u.surname'))
				->setIntegrityCheck(false);

		// return: The row results of the Zend_Db_Adapter fetch mode.
        return $this->_dbTable->fetchAll($select);
	}

	public function countPerService($service_id)
	{
		$countRows = $this->_dbTable->select()->from('booking', array('rows' => 'COUNT(*)'))->where('service = '.$service_id.'');
		return $this->_dbTable->fetchRow($countRows);
	}

	public function countPerDepartment($department_id)
	{
		$select = $this->_dbTable->select()
				->from(array('b' => 'booking'), array('rows' => 'COUNT(*)', 'b.booking_id'))
				->joinInner(array('s' => 'service'), 's.service_id = b.service', array('s.service_id'))
				->joinInner(array('d' => 'department'), 'd.department_id = s.department', array('d.department_id'))
				->group(array('d.department_id'))
				->where('s.department = ?', $department_id)
				->setIntegrityCheck(false);

		return $this->_dbTable->fetchRow($select);
	}

}

