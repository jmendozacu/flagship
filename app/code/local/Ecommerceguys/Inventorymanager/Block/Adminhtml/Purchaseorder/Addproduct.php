<?php

class Ecommerceguys_Inventorymanager_Block_Adminhtml_Purchaseorder_Addproduct extends Mage_Core_Block_Template
{
	public function getProduct(){
		$postData = $this->getRequest()->getPost();
		return Mage::getModel('catalog/product')->load($postData['product_id']);
	}
}