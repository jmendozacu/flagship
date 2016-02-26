<?php 
class Ecommerceguys_Inventorymanager_Block_label_Refresh extends Mage_Core_Block_Template
{
	public function getSerials(){
		$productId = $this->getRequest()->getParam('product_id');
		$purchaseorderId = $this->getRequest()->getParam('order_id');
		$serials = Mage::getModel('inventorymanager/label')->getCollection();
		$serials->addFieldToFilter('order_id', $purchaseorderId);
		$serials->addFieldToFilter('product_id', $productId);
		return $serials;
	}
	
	public function getPurchaseorderId(){
		return $this->getRequest()->getParam('order_id', 0);
	}
	
	public function getPurchaseOrder(){
		$id = $this->getPurchaseorderId();
		return Mage::getModel('inventorymanager/purchaseorder')->load($id);
	}
}