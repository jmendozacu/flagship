<?php
class Ecommerceguys_Inventorymanager_Block_User_Product_Stockgrid extends Mage_Core_Block_Template
{
	public function getProducts(){
		
		//$products = Mage::getModel('catalog/product')->getCollection();
		
		$vendorModel = Mage::getResourceModel('inventorymanager/vendor');
		$products = $vendorModel->getAllProducts();
		return $products;
	}
}