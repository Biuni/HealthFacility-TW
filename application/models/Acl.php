<?php 

class Application_Model_Acl extends Zend_Acl
{
	public function __construct()
	{
		// ACL for undergister role
		$this->addRole(new Zend_Acl_Role('unregistered'))
			 ->add(new Zend_Acl_Resource('index'))
			 ->add(new Zend_Acl_Resource('error'))
			 ->add(new Zend_Acl_Resource('contact'))
			 ->add(new Zend_Acl_Resource('department'))
			 ->add(new Zend_Acl_Resource('faq'))
			 ->add(new Zend_Acl_Resource('login'))
			 ->add(new Zend_Acl_Resource('register'))
			 ->add(new Zend_Acl_Resource('search'))
			 ->add(new Zend_Acl_Resource('service'))
			 ->allow('unregistered', array('index','error','contact','department','faq','login','register','search','service'));
			 
		// ACL for user
		$this->addRole(new Zend_Acl_Role('user'), 'unregistered')
			 ->add(new Zend_Acl_Resource('user'))
			 ->allow('user','user');

		// ACL for staff
		$this->addRole(new Zend_Acl_Role('staff'), 'unregistered')
			 ->add(new Zend_Acl_Resource('staff'))
			 ->allow('staff','staff');
				   
		// ACL for administrator
		$this->addRole(new Zend_Acl_Role('admin'), 'unregistered')
			 ->add(new Zend_Acl_Resource('admin'))
			 ->allow('admin','admin');
	}
}