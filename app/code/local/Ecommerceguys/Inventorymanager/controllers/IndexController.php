<?php
class Ecommerceguys_Inventorymanager_IndexController extends Mage_Core_Controller_Front_Action
{
	protected function _getSession()
    {
        return Mage::getSingleton('inventorymanager/session');
    }
    
	public function indexAction(){
		$this->_redirect("*/vendor/");
	}
	
	public function preDispatch(){
		parent::preDispatch();
		if (!$this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/vendor/login');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
	}
}