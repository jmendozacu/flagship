<?php
class Ecommerceguys_Inventorymanager_Block_User_Purchaseorder extends Mage_Core_Block_Template
{

	public function getAllpurchaseorders(){
		return $collection = Mage::getModel('inventorymanager/purchaseorder')->getCollection();
	}
	 
     

    public function getVendorname($id){

    	if(!is_numeric($id)){
    		return;
    	}
		$vendor = Mage::getModel('inventorymanager/vendor')->load($id);
      	return $vendor->getName();
	}


	public function getPaymentterms($id){

    	if(!is_numeric($id)){
    		return;
    	}
		
		$paymentTerms = Mage::getModel('inventorymanager/paymentterms')->load($id);
      	return $vendor->getName();
	}

	
	
	public function getCurrentVendorId(){
		return Mage::getSingleton('core/session')->getVendor()->getId();
	}
	
	public function getVendorOrders(){
		$vendorId = $this->getCurrentVendorId();
		$purchaseorderCollection = Mage::getModel('inventorymanager/purchaseorder')->getCollection();
		$purchaseorderCollection->addFieldToFilter('vendor_id', $vendorId);
		$purchaseorderCollection->setOrder('po_id','DESC');
		return $purchaseorderCollection;
	}
}