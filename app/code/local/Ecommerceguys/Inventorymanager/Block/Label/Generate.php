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
}