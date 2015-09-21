<?php 

class Ecommerceguys_Inventorymanager_Block_Vendor_Profile extends Mage_Core_Block_Template
{
	public function getCurrentVendorId(){
		$vendor = Mage::getSingleton('core/session')->getVendor();
		if($vendor && $vendor->getId()){
			return $vendor->getId();
		}else{
			return false;
		}
	}
	
	public function getCurrentVendor(){
		$vendor = Mage::getSingleton('core/session')->getVendor();
		if($vendor && $vendor->getId()){
			return $vendor;
		}else{
			return false;
		}
	}
	public function getPostUrl(){
		return Mage::getUrl('inventorymanager/vendor/vendorsave');
	}
}