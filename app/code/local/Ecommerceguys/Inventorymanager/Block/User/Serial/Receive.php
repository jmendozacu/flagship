<?php
class Ecommerceguys_Inventorymanager_Block_User_Serial_Receive extends Mage_Core_Block_Template
{
	public function getLabelObject(){
		$serialKey = trim($this->getRequest()->getParam('serial_key'));
		$labelObject = Mage::getModel('inventorymanager/label')->load($serialKey, 'serial');
		if($labelObject && $labelObject->getId()){
			return $labelObject;
		}
		return false;
	}
	
	public function getOrderProduct(){
		$labelObject = $this->getLabelObject();
		return Mage::getModel('inventorymanager/product')->load($labelObject->getProductId());
	}
	
	public function gerMainProduct(){
		$orderProduct = $this->getOrderProduct();
		if($orderProduct && $orderProduct->getId()){
			return Mage::getModel('catalog/product')->load($orderProduct->getMainProductId());
		}
		return false;
	}
	
	public function getComments(){
		$label = $this->getLabelObject();
		$labelComments = Mage::getModel('inventorymanager/label_comment')->getCollection();
		$labelComments->addFieldToFilter('label_id', $label->getId());
		return $labelComments;
	}
	
	public function getPurchaseOrder(){
		return Mage::getModel('inventorymanager/purchaseorder')->load($this->getOrderProduct()->getPoId());
	}
	
	public function getVendorProduct(){
		$order = $this->getPurchaseOrder();
		$mainProduct = $this->gerMainProduct();
		$vendorId = $order->getVendorId();
		
		$inventorymanagerProducts = Mage::getModel('inventorymanager/vendor_productinfo')->getCollection();
		$inventorymanagerProducts->addFieldToFilter('vendor_id', $vendorId);
		$inventorymanagerProducts->addFieldToFilter('product_id', $mainProduct->getId());
		if($inventorymanagerProducts->count()){
			return $inventorymanagerProducts->getFirstitem();
		}
		return false;
	}
}