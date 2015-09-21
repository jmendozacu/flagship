<?php
class Ecommerceguys_Inventorymanager_Block_Vendor_Product_Revisionlist extends
	Ecommerceguys_Inventorymanager_Block_Vendor_Product_Edit
{
	public function getRevisionList(){
		$vendorId = Mage::getSingleton('core/session')->getVendor()->getId();
		$productId = $this->getRequest()->getParam('id',0);
		
		$productInfoCollection = Mage::getModel('inventorymanager/vendor_productinfo')->getCollection();
		$productInfoCollection->addFieldToFilter("product_id", $productId);
		$productInfoCollection->addFieldToFilter("vendor_id", $vendorId);
		$productInfoCollection->addFieldToFilter("is_revision", 1);
		return $productInfoCollection;
	}
}