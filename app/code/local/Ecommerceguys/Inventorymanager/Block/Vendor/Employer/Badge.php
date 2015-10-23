<?php 
class Ecommerceguys_Inventorymanager_Block_Vendor_Employer_Badge extends Mage_Core_Block_Template
{
	public function getCurrentEmployee(){
		$id = $this->getRequest()->getParam('id');
		$employee = Mage::getModel('inventorymanager/vendor_employee')->load($id);
		return $employee;
	}
}