<?php
class Ecommerceguys_Inventorymanager_Block_Purchaseorder_Print extends Mage_Core_Block_Template
{
	public function getSerials(){
		$serials = Mage::getModel('inventorymanager/label')->getCollection();	
		$serials->addFieldToFilter('order_id', $this->getRequest()->getParam('id'));
		return $serials;
	}
	
	public function getOrderObject(){
		return Mage::getModel("inventorymanager/purchaseorder")->load($this->getRequest()->getParam('id'));
	}
	
	public function getOrderProduct($serialId){
		$serialObject = Mage::getModel('inventorymanager/label')->load($serialId);
		return Mage::getModel('inventorymanager/product')->load($serialObject->getProductId());
	}
	
	public function getCatalogProduct($serialId){
		$orderProduct = $this->getOrderProduct($serialId);
		return Mage::getModel("catalog/product")->load($orderProduct->getMainProductId());
	}
	
	public function getInventorymanagerProductInfo($serialId){
		$catalogProduct = $this->getCatalogProduct($serialId);
		$order = $this->getOrderObject();
		$inventoryManagerProductInfoCollection = Mage::getModel("inventorymanager/vendor_productinfo")->getCollection();
		$inventoryManagerProductInfoCollection->addFieldToFilter("product_id", $catalogProduct->getId());
		$inventoryManagerProductInfoCollection->addFieldToFilter("vendor_id", $order->getVendorId());
		if($inventoryManagerProductInfoCollection->count() > 0)
			return $inventoryManagerProductInfoCollection->getFirstItem();
		return false;
	}
}