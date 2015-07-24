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
    		return false;
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
    
    public function authenticateAdmin($username, $password){
    	Mage::getSingleton('core/session', array('name' => 'adminhtml'));
    	$user = Mage::getModel('admin/user')->loadByUsername($username);
    	$user_id = $user->getId();
    	if($user->getId()>=1){
    		$dbpassword = $user->getData('password');
    		if(Mage::helper('core')->validateHash($password, $dbpassword))
    		{
    			return true;
    		}
    	}
    	return false;
    }
}