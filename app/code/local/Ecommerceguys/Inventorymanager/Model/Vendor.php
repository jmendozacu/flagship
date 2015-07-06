<?php

class Ecommerceguys_Inventorymanager_Model_Vendor extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('inventorymanager/vendor');
    }
    
    public function checkVendorId($id){
    	$vendorObject = $this->load($id);
    	if($vendorObject && $vendorObject->getId()){
    		return true;
    	}
    	return false;
    }
    
    public function authenticate($login, $password){
    	$this->loadVenderByLogin($login);
    	if(!$this->validatePassword($password)){
    		throw Mage::exception('Mage_Core', Mage::helper('customer')->__('Invalid login or password.')
            );
    	}
    	return true;
    }
    
    public function loadVenderByLogin($login){
    	$this->load($login, "username");
    }
    
    public function validatePassword($password){
    	if($this->getPassword() === $password){
    		return true;
    	}
    	return false;
    }
    
    public function getMaterial(){
    	$verndorId = $this->getId();
    	return Mage::getResourceModel('inventorymanager/vendor')->getMaterial($verndorId);
    }
    
    public function getLighting(){
    	$verndorId = $this->getId();
    	return Mage::getResourceModel('inventorymanager/vendor')->getLighting($verndorId);
    }
}