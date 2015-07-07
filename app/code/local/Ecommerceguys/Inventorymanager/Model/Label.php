<?php
class Ecommerceguys_Inventorymanager_Model_Label extends Mage_Core_Model_Abstract
{
	 public function _construct()
    {
        parent::_construct();
        $this->_init('inventorymanager/label');
    }
    
    public function setNewStatus($status){
    	$statuses = Mage::helper('inventorymanager')->getOrderedProductStatusArray();
    	if(!in_array($status, $statuses)){
    		Mage::getResourceModel('inventorymanager/label')->addStatus($status);
    	}
    	//return parent::setStatus($status);
    }
    
    public function removeStatus($status){
    	$statuses = Mage::helper('inventorymanager')->getOrderedProductStatusArray();
    	if(in_array($status, $statuses)){
    		Mage::getResourceModel('inventorymanager/label')->removeStatus($status);
    	}
    }
    
    public function getLocations(){
    	return Mage::getResourceModel('inventorymanager/label')->getLocations();
    }
    
    public function setLocation($location){
    	Mage::getResourceModel('inventorymanager/label')->addLocation($location);
    	return parent::setLocation($location);
    }
}