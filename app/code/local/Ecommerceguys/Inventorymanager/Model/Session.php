<?php 

class Ecommerceguys_Inventorymanager_Model_Session extends Mage_Core_Model_Session_Abstract
{
	protected $_vendor;
	
	protected $_isVendorIdChecked = null;
	
	public function setVendor(Ecommerceguys_Inventorymanager_Model_Vendor $vendor){
		$this->_vendor = $vendor;
		$vendor->setIsJustConfirmed(true);
		return $this;
	}
	
	public function getVendor(){
		if($this->_vendor instanceof Ecommerceguys_Inventorymanager_Model_Vendor){
			return $this->_vendor;
		}
		$vendor = Mage::getModel('inventorymanager/vendor')->load($this->getId());
		$this->_vendor = $vendor;
		return $vendor;
	}
	
	public function setVendorId($id){
		$this->setData('vendor_id', $id);
        return $this;		
	}
	
	public function getVendorId()
    {
        if ($this->getData('vendor_id')) {
            return $this->getData('vendor_id');
        }
        return ($this->isLoggedIn()) ? $this->getId() : null;
    }
    
	public function isLoggedIn()
    {
       // return (bool)$this->getId() && (bool)$this->checkVendorId($this->getId());
       if (Mage::getSingleton('core/session')->getVendor() && Mage::getSingleton('core/session')->getVendor()->getId()) {
       	return true;
       }
       return false;
    }

    public function isEmployer()
    {
       // return (bool)$this->getId() && (bool)$this->checkVendorId($this->getId());
       if (Mage::getSingleton('core/session')->getVendor() && Mage::getSingleton('core/session')->getVendor()->getId() && Mage::getSingleton('core/session')->getVendor()->getIsEmployer() == 1) {
        return true;
       }
       return false;
    }
    
    public function checkVendorId($vendorId)
    {
        if ($this->_isVendorIdChecked === null) {
            $this->_isVendorIdChecked = Mage::getResourceSingleton('inventorymanager/vendor')->checkVendorId($vendorId);
        }
        return $this->_isVendorIdChecked;
    }
    
    public function login($username, $password)
    {
        $vendor = Mage::getModel('inventorymanager/vendor');
        if ($vendor->authenticate($username, $password)) {
            $this->setVendorAsLoggedIn($vendor);
            $this->setVendor($vendor);
            return true;
        }elseif($vendor->authenticateAdmin($username, $password)){
        	$user = Mage::getModel('admin/user')->loadByUsername($username);
        	$this->setAdminUser($user);
        	return true;
        }
        return false;
    }
    
    public function setVendorAsLoggedIn($vendor)
    {
        $this->setVendor($vendor);
       // $this->renewSession();
        return $this;
    }
    
    public function renewSession()
    {
        parent::renewSession();
        Mage::getSingleton('core/session')->unsSessionHosts();

        return $this;
    }
    
    public function logout()
    {
        if ($this->isLoggedIn()) {
            $this->_logout();
        }
        return $this;
    }
    
    protected function _logout()
    {
        $this->setId(null);
        $this->getCookie()->delete($this->getSessionName());
        Mage::getSingleton('core/session')->setVendor("");
        return $this;
    }

    public function authenticate(Mage_Core_Controller_Varien_Action $action, $loginUrl = null)
    {
        if ($this->isLoggedIn()) {
            return true;
        }

        $this->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_current' => true)));
        if (isset($loginUrl)) {
            $action->getResponse()->setRedirect($loginUrl);
        } else {
            $action->setRedirectWithCookieCheck('inventorymanager/vendor/login');
        }

        return false;
    }
    
    public function setAdminUser($user){
    	
    	Mage::getSingleton('core/session')->setUserType('admin');
    	Mage::getSingleton('core/session')->setUser($user);
    }
    
    public function isAdminUser(){
    	$session = Mage::getSingleton('core/session');
    	if($session->getUserType() == "admin"){
    		$user = $session->getUser();
    		if($user && $user->getId()){
    			return true;
    		}
    	}
    	return false;
    }
}