<?php 
class Ecommerceguys_Inventorymanager_Model_Vendor_Productinfo extends Mage_Core_Model_Abstract
{
	public function _construct()
    {
        parent::_construct();
        $this->_init('inventorymanager/vendor_productinfo');
    }
    
    public function setRevision($vendorId, $productId){
    	$productInfoCollection = $this->getCollection();
    	$productInfoCollection->addFieldToFilter('vendor_id', $vendorId);
    	$productInfoCollection->addFieldToFilter('product_id', $productId);
    	foreach ($productInfoCollection as $productInfo){
    		$productInfo->setIsRevision(1)->save();
    	}
    }
    
    public function getActiveObject($vendorId, $productId){
    	$productInfoCollection = $this->getCollection();
    	$productInfoCollection->addFieldToFilter('vendor_id', $vendorId);
    	$productInfoCollection->addFieldToFilter('product_id', $productId);
    	$productInfoCollection->addFieldToFilter('is_revision', 0);
    	if($productInfoCollection->count() > 0){
    		return $productInfoCollection->getFirstItem();
    	}
    	return false;
    }
}