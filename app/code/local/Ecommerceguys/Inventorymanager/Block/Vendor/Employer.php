<?php 

class Ecommerceguys_Inventorymanager_Block_Vendor_Employer extends Mage_Core_Block_Template
{
	public function getAllVendors(){

		$vendor = Mage::getSingleton('core/session')->getVendor()->getId();
		$vendors = Mage::getModel('inventorymanager/vendor')->getCollection()->addFieldToFilter('parent_id',$vendor);
		return $vendors;
	}
}