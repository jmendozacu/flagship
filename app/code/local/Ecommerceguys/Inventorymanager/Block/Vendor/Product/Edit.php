<?php
class Ecommerceguys_Inventorymanager_Block_Vendor_Product_Edit extends Mage_Core_Block_Template
{
	public function getCurrentProduct(){
		$productId = $this->getRequest()->getParam('id',0);
		return Mage::getModel('catalog/product')->load($productId);
	}
	
	public function getCurrentVendor(){
		$vendorId = Mage::getSingleton('core/session')->getVendor()->getId();
		return Mage::getModel('inventorymanager/vendor')->load($vendorId);
	}
}