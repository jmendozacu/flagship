<?php 

class Ecommerceguys_Inventorymanager_Block_User_Vendor extends Mage_Core_Block_Template
{
	public function getAllVendors(){
		$vendors = Mage::getModel('inventorymanager/vendor')->getCollection();
		return $vendors;
	}
public function getPostUrl($id){
		return Mage::getUrl('inventorymanager/adminuser/vendorsave',array('id'=>$id));	
	}
}