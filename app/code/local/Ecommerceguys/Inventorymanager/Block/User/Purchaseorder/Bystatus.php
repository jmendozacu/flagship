<?php 
class Ecommerceguys_Inventorymanager_Block_User_Purchaseorder_Bystatus extends Mage_Core_Block_Template
{
	public function getOrderByStatus(){
		$status = $this->getRequest()->getParam('status');
		$orders = Mage::getModel('inventorymanager/purchaseorder')->getCollection();
		$orders->addFieldToFilter('status', $status);
		return $orders;
	}
	
	public function getVendorname($id){
    	if(!is_numeric($id)){
    		return;
    	}
		$vendor = Mage::getModel('inventorymanager/vendor')->load($id);
      	return $vendor->getName();
	}
}