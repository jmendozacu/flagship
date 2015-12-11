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
	
	public function getSold($productId){
		$_product = Mage::getModel('catalog/product')->load($productId);
		$resource = Mage::getSingleton('core/resource');
    	$tableName = $resource->getTableName('inventorymanager_purchaseorder_label');
		$orderItems = Mage::getModel('sales/order_item')->getCollection();
		
		$orderItems->getSelect()->joinLeft(array('lt'=>$tableName), "main_table.order_id = lt.real_order_id", array('real_order_id'=>'real_order_id'))
		->where('(lt.real_order_id = 0 OR lt.real_order_id = "") AND main_table.product_id = ' . $productId)
		->columns('SUM(qty_ordered) as total');
		;
		return (int)$orderItems->getFirstitem()->getTotal();
		
		/*$sku = nl2br($_product->getSku());
			 $product = Mage::getResourceModel('reports/product_collection')
			 ->addOrderedQty()
			 ->addAttributeToFilter('sku', $sku)
			->setOrder('ordered._qty', 'desc')
			->getFirstItem()
			;
			
			print_r($product->getData());
		return (int)$product->getOrderedQty();*/
	}
	
	public function getOnTheWay($productId){
		
		$purchaseorderProducts = Mage::getModel('inventorymanager/product')->getCollection();
		$purchaseorderProducts->addFieldToFilter('main_product_id', $productId);
		$purchaseorderProducts->getSelect()->columns('SUM(qty) as total');
		return (int)$purchaseorderProducts->getFirstitem()->getTotal();
	}
}