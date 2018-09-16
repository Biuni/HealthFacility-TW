<?php

/**
* Application_Model_Booking
* 
* This is the model class
* of the booking.
*
* @author     Gianluca Bonifazi
* @category   models 
* @copyright  Univpm (c) 2018
*/

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
    * Fetches all booking.
    */
    public function get($param)
    {
        // return: The row results of the Zend_Db_Adapter fetch mode.
        return $this->_dbTable->fetchAll($param);
    }
    
    /**
    * Fetches bookings by primary key. The argument specifies
    * one or more primary key value(s). To find multiple
    * rows by primary key, the argument must be an array.
    */
    public function find($id)
    {
        // return: Row(s) matching the criteria.
        return $this->_dbTable->find($id);
    }
    
    /**
    * Inserts a new booking.
    */
    public function create($array)
    {
        // return: The primary key of the row inserted.
        return $this->_dbTable->insert($array);
    }
    
    /**
    * Updates existing booking.
    */
    public function update($array, $id)
    {
        // return: The number of rows updated.
        return $this->_dbTable->update($array, "$this->_primaryKey = $id");
    }
    
    /**
    * Deletes existing booking.
    */
    public function delete($id)
    {
        // return: The number of rows deleted.
        return $this->_dbTable->delete("$this->_primaryKey = $id");
    }
    
    /**
    * checkUserReservation
    *
    * Check if the user does not
    * have another appointment on
    * the same date
    *
    * @return     Boolean
    * @category   query
    */
    public function checkUserReservation($user_id, $date)
    {
        $result = 1;
        $checkUser = $this->_dbTable->select()->from('booking', array(
            'date'
        ))->where('user = ?', $user_id);
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
    * checkServiceReservation
    *
    * Check if the service does not
    * have another appointment on
    * the same date.
    *
    * @return     Boolean
    * @category   query
    */
    public function checkServiceReservation($service_id, $date)
    {
        $result = 1;
        $checkService = $this->_dbTable->select()->from('booking', array(
            'date'
        ))->where('service = ?', $service_id);
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
    * getAllReservations
    *
    * Get bookings by time interval 
    * or by all times.
    *
    * @return     date|booking_id|service|name|surname
    * @category   query
    */
    public function getAllReservations($dateStart = null, $dateEnd = null)
    {
        if ($dateStart == null && $dateEnd == null) {
            $select = $this->_dbTable->select()->from(array(
                'b' => 'booking'
            ), array(
                'b.date',
                'b.booking_id'
            ))->joinInner(array(
                's' => 'service'
            ), 's.service_id = b.service', array(
                'service' => 's.name'
            ))->joinInner(array(
                'u' => 'user'
            ), 'b.user = u.user_id', array(
                'u.name',
                'u.surname'
            ))->setIntegrityCheck(false);
        } else {
            $select = $this->_dbTable->select()->from(array(
                'b' => 'booking'
            ), array(
                'b.date',
                'b.booking_id'
            ))->joinInner(array(
                's' => 'service'
            ), 's.service_id = b.service', array(
                'service' => 's.name'
            ))->joinInner(array(
                'u' => 'user'
            ), 'b.user = u.user_id', array(
                'u.name',
                'u.surname'
            ))->where('b.date >= ?', $dateStart)->where('b.date <= ?', $dateEnd)->setIntegrityCheck(false);
        }
        
        return $this->_dbTable->fetchAll($select);
    }
    
    /**
    * countPerService
    *
    * Count the number of booking
    * per service using a time interval
    * or by all times.
    *
    * @return     rows
    * @category   query
    */
    public function countPerService($service_id, $dateStart, $dateEnd)
    {
        if ($dateStart == null && $dateEnd == null) {
            $countRows = $this->_dbTable->select()->from('booking', array(
                'rows' => 'COUNT(*)'
            ))->where('service = ' . $service_id . '');
        } else {
            $countRows = $this->_dbTable->select()->from('booking', array(
                'rows' => 'COUNT(*)'
            ))->where('date >= ?', $dateStart)->where('date <= ?', $dateEnd)->where('service = ?', $service_id);
        }
        
        return $this->_dbTable->fetchRow($countRows);
    }
    
    /**
    * countPerDepartment
    *
    * Count the number of booking
    * per department using a time interval
    * or by all times.
    *
    * @return     rows|booking_id|service_id|department_id
    * @category   query
    */
    public function countPerDepartment($department_id, $dateStart, $dateEnd)
    {
        if ($dateStart == null && $dateEnd == null) {
            $select = $this->_dbTable->select()->from(array(
                'b' => 'booking'
            ), array(
                'rows' => 'COUNT(*)',
                'b.booking_id'
            ))->joinInner(array(
                's' => 'service'
            ), 's.service_id = b.service', array(
                's.service_id'
            ))->joinInner(array(
                'd' => 'department'
            ), 'd.department_id = s.department', array(
                'd.department_id'
            ))->group(array(
                'd.department_id'
            ))->where('s.department = ?', $department_id)->setIntegrityCheck(false);
        } else {
            $select = $this->_dbTable->select()->from(array(
                'b' => 'booking'
            ), array(
                'rows' => 'COUNT(*)',
                'b.booking_id'
            ))->joinInner(array(
                's' => 'service'
            ), 's.service_id = b.service', array(
                's.service_id'
            ))->joinInner(array(
                'd' => 'department'
            ), 'd.department_id = s.department', array(
                'd.department_id'
            ))->group(array(
                'd.department_id'
            ))->where('b.date >= ?', $dateStart)->where('b.date <= ?', $dateEnd)->where('s.department = ?', $department_id)->setIntegrityCheck(false);
        }
        
        return $this->_dbTable->fetchRow($select);
    }
    
    /**
    * getAllReservationsByUser
    *
    * Get all the reservations 
    * booked by an user.
    *
    * @return     booking_id|user|department|date
    * @category   query
    */
    public function getAllReservationsByUser($user_id)
    {
        return $this->_dbTable->fetchAll('user = ' . $user_id);
    }
    
    /**
    * countPerServicePerUser
    *
    * Count the number of user's 
    * booking per service.
    *
    * @return     rows
    * @category   query
    */
    public function countPerServicePerUser($service_id, $user_id)
    {
        $countRows = $this->_dbTable->select()->from('booking', array(
            'rows' => 'COUNT(*)'
        ))->where('user = ?', $user_id)->where('service = ?', $service_id);
        return $this->_dbTable->fetchRow($countRows);
    }
    
    /**
    * countPerDepartmentPerUser
    *
    * Count the number of user's 
    * booking per department.
    *
    * @return     rows|booking_id|service_id|department_id
    * @category   query
    */
    public function countPerDepartmentPerUser($department_id, $user_id)
    {
        $select = $this->_dbTable->select()->from(array(
            'b' => 'booking'
        ), array(
            'rows' => 'COUNT(*)',
            'b.booking_id'
        ))->joinInner(array(
            's' => 'service'
        ), 's.service_id = b.service', array(
            's.service_id'
        ))->joinInner(array(
            'd' => 'department'
        ), 'd.department_id = s.department', array(
            'd.department_id'
        ))->group(array(
            'd.department_id'
        ))->where('s.department = ?', $department_id)->where('b.user = ?', $user_id)->setIntegrityCheck(false);
        
        return $this->_dbTable->fetchRow($select);
    }
}

