<?php 

class Ecommerceguys_Inventorymanager_Model_Resource_Vendor_Employee extends Mage_Core_Model_Resource_Db_Abstract
{
	public function _construct()
    {    
        $this->_init('inventorymanager/vendor_employee', 'employee_id');
    }
}