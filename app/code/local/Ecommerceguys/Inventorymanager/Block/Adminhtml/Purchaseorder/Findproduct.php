<?php

class Ecommerceguys_Inventorymanager_Block_Adminhtml_Purchaseorder_Findproduct extends Mage_Core_Block_Template
{
	public function getMatchingProduct(){
		$postData = $this->getRequest()->getPost();
		$searchKeyword = "";
		if(isset($postData['keyword']) && $postData['keyword'] != ""){
			$searchKeyword = $postData['keyword'];
		}else{
			return null;
		}
		$productCollection = Mage::getModel('catalog/product')->getCollection();
		$productCollection->addAttributeToSelect('name');
		$productCollection->addAttributeToFilter('name', array('like'=>$searchKeyword.'%'));
		return $productCollection;
	}
}