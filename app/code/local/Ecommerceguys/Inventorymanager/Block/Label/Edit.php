<?php 

class Ecommerceguys_Inventorymanager_Block_Label_Edit extends Mage_Core_Block_Template
{
	public function getLabelObject(){
		$serialKey = $this->getRequest()->getParam('serial_key');
		$serialKey= trim($serialKey);
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
	
	public function getVendorInfoObject(){
		$order = $this->getPurchaseOrder();
		$catalogProduct = $this->gerMainProduct();
		
		//echo "vendorId = " . $order->getVendorId();
		//echo "<br/>ProductId = " . $catalogProduct->getId();
		
		$inventorymanagerProducts = Mage::getModel('inventorymanager/vendor_productinfo')->getCollection();
		$inventorymanagerProducts->addFieldToFilter('vendor_id', $order->getVendorId());
		$inventorymanagerProducts->addFieldToFilter('product_id', $catalogProduct->getId());
		
		if($inventorymanagerProducts->count()){
			return $inventorymanagerProducts->getFirstItem();
		}
		return false;
	}
}