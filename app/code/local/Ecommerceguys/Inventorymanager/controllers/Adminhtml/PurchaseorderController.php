<?php 
class Ecommerceguys_Inventorymanager_Adminhtml_PurchaseorderController extends Mage_Adminhtml_Controller_action
{
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('inventorymanager/purchaseorder')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Purchase Order'), Mage::helper('adminhtml')->__('Purchase Order'));
		
		return $this;
	}
	
	public function indexAction(){
		$this->_initAction();
		$this->renderLayout();
	}
}