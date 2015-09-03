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
	
	public function getProductInfoObject(){
		$vendorId = Mage::getSingleton('core/session')->getVendor()->getId();
		$productId = $this->getRequest()->getParam('id',0);
		
		$productInfoCollection = Mage::getModel('inventorymanager/vendor_productinfo')->getCollection();
		$productInfoCollection->addFieldToFilter("product_id", $productId);
		$productInfoCollection->addFieldToFilter("vendor_id", $vendorId);
		$productInfoCollection->addFieldToFilter("is_revision", 0);
		
		if($productInfoCollection->count() > 0){
			return $productInfoCollection->getFirstItem();
		}
		return false;
	}
	
	public function getProductInfoDescription(){
		if($productInfoObject = $this->getProductInfoObject()){
			return $productInfoObject->getDescription();
		}
		return false;
	}
	
	public function getProductInfoCost(){
		if($productInfoObject = $this->getProductInfoObject()){
			return $productInfoObject->getCost();
		}
		return false;
	}
	
	public function getProductInfoLength(){
		if($productInfoObject = $this->getProductInfoObject()){
			return $productInfoObject->getLength();
		}
		return false;
	}
	
	public function getProductInfoWidth(){
		if($productInfoObject = $this->getProductInfoObject()){
			return $productInfoObject->getWidth();
		}
		return false;
	}
	
	public function getProductInfoHeight(){
		if($productInfoObject = $this->getProductInfoObject()){
			return $productInfoObject->getHeight();
		}
		return false;
	}
	
	public function getProductInfoFunSpec(){
		if($productInfoObject = $this->getProductInfoObject()){
			return $productInfoObject->getFunSpec();
		}
		return false;
	}
	
	public function getProductInfoMaterial(){
		if($productInfoObject = $this->getProductInfoObject()){
			return $productInfoObject->getMaterial();
		}
		return false;
	}
	
	public function getProductInfoLighting(){
		if($productInfoObject = $this->getProductInfoObject()){
			return $productInfoObject->getLighting();
		}
		return false;
	}
	
	public function getProductInfoBoxHeight(){
		if($productInfoObject = $this->getProductInfoObject()){
			return $productInfoObject->getBoxHeight();
		}
		return false;
	}
	
	public function getProductInfoBoxWidth(){
		if($productInfoObject = $this->getProductInfoObject()){
			return $productInfoObject->getBoxWidth();
		}
		return false;
	}
	
	public function getProductInfoBoxLength(){
		if($productInfoObject = $this->getProductInfoObject()){
			return $productInfoObject->getBoxLength();
		}
		return false;
	}
}