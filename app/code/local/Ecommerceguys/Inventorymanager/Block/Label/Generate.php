<?php
class Ecommerceguys_Inventorymanager_Block_Label_Generate extends Mage_Core_Block_Template
{
	public function getProducts(){
		$orderId = $this->getOrderId();
		$labelCollection = Mage::getModel('inventorymanager/label')->getCollection();
		$labelCollection->addFieldToFilter('order_id', $orderId);
		return $labelCollection;
	}
	
	public function getOrderId(){
		return $this->getRequest()->getParam('id');
	}
	
	public function getProductrInfoObject($productId){
		
		$vendorProduct = Mage::getModel('inventorymanager/vendor_productinfo')->getCollection();
		$vendorProduct->addFieldToFilter('is_revision', 0);
		$vendorProduct->addFieldToFilter('product_id', $productId);

		/*
		echo "<pre>";
		print_r($vendorProduct->getData());
		exit;
		*/
		if($vendorProduct->count()){
			return $vendorProduct->getFirstItem();
		}
	}
}