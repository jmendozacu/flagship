<?php 
class Ecommerceguys_Inventorymanager_Model_Label_History extends Mage_Core_Model_Abstract
{
	public function _construct()
    {
        parent::_construct();
        $this->_init('inventorymanager/label_history');
    }
    
    protected function _getSession()
    {
        return Mage::getSingleton('inventorymanager/session');
    }
    
    public function getAdminUserId(){
    	$session = Mage::getSingleton('core/session');
    	$user = $session->getUser();
    	return $user->getId();
    }
    
    public function getUserInfo(){
    	if($this->_getSession()->isAdminUser()){
    		$user = Mage::getSingleton('core/session')->getUser();
    		$userId = "1-".$user->getId();
    	}elseif ($this->_getSession()->isLoggedIn()){
    		$vendor = Mage::getSingleton('core/session')->getVendor();
    		$userId = "2-".$vendor->getId();
    	}
    	return $userId;
    }
    
    public function addStatusAndLocation($serialId){
    	$serial = Mage::getModel('inventorymanager/label')->load($serialId);
    	if($serial && $serial->getId()){
    		$data = array(
	    		'location'	=>	$serial->getLocation(),
	    		'status'		=>	$serial->getStatus(),
	    		'label_id'	=>	$serial->getId(),
	    		'user_id'	=>	$this->getUserInfo(),
	    		'created_time'	=>	now(),
	    	);
	    	//print_r($data); exit;
	    	try {
	    		$this->setData($data)->save();
	    	}catch (Exception $e){
	    		Mage::log($e->getMessage());
	    	}
    	}
    }
    
    public function addLocation($serialId){
    	$serial = Mage::getModel('inventorymanager/label')->load($serialId);
    	if($serial && $serial->getId()){
    		$data = array(
	    		'location'	=>	$serial->getLocation(),
	    		'label_id'	=>	$serial->getId(),
	    		'user_id'	=>	$this->getUserInfo(),
	    		'created_time'	=>	now(),
	    	);
	    	//print_r($data); exit;
	    	try {
	    		$this->setData($data)->save();
	    	}catch (Exception $e){
	    		Mage::log($e->getMessage());
	    	}
    	}
    }
    
    public function addStatus($serialId){
    	$serial = Mage::getModel('inventorymanager/label')->load($serialId);
    	if($serial && $serial->getId()){
    		$data = array(
	    		'status'		=>	$serial->getStatus(),
	    		'label_id'	=>	$serial->getId(),
	    		'user_id'	=>	$this->getUserInfo(),
	    		'created_time'	=>	now(),
	    	);
	    	//print_r($data); exit;
	    	try {
	    		$this->setData($data)->save();
	    	}catch (Exception $e){
	    		Mage::log($e->getMessage());
	    	}
    	}
    }
}