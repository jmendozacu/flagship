<?php

class Ecommerceguys_Inventorymanager_Block_User_Purchaseorder_Findproduct extends Mage_Core_Block_Template
{
	public function getMatchingProduct(){
		$postData = $this->getRequest()->getPost();
		/*$searchKeyword = "";
		if(isset($postData['keyword']) && $postData['keyword'] != ""){
			$searchKeyword = $postData['keyword'];
		}else{
			return null;
		}*/
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
		$productCollection = Mage::getModel('catalog/product')->getCollection();
		$productCollection->addAttributeToSelect('sku');
		$productCollection->addAttributeToSelect('name');
		//$productCollection->addAttributeToFilter('sku', array('like'=>$searchKeyword.'%'));
		/*$productCollection->addAttributeToFilter(
			array(
				array('attribute'=>'name', 'like'=>$searchKeyword.'%'),
				array('attribute'=>'sku', 'like'=>$searchKeyword.'%')
			)
		);*/
		
		$vendorProducttable = Mage::getSingleton('core/resource')->getTableName('inventorymanager_vendorproduct');
		
		$productCollection->getSelect()->joinLeft(array('vp'=>$vendorProducttable), "e.entity_id = vp.product_id")
			->where("vp.vendor_id = ".$postData['vendor_id']);
		
		return $productCollection;
	}
}