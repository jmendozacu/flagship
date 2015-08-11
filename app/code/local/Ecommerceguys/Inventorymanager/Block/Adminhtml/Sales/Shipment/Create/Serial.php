<?php
class Ecommerceguys_Inventorymanager_Block_Adminhtml_Sales_Shipment_Create_Serial extends Mage_Core_Block_Template
{
	public function getCurrentSerials(){
		$params = $this->getRequest()->getParams();
		if(isset($params['shipment_id']) && $params['shipment_id'] > 0){
			$serials = Mage::getModel('inventorymanager/label')->getCollection();
			$serials->addFieldToFilter('shipment_id', $params['shipment_id']);
			return $serials;
		}
	}
	
	public function getShipmentId()
	{
		$params = $this->getRequest()->getParams();
		if(isset($params['shipment_id']) && $params['shipment_id'] > 0){
			return $params['shipment_id'];
		}
		return false;
	}
	
	public function getOrderId(){
		if($shipmentId = $this->getShipmentId()){
			$shipment = Mage::getModel('sales/order_shipment')->load($shipmentId);
			if($shipment && $shipment->getId()){
				$order = $shipment->getOrder();
				if($order && $order->getId()){
					return $order->getId();
				}
			}
		}
		return 0;
	}
}