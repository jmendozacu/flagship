<?php
class Ecommerceguys_Inventorymanager_Block_Purchaseorder_Productpdf extends Mage_Core_Block_Template
{
	public function getSerials(){
		$data = $this->getRequest()->getParams();
		$orderId = $data["order_id"];
		$productId = $data["product_id"];
		$labelCollection = Mage::getModel('inventorymanager/label')->getCollection();
		$labelCollection->addFieldToFilter('order_id', $orderId);
		$labelCollection->addFieldToFilter('product_id', $productId);
		return $labelCollection;
	}
	
	public function getOrderOroduct(){
		$data = $this->getRequest()->getParams();
		$productId = $data["product_id"];
		$orderProduct = Mage::getModel('inventorymanager/product')->load($productId);
		return $orderProduct;
	}
	
	public function getCatalogProduct(){
		$orderProduct = $this->getOrderOroduct();
		return Mage::getModel('catalog/product')->load($orderProduct->getMainProductId());
	}
	
	public function getProductInfoObject(){
		$catalogProduct = $this->getCatalogProduct();
		$productId = $catalogProduct->getId();
		$vendorId = Mage::getSingleton('core/session')->getVendor()->getId();
		$productInfoCollection = Mage::getModel('inventorymanager/vendor_productinfo')->getCollection();
		$productInfoCollection->addFieldToFilter("product_id", $productId);
		$productInfoCollection->addFieldToFilter("vendor_id", $vendorId);
		$productInfoCollection->addFieldToFilter("is_revision", 0);
		if($productInfoCollection->count() && $productInfoCollection->count() > 0){
			return $productInfoCollection->getFirstItem();
		}
	}
}