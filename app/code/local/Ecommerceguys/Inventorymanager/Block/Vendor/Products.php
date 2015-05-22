<?php 

class Ecommerceguys_Inventorymanager_Block_Vendor_Products extends Mage_Core_Block_Template
{
	public function getVendorProducts(){
		
		
		
		$productCollection = Mage::getModel('catalog/product')->getCollection()
			->addAttributeToSelect(array('name', 'sku'));
		$productCollection->getSelect()
			->join(array("vp"=>"inventorymanager_vendorproduct"), "e.entity_id = vp.product_id",array('vendor_id'))
			->where('vendor_id = ' .$this->getCurrentVendorId() );
		return $productCollection;
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