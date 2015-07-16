<?php
class Ecommerceguys_Inventorymanager_Block_Vendor_Product_Generatepdf extends Mage_Core_Block_Template
{
	public function getVendorProducts(){
		$productId = $this->getRequest()->getParam('product_id');
		$vendorProducts = Mage::getModel('inventorymanager/product')->getCollection();
		$vendorProducts->addFieldToFilter('main_product_id', $productId);
		return $vendorProducts;
	}
	
	public function getlabels(){
		$orderProducts = $this->getVendorProducts();
		$orderProductIds = $orderProducts->getAllIds();
		
		$labelCollection = Mage::getModel('inventorymanager/label')->getCollection();
		$labelCollection->AddFieldToFilter('product_id', array('in'=>$orderProductIds));
		return $labelCollection;
	}
	
	public function getMainProduct(){
		$productId = $this->getRequest()->getParam('product_id');
		return Mage::getModel('catalog/product')->load($productId);
	}
	
	public function getProductInfoObject(){
		$productId = $this->getRequest()->getParam('product_id');
		$productInfoCollection = Mage::getModel('inventorymanager/vendor_productinfo')->getCollection();
		$productInfoCollection->addFieldToFilter("product_id", $productId);
		$productInfoCollection->addFieldToFilter("is_revision", 0);
		return $productInfoCollection->getFirstItem();
	}
}