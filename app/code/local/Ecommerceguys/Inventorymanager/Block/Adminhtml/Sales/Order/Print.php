<?php 

class Ecommerceguys_Inventorymanager_Block_Adminhtml_Sales_Order_Print extends Mage_Core_Block_Template
{
	public function _construct(){
		parent::_construct();
		$this->setTemplate("inventorymanager/order/print.phtml");
	}
	
	public function getCurrentOrder(){
		$orderId = $this->getOrderId();
		return Mage::getModel('sales/order')->load($orderId);
	}
	
	public function getCurrentCustomer(){
		$order = $this->getCurrentOrder();
		return Mage::getModel('customer/customer')->load($order->getCustomerId());
	}
}