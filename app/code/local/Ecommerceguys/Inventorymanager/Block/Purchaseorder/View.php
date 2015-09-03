<?php 
class Ecommerceguys_Inventorymanager_Block_Purchaseorder_View extends Mage_Core_Block_Template
{
	public function getPurchaseorderId(){
		return $this->getRequest()->getParam('id', 0);
	}
	
	public function getPurchaseOrder(){
		$id = $this->getPurchaseorderId();
		return Mage::getModel('inventorymanager/purchaseorder')->load($id);
	}
	
	public function getProducts(){
		$purchaseorderProducts = Mage::getModel('inventorymanager/product')->getCollection();
		$purchaseorderProducts->addFieldToFilter('po_id', $this->getPurchaseorderId());
		return $purchaseorderProducts;
	}
	
	public function getComments(){
		$comments = Mage::getModel('inventorymanager/comment')->getCollection();
		$comments->addFieldToFilter("po_id", $this->getPurchaseorderId());
		return $comments;
	}
	public function getSerials($productId){
		$purchaseorderId = $this->getPurchaseorderId();
		$serials = Mage::getModel('inventorymanager/label')->getCollection();
		$serials->addFieldToFilter('order_id', $purchaseorderId);
		$serials->addFieldToFilter('product_id', $productId);
		return $serials;
	}
}