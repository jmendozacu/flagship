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
	public function getProducts($selectedProducts){
		
		
		$id = $this->getRequest()->getParam('vendor_id');
		$products = Mage::getResourceModel('inventorymanager/vendor')->getUnselectedProducts($id, $selectedProducts);
		
		return $products;
		
	/*return $collection = Mage::getModel('catalog/product')
            ->getCollection()
            //->setProduct($this->_getProduct())
            ->addAttributeToSelect('*')
            ->addAttributeToSort('entity_id', 'DESC');*/
		//$vendorId = $this->getRequest()->getParam('vendor_id');
	
		//$vendorModel = Mage::getResourceModel('inventorymanager/vendor');
		
		//$products = $vendorModel->getUnselectedProducts($vendorId);
		//return $products;
        }
	public function getVendorProducts($id){
		
		if(!$id){
			return;
		}
		
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
		
		$products = Mage::getResourceModel('inventorymanager/vendor')->getProducts($id);
		
		return $products;
	}
}