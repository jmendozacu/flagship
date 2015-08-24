<?php 
class Ecommerceguys_Inventorymanager_Block_Vendor_Product_Stock extends Mage_Core_Block_Template
{
	public function getVendorProducts(){
		$vendorId = $this->getCurrentVendorId();
		$vendorModel = Mage::getResourceModel('inventorymanager/vendor');
		$products = $vendorModel->getProducts($vendorId);
		return $products;
	}
	
	public function getCurrentVendorId(){
		$vendor = Mage::getSingleton('core/session')->getVendor();
		if($vendor && $vendor->getId()){
			return $vendor->getId();
		}else{
			return false;
		}
	}
	
	public function getCurrentVendor(){
		$vendor = Mage::getSingleton('core/session')->getVendor();
		if($vendor && $vendor->getId()){
			return $vendor;
		}else{
			return false;
		}
	}
	
	public function getShippedSerials($productId){
		$orderProducts = Mage::getModel('inventorymanager/product')->getCollection();
		$orderProducts->addFieldToFilter('main_product_id', $productId);
		$stock = 0;
		foreach ($orderProducts as $orderProduct){
			$labels = Mage::getModel('inventorymanager/label')->getCollection();
			$labels->addFieldToFilter('product_id', $orderProduct->getId());
			$labels->addFieldToFilter('order_id', $orderProduct->getPoId());
			$labels->addFieldToFilter('is_out_stock', 1);
			$stock += $labels->count();
		}
		return $stock;
	}
}