<?php
class Ecommerceguys_Inventorymanager_Block_User_Product_Stockgrid extends Mage_Core_Block_Template
{
	public function getProducts(){
		
		//$products = Mage::getModel('catalog/product')->getCollection();
		
		$vendorModel = Mage::getResourceModel('inventorymanager/vendor');
		$products = $vendorModel->getAllProducts();
		return $products;
	}
	
	public function getSerialCount($product){
		$serialCount = 0;
		$orderProduct = Mage::getModel('inventorymanager/product')->getCollection();
		$orderProduct->addFieldToFilter('main_product_id', $product->getId());
		foreach ($orderProduct as $orderP){
			$serialCollection = Mage::getModel('inventorymanager/label')->getCollection();
			$serialCollection->addFieldToFilter('product_id', $orderP->getId());
			$serialCollection->addFieldToFilter('is_in_stock', 1);
			if($serialCollection && $serialCollection->count() > 0){
				$serialCount += $serialCollection->count();
			}
		}
		return $serialCount;
	}
}