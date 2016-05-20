<?php 
class Ecommerceguys_Inventorymanager_Block_User_Shipmanager_Waitingshipment extends Mage_Core_Block_Template
{
	public function getOrders(){
		$orderCollection = Mage::getModel('sales/order')->getCollection();
		$orderCollection->addAttributeToFilter('created_at', array('gt'=>'2015-02-01 00:00:00'));
		
		$orders = array();
		foreach($orderCollection as $item){
			$order = Mage::getModel('sales/order')->load($item->getId());
			//echo $order->hasShipments()
			if($order->hasShipments() == 0){
				$orders[] = $order;  
				//echo $order->hasShipments();
			}
			//echo $order->hasShipments();
		}
		//exit;
		return $orders;
	}
	public function getOscommerceOrders(){
		$resource	= Mage::getSingleton('core/resource');
			$conn 		= $resource->getConnection('oscomm_read');
			$results 	= $conn->query("SELECT * FROM orders where date_purchased >= '2015-02-01 00:00:00' AND orders_status NOT IN (111,105,109,115,114,116,118)");
			$row = $results->fetchAll();

			return $row;		
	}

	public function  getStorename($storeId){

		$store = Mage::getModel('core/store')->load($storeId);
    	return $name = $store->getName();
	}
}