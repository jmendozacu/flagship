<?php 
class Ecommerceguys_Inventorymanager_Adminhtml_VendorController extends Mage_Adminhtml_Controller_action
{
	
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('inventorymanager/vendors')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}
	
	public function indexAction(){
		$this->_initAction();
		$this->renderLayout();
	}
}