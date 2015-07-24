<?php
class Ecommerceguys_Inventorymanager_Block_Purchaseorder extends Mage_Core_Block_Template
{
	public function getCurrentVendorId(){
		return Mage::getSingleton('core/session')->getVendor()->getId();
	}
	
	public function getVendorOrders(){
		$vendorId = $this->getCurrentVendorId();
		$purchaseorderCollection = Mage::getModel('inventorymanager/purchaseorder')->getCollection();
		$purchaseorderCollection->addFieldToFilter('vendor_id', $vendorId);
		return $purchaseorderCollection;
	}
}