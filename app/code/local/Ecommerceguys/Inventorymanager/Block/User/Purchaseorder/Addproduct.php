<?php

class Ecommerceguys_Inventorymanager_Block_User_Purchaseorder_Addproduct extends Mage_Core_Block_Template
{
	public function getProduct(){
		$postData = $this->getRequest()->getPost();
		return Mage::getModel('catalog/product')->load($postData['product_id']);
	}
	
	public function getVendorProduct(){
		$vendorId = $this->getRequest()->getPost('vendor_id');
		$product = $this->getproduct();
		$vendorProducts = Mage::getModel('inventorymanager/vendor_productinfo')->getCollection();
		$vendorProducts->addFieldToFilter('is_revision', 0);
		$vendorProducts->addFieldToFilter('vendor_id', $vendorId);
		$vendorProducts->addFieldToFilter('product_id', $product->getId());
		if($vendorProducts && $vendorProducts->count() && $vendorProducts->count() > 0){
			return $vendorProducts->getFirstItem();
		}
	}
}