<?php 
	
class Ecommerceguys_Inventorymanager_Block_User_Vendor_Productinfo extends Mage_Core_Block_Template
{
	public function getProductInfo(){
		$vendorId = $this->getRequest()->getParam('vendor_id');
		$productId = $this->getRequest()->getParam('product_id');
		
		$productinfoCollection = Mage::getModel('inventorymanager/vendor_productinfo')->getCollection();
		$productinfoCollection->addFieldToFilter('vendor_id', $vendorId);
		$productinfoCollection->addFieldToFilter('product_id', $productId);
		$productinfoCollection->addFieldToFilter('is_revision', 0);
		
		if($productinfoCollection && $productinfoCollection->count() > 0){
			$productInfo = $productinfoCollection->getFirstItem();
			return $productInfo;
		}
		return false;
	}
	
	public function getCurrentVendor(){
		$vendorId = $this->getRequest()->getParam('vendor_id');
		
		$vendor = Mage::getModel('inventorymanager/vendor')->load($vendorId);
		return $vendor;
	}
}