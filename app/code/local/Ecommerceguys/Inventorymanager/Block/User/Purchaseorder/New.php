<?php 

class Ecommerceguys_Inventorymanager_Block_User_Purchaseorder_New extends Mage_Core_Block_Template
{
	public function getVendors(){
		$vendors = array();
      	$vendorsCollection = Mage::getModel('inventorymanager/vendor')->getCollection();
      	foreach ($vendorsCollection as $vendor){
      		$vendors[$vendor->getId()] = $vendor->getName();
      	}
      	return $vendors;
	}
	
	public function getAllShippingMethod(){
		$methods = Mage::getSingleton('shipping/config')->getActiveCarriers();
		$shippingMethods = array();
		foreach($methods as $_code => $_method)
	 	{
	 		if(!$_title = Mage::getStoreConfig("carriers/$_code/title")){
	            $_title = $_code;
	 		}
	      	$shippingMethods[$_code] = $_title;
	 	}
	 	return $shippingMethods;
	}
	
	public function getPaymentTerms(){
		return Mage::getModel('inventorymanager/paymentterms')->getArray();
	}
	
	public function getCurrentOrder(){
		$id = $this->getRequest()->getParam('id',0);
		if(!$id || $id < 1){
			return false;
		}
		return Mage::getModel('inventorymanager/purchaseorder')->load($id);
	}
	
	public function getCurrentOrderProducts(){
		$purchaseOrder = $this->getCurrentOrder();
		if(!$purchaseOrder) return false;
		$orderProduct = Mage::getModel('inventorymanager/product')->getCollection();
		$orderProduct->addFieldToFilter('po_id', $purchaseOrder->getId());
		return $orderProduct;
	}
}