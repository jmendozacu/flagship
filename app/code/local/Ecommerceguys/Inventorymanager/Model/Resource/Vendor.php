<?php

class Ecommerceguys_Inventorymanager_Model_Resource_Vendor extends Mage_Core_Model_Resource_Db_Abstract
{
    public function _construct()
    {    
        $this->_init('inventorymanager/vendor', 'vendor_id');
    }
    
    public function getProducts($vendorId){
    	
    	$resourceObject = Mage::getSingleton('core/resource');
    	$productTable = $resourceObject->getTableName('catalog_product_entity');
    	$vendorProductTable = $resourceObject->getTableName('inventorymanager_vendorproduct');
    	
    	$connection = $resourceObject->getConnection('core_read');
    	
    	$select = $connection->select()
                ->from(array("e"=>$productTable))
                ->join(array("vp"=>$vendorProductTable), "e.entity_id = vp.product_id",array('vendor_id'))
                ->where("vendor_id = " . $vendorId);  
         
    	return $connection->fetchAll($select);
    	
    }
}