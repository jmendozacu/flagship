<?php
class Ecommerceguys_Inventorymanager_Block_Vendor_Product_Serialgrid extends Mage_Core_Block_Template
{
	public function getVendorProduct(){
		$productId = $this->getRequest()->getParam('product_id');
		$vendorId = $this->getCurrentVendorId();
		
		$purchaseorders = $this->getVendorOrders();
		
		$orderProducts = Mage::getModel('inventorymanager/product')->getCollection();
		$orderProducts->addFieldToFilter('po_id', array('in'=>$purchaseorders));
		$orderProducts->addFieldToFilter('main_product_id', $productId);
		return $orderProducts;
	}
	
	public function getVendorOrders(){
		$vendorId = $this->getCurrentVendorId();
		$purchaseorders = Mage::getModel('inventorymanager/purchaseorder')->getCollection();
		$purchaseorders->addFieldToFilter('vendor_id', $vendorId);
		return $purchaseorders->getAllIds();
	}
	
	public function getCurrentVendorId(){
		$vendor = Mage::getSingleton('core/session')->getVendor();
		if($vendor && $vendor->getId()){
			return $vendor->getId();
		}else{
			return false;
		}
	}
	
	public function getSerials($orderProductId){
		$serials = Mage::getModel('inventorymanager/label')->getCollection();
		$serials->addFieldToFilter('product_id', $orderProductId);
		$serials->addFieldToFilter('is_in_stock', 1);
		return $serials;
	}
	
	public function getCatalogProduct(){
		$productId = $this->getRequest()->getParam('product_id');
		return Mage::getModel('catalog/product')->load($productId);
	}
}