<?php
class Ecommerceguys_Inventorymanager_Block_Purchaseorder_Printpo extends Mage_Core_Block_Template
{
	public function getOrderObject(){
		return Mage::getModel("inventorymanager/purchaseorder")->load($this->getRequest()->getParam('id'));
	}
	
	public function getVendor(){
		$order = $this->getOrderObject();
		return Mage::getModel('inventorymanager/vendor')->load($order->getVendorId());
	}
	
	public function getOrderProducts(){
		$products = Mage::getModel('inventorymanager/product')->getCollection();
		$products->addFieldToFilter('po_id', $this->getRequest()->getParam('id'));
		return $products;
	}
}