<?php 

class Ecommerceguys_Inventorymanager_Block_User_Vendor extends Mage_Core_Block_Template
{
	public function getAllVendors(){
		$vendors = Mage::getModel('inventorymanager/vendor')->getCollection();
		return $vendors;
	}
public function getPostUrl($id){
		return Mage::getUrl('inventorymanager/adminuser/vendorsave',array('id'=>$id));	
	}
public function getProducts(){
	/*return $collection = Mage::getModel('catalog/product')
            ->getCollection()
            //->setProduct($this->_getProduct())
            ->addAttributeToSelect('*')
            ->addAttributeToSort('entity_id', 'DESC');*/
	
	
			$vendorModel = Mage::getResourceModel('inventorymanager/vendor');
		
			$products = $vendorModel->getAllCatalogProducts();
		return $products;
        }
public function getVendorProducts($id){
		if(!$id){
			return;
		}
		$products = Mage::getResourceModel('inventorymanager/vendor')->getProducts($id);

		//echo "<pre>";
		//print_r($products);
		foreach($products as $pid) {
			$returnIds[] = $pid['entity_id'];
        }
		return $returnIds;
	}
}