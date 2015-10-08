<?php 

class Ecommerceguys_Inventorymanager_Block_Vendor_Employer_Edit extends Mage_Core_Block_Template
{
	public function getAllVendors(){


		$vendor = Mage::getSingleton('core/session')->getVendor()->getId();
		$vendors = Mage::getModel('inventorymanager/vendor')->getCollection()->addFieldToFilter('parent_id',$vendor);
		return $vendors;
	}
public function getPostUrl($id){
		return Mage::getUrl('inventorymanager/vendor/employer/save',array('id'=>$id));	
	}
}