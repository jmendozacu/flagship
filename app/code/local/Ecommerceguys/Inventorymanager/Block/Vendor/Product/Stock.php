<?php 
class Ecommerceguys_Inventorymanager_Block_Vendor_Product_Stock extends Mage_Core_Block_Template
{
	public function getVendorProducts(){
		$vendorId = $this->getCurrentVendorId();
		$vendorModel = Mage::getResourceModel('inventorymanager/vendor');
		$products = $vendorModel->getProducts($vendorId);
		return $products;
	}
	
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
}