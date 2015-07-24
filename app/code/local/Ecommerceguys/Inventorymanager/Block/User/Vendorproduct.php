<?php

class Ecommerceguys_Inventorymanager_Block_User_Vendorproduct extends Mage_Core_Block_Template
{
	public function getCurrentVendor(){
		$param = $this->getRequest()->getParams();
		if(isset($param['vendor_id']) && $param['vendor_id'] != ""){
			return Mage::getModel('inventorymanager/vendor')->load($param['vendor_id']);
		}
	}
	
	public function getVendorProducts(){
		/*$param = $this->getRequest()->getParams();
		$vendorProducttable = Mage::getSingleton('core/resource')->getTableName('inventorymanager_vendorproduct');
		$products = Mage::getModel('catalog/product')->getCollection();
		$products->addAttributeToSelect(array('name'));
		if(isset($param['vendor_id']) && $param['vendor_id'] > 0){
			$products->getSelect()->joinLeft(array('vp'=>$vendorProducttable), "e.entity_id = vp.product_id")
				->where("vp.vendor_id = ".$param['vendor_id']);
		}*/
		
		$vendor = $this->getCurrentVendor();
		$products = Mage::getResourceModel('inventorymanager/vendor')->getProducts($vendor->getId());
		
		return $products;
	}
}