<?php 
class Ecommerceguys_Inventorymanager_Block_Vendor_Notification extends Mage_Core_Block_Template
{
	public function getCurrentVendorId(){
		$vendor = Mage::getSingleton('core/session')->getVendor();
		if($vendor && $vendor->getId()){
			return $vendor->getId();
		}else{
			return false;
		}
	}
	
	public function getOrderNotifications(){
		$vendorId = $this->getCurrentVendorId();
		$purchaseorderCollection = Mage::getModel('inventorymanager/purchaseorder')->getCollection();
		$purchaseorderCollection->addFieldToFilter('is_seen', 0);
		$purchaseorderCollection->addFieldToFilter('vendor_id', $vendorId);
		return $purchaseorderCollection;
	}
	
	public function getSerials(){
		$vendorId = $this->getCurrentVendorId();
		
		$purchaseorderCollection = Mage::getModel('inventorymanager/purchaseorder')->getCollection();
		$purchaseorderCollection->addFieldToFilter('vendor_id', $vendorId);
		
		$orders = $purchaseorderCollection->getAllIds();
		
		$serials = Mage::getModel('inventorymanager/label')->getCollection();
		$serials->addFieldToFilter('order_id', array('in'=>$orders));
		//$serials->addFieldToFilter('created_time', array('from'=>date("Y-m-d M:i:s", strtotime("2015-09-10 00:00:00"))));
		$serials->addFieldToFilter('is_seen', 2);
		
		return $serials;
	}
}