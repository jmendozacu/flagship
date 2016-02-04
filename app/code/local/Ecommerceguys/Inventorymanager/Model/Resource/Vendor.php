<?php

class Ecommerceguys_Inventorymanager_Model_Resource_Vendor extends Mage_Core_Model_Resource_Db_Abstract
{
    public function _construct()
    {    
        $this->_init('inventorymanager/vendor', 'vendor_id');
    }
    
    public function getResourceObject(){
    	return Mage::getSingleton('core/resource');
    }
    
    public function getProducts($vendorId){
    	
    	$resourceObject = $this->getResourceObject();

    	
    	$vendorProductTable = $resourceObject->getTableName('inventorymanager_vendorproduct');
    	$vendorProductInfoTable = $resourceObject->getTableName('inventorymanager_vendor_productdetail');
    	$collection = Mage::getModel('catalog/product')->getCollection();
    	$collection->addAttributeToSelect(array('name', 'status'));
    	$collection->addAttributeToFilter('status', array('eq' => 1));
    	$select = $collection->getSelect()
                ->join(array("vp"=>$vendorProductTable), "e.entity_id = vp.product_id", array('vendor_id'))
                ->joinLeft(array("vpi"=>$vendorProductInfoTable), "e.entity_id = vpi.product_id", array('cost'))
                ->where("vp.vendor_id = " . $vendorId . " AND vp.vendor_id = " . $vendorId . " AND is_revision = 0" )
                ->group('e.entity_id'); 
         
    	return $collection;
    	
    }
    
    
    public function getMaterial($vendorId){
    	$resource = $this->getResourceObject();
    	$tableName = $resource->getTableName('inventorymanager_vendor_product_material');
    	$readConnection = $resource->getConnection('core_read');
    	
    	$select = $readConnection->select()
                ->from(array('material' => $tableName))
                ->where("material.vendor_id = ?", $vendorId);
        return $readConnection->fetchAll($select);
    }
    
    
    public function getLighting($vendorId){
    	$resource = $this->getResourceObject();
    	$tableName = $resource->getTableName('inventorymanager_vendor_product_lighting');
    	$readConnection = $resource->getConnection('core_read');
    	
    	$select = $readConnection->select()
                ->from(array('lighting' => $tableName))
                ->where("lighting.vendor_id = ?", $vendorId);
        return $readConnection->fetchAll($select);
    }
    
    public function addMaterial($material){
    	$addedMaterials = Mage::helper('inventorymanager')->getVendorMaterials();
    	if(in_array($material, $addedMaterials)){
    		return $this;
    	}
    	$vendorId = Mage::getSingleton('core/session')->getVendor()->getId();
    	$resource = $this->getResourceObject();
    	$tableName = $resource->getTableName('inventorymanager_vendor_product_material');
    	$writeConnection = $resource->getConnection('core_write');
    	$data = array('vendor_id' => $vendorId, 'material' => $material);
    	try {
    		$writeConnection->insert($tableName, $data);
    	}catch (Exception $e){
    		Mage::log($e->getMessage());
    	}
    }
    
    public function removeMaterial($material){
    	$vendorId = Mage::getSingleton('core/session')->getVendor()->getId();
    	$resource = $this->getResourceObject();
    	$tableName = $resource->getTableName('inventorymanager_vendor_product_material');
    	$writeConnection = $resource->getConnection('core_write');
    	$whereCondition = $writeConnection->quoteInto('vendor_id=? AND material = "'.$material.'"', $vendorId);
    	try {
    		$writeConnection->delete($tableName, $whereCondition);
    	}catch (Exception $e){
    		Mage::log($e->getMessage());
    	}
    }
    
    public function addLighting($lighting){
    	$addedLightings = Mage::helper('inventorymanager')->getVendorLighting();
    	if(in_array($lighting, $addedLightings)){
    		return $this;
    	}
    	$vendorId = Mage::getSingleton('core/session')->getVendor()->getId();
    	$resource = $this->getResourceObject();
    	$tableName = $resource->getTableName('inventorymanager_vendor_product_lighting');
    	$writeConnection = $resource->getConnection('core_write');
    	$data = array('vendor_id' => $vendorId, 'lighting' => $lighting);
    	try {
    		$writeConnection->insert($tableName, $data);
    	}catch (Exception $e){
    		Mage::log($e->getMessage());
    	}
    }
    
    public function removeLighting($lighting){
    	$vendorId = Mage::getSingleton('core/session')->getVendor()->getId();
    	$resource = $this->getResourceObject();
    	$tableName = $resource->getTableName('inventorymanager_vendor_product_lighting');
    	$writeConnection = $resource->getConnection('core_write');
    	$whereCondition = $writeConnection->quoteInto('vendor_id=? AND lighting = "'.$lighting.'"', $vendorId);
    	try {
    		$writeConnection->delete($tableName, $whereCondition);
    	}catch (Exception $e){
    		Mage::log($e->getMessage());
    	}
    }
    
    public function getAllProducts(){
    	$resourceObject = $this->getResourceObject();
    	$productTable = $resourceObject->getTableName('catalog_product_entity');
    	$vendorProductTable = $resourceObject->getTableName('inventorymanager_vendorproduct');
    	$connection = $resourceObject->getConnection('core_read');
    	$select = $connection->select()
                ->from(array("e"=>$productTable))
                ->join(array("vp"=>$vendorProductTable), "e.entity_id = vp.product_id",array('vendor_id'))
                ->group('entity_id');
         
    	return $connection->fetchAll($select);
    }
    
    public function getAllCatalogProducts(){
    	$resourceObject = $this->getResourceObject();
    	$productTable = $resourceObject->getTableName('catalog_product_entity');
    	$connection = $resourceObject->getConnection('core_read');
    	$select = $connection->select()
                ->from(array("e"=>$productTable))
                ->order(array('created_at DESC'))
                ->group('entity_id');
         
    	return $connection->fetchAll($select);
    }
    
    public function getUnselectedProducts($selectedProducts){
    	
    	$resourceObject = $this->getResourceObject();

    	
    	$vendorProductTable = $resourceObject->getTableName('inventorymanager_vendorproduct');
    	$collection = Mage::getModel('catalog/product')->getCollection();
    	$collection->addAttributeToSelect(array('name', 'status'));
    	$collection->addAttributeToFilter('status', array('eq' => 1));
    	if(is_array($selectedProducts) && sizeof($selectedProducts) > 0)
    		$collection->addFieldToFilter('entity_id', array('nin' => $selectedProducts));
         
    	return $collection;
    }
    
    public function filterVendorProductArray($value){
    	if(isset($value['entity_id'])){
    		return $value['entity_id'];
    	}
    }
}