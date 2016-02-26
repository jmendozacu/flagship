<?php 

class Ecommerceguys_Inventorymanager_Block_Vendor_Employer_Edit extends Mage_Core_Block_Template
{
	public function getAllVendors(){

		$vendor = Mage::getSingleton('core/session')->getVendor()->getId();
		$vendors = Mage::getModel('inventorymanager/vendor')->getCollection()->addFieldToFilter('parent_id',$vendor);
		return $vendors;
	}

	public function getCurrentVendor(){
		
	}
	
	public function getCurrentEmployee(){
		$id = $this->getRequest()->getParam('id', 0);
		$employee = Mage::getModel('inventorymanager/vendor_employee')->load($id);
		if(!$employee || !$employee->getId()){
			$data = Mage::getSingleton('core/session')->getFormData();
			$employee->setData($data);
		}
		return $employee;
	}
	public function getPostUrl($id){
		return Mage::getUrl('inventorymanager/vendor_employer/save',array("id"=>$id));
	}
}