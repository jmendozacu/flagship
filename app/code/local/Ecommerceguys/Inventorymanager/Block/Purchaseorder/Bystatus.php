<?php
class Ecommerceguys_Inventorymanager_Block_Purchaseorder_Bystatus extends Mage_Core_Block_Template
{
	public function getVendorOrders(){
		$vendorId = $this->getCurrentVendorId();
		
		$status = $this->getRequest()->getParam('status');
		$orders = Mage::getModel('inventorymanager/purchaseorder')->getCollection();
		$orders->addFieldToFilter('status', $status);
		$orders->addFieldToFilter('vendor_id', $vendorId);
		return $orders;
	}
	
	public function getCurrentVendorId(){
		return Mage::getSingleton('core/session')->getVendor()->getId();
	}
}