<?php
class Ecommerceguys_Inventorymanager_Block_Vendor_Product_Revision extends Mage_Core_Block_Template
{
	public function getCurrentRevision(){
		$revisionId = $this->getRequest()->getParam('revision_id');
		$revisionObject = Mage::getModel('inventorymanager/vendor_productinfo')->load($revisionId);
		return $revisionObject;
	}
	
	public function getProductInfoObject(){
		$revisionObject = $this->getCurrentRevision();
		$productId = $revisionObject->getProductId();
		$vendorId = $revisionObject->getVendorId();
		
		$productInfoCollection = Mage::getModel('inventorymanager/vendor_productinfo')->getCollection();
		$productInfoCollection->addFieldToFilter("product_id", $productId);
		$productInfoCollection->addFieldToFilter("vendor_id", $vendorId);
		$productInfoCollection->addFieldToFilter("is_revision", 0);
		
		if($productInfoCollection->count() > 0){
			return $productInfoCollection->getFirstItem();
		}
		return false;
	}
	
	public function getDifferences(){
		$revisionObject = $this->getCurrentRevision();
		$productInfoObject = $this->getProductInfoObject();
		
		$differences = array_diff($productInfoObject->getData(), $revisionObject->getData());
		
		return $differences;
	}
	
	public function getProductObject(){
		return Mage::getModel('catalog/product')->load($this->getCurrentRevision()->getProductId());
	}
}