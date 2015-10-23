<?php
class Ecommerceguys_Inventorymanager_Model_Vendor_Employee extends Mage_Core_Model_Abstract
{
	public function _construct()
    {
        parent::_construct();
        $this->_init('inventorymanager/vendor_employee');
    }
    
    public function validateUsername(){
    	$username = $this->getUsername();
    	if($username == ""){
    		return false;
    	}
    	
    	$vendors = Mage::getModel('inventorymanager/vendor')->getCollection();
    	$vendors->addFieldToFilter('username', $username);
    	if($vendors->count() > 0){
    		return false;
    	}
    	$employees = Mage::getModel('inventorymanager/vendor_employee')->getCollection();
    	$employees->addFieldToFilter('username', $username);
    	if($this->getId()){
    		$employees->addFieldToFilter('employee_id', array("neq"=>$this->getId()));
    	}
    	if($employees->count() > 0){
    		return false;
    	}
    	
    	return true;
    }
}