<?php

class Ecommerceguys_Inventorymanager_Block_Adminhtml_Purchaseorder_Purchaseorder extends Mage_Core_Block_Template
{
	public function getProductsJson(){
		$poId = $this->getRequest()->getParam('id', 0);
	
		$purchaseorder = Mage::getModel('inventorymanager/purchaseorder')->load($poId);
		
		$purchaseorderProducts = Mage::getModel('inventorymanager/product')->getCollection();
		$purchaseorderProducts->addFieldToFilter('po_id', $poId);
		$products = $purchaseorderProducts->getAllIds();
		
		if (!empty($products)) {
            return Mage::helper('core')->jsonEncode($products);
        }
        return '{}';
	}
}