<?php 

class Ecommerceguys_Inventorymanager_Block_Vendor_Employer extends Mage_Core_Block_Template
{
	/*public function getAllVendors(){

		$vendor = Mage::getSingleton('core/session')->getVendor()->getId();
		$vendors = Mage::getModel('inventorymanager/vendor')->getCollection()->addFieldToFilter('parent_id',$vendor);
		return $vendors;
	}*/
	
	public function getEmployees(){
		$vendorId = Mage::getSingleton('core/session')->getVendor()->getId();
		$employess = Mage::getModel('inventorymanager/vendor_employee')->getCollection();
		$employess->addFieldToFilter("parent_id", $vendorId);
		return $employess;
	}
}