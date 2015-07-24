<?php
class Ecommerceguys_Inventorymanager_AdminuserController extends Mage_Core_Controller_Front_Action
{
	
	protected function _getSession()
    {
        return Mage::getSingleton('inventorymanager/session');
    }
	
	public function preDispatch(){
		parent::preDispatch();
		if (!$this->_getSession()->isAdminUser()) {
            $this->_redirect('*/vendor/login');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
	}
	
	public function vendorsAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function logoutAction(){
		$session = Mage::getSingleton('core/session');
		$session->setUserType("");
		$session->setUser("");
		$this->_redirect('*/vendor/login');
	}
	
	public function vendorproductAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
}