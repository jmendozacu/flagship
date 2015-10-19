<?php
class Ecommerceguys_Inventorymanager_Model_Resource_Label extends Mage_Core_Model_Resource_Db_Abstract
{
	public function _construct()
    {    
        $this->_init('inventorymanager/label', 'label_id');
    }
    
    
    
    public function getStatuses(){
    	$resource = Mage::getSingleton('core/resource');
    	$tableName = $resource->getTableName('inventorymanager_purchaseorder_label_status');
    	$readConnection = $resource->getConnection('core_read');

    	$select = $readConnection->select()
                ->from(array('status' => $tableName));
		
		$vendor = Mage::getSingleton('core/session')->getVendor();
        if($vendor && $vendor->getId()){
        	$vendorId = $vendor->getId();
        }else{
    		$vendorId = Mage::helper('inventorymanager')->getVendorFromRequest();
    	}
    	if($vendorId > 0){
        	$select->where("status.vendor_id = ?", $vendorId);
    	}
    	return $readConnection->fetchAll($select);
    }
    
    public function addStatus($status){
    	$resource = Mage::getSingleton('core/resource');
    	$tableName = $resource->getTableName('inventorymanager_purchaseorder_label_status');
    	$writeConnection = $resource->getConnection('core_write');
    	
    	$vendor = Mage::getSingleton('core/session')->getVendor();
        if($vendor && $vendor->getId()){
			$vendorId = $vendor->getId();
        }else{
    		$vendorId = Mage::helper('inventorymanager')->getVendorFromRequest();
    	}
    	if($vendorId > 0){
	    	$data = array('vendor_id' => $vendorId, 'status' => $status);
	    	try {
	    		$writeConnection->insert($tableName, $data);
	    	}catch (Exception $e){
	    		Mage::log($e->getMessage());
	    	}
    	}
    }
    
    
    public function removeStatus($status){
    	$resource = Mage::getSingleton('core/resource');
    	$tableName = $resource->getTableName('inventorymanager_purchaseorder_label_status');
    	$writeConnection = $resource->getConnection('core_write');
    	
    	$vendor = Mage::getSingleton('core/session')->getVendor();
        if($vendor && $vendor->getId()){
			$vendorId = $vendor->getId();
        }else{
    		$vendorId = Mage::helper('inventorymanager')->getVendorFromRequest();
    	}
    	if($vendorId > 0){
	    	$whereCondition = $writeConnection->quoteInto('vendor_id=? AND status = "'.$status.'"', $vendorId);
	    	try {
	    		$writeConnection->delete($tableName, $whereCondition);
	    	}catch (Exception $e){
	    		Mage::log($e->getMessage());
	    	}
    	}
    }
    
    public function getLocations(){
    	$resource = Mage::getSingleton('core/resource');
    	$tableName = $resource->getTableName('inventorymanager_purchaseorder_label_location');
    	$readConnection = $resource->getConnection('core_read');
    	
    	$select = $readConnection->select()
                ->from(array('location' => $tableName));
    	
    	$vendor = Mage::getSingleton('core/session')->getVendor();
        if($vendor && $vendor->getId()){
			$vendorId = $vendor->getId();
        }else{
    		$vendorId = Mage::helper('inventorymanager')->getVendorFromRequest();
    	}
    	if($vendorId > 0){
        	//$select->where("location.vendor_id = ? OR location.vendor_id = 0", $vendorId);
        	$select->where("location.vendor_id = ? ", 0); 
    	}
    	
    	/*$deletedLocationArray = array();
    	if($vendorId > 0){
	    	$deletedLocations = Mage::getModel('inventorymanager/vendor_deletedlocation')->getCollection();
	    	$deletedLocations->addFieldToFilter('vendor_id', $vendorId);
	    	foreach ($deletedLocations as $dLocation){
	    		$deletedLocationArray[] = $dLocation->getLocation();
	    	}
    	}*/
    	
    	return  $readConnection->fetchAll($select);
    	/*$locationsToDisplay = array();
    	foreach ($currentLocations as $cl){
    		if(!in_array($cl['location'], $deletedLocationArray)){
    			$locationsToDisplay[] = $cl;
    		}
    	}
    	return $locationsToDisplay;*/
    }
    
    public function addLocation($location){
    	$locations = Mage::helper('inventorymanager')->getLocations();
    	if(in_array($location, $locations)){
    		return $this;
    	}
    	$resource = Mage::getSingleton('core/resource');
    	$tableName = $resource->getTableName('inventorymanager_purchaseorder_label_location');
    	$writeConnection = $resource->getConnection('core_write');
    	
    	$vendor = Mage::getSingleton('core/session')->getVendor();
    	if($vendor && $vendor->getId()){
    		$vendorId = $vendor->getId();
    	}else{
    		$vendorId = Mage::helper('inventorymanager')->getVendorFromRequest();
    	}
    	if($vendorId > 0){
	    	$data = array('vendor_id' => $vendorId, 'location' => $location);
	    	try {
	    		$writeConnection->insert($tableName, $data);
	    	}catch (Exception $e){
	    		Mage::log($e->getMessage());
	    	}
    	}
    }
    
    public function removeLocation($location){
    	$resource = Mage::getSingleton('core/resource');
    	$tableName = $resource->getTableName('inventorymanager_purchaseorder_label_location');
    	$writeConnection = $resource->getConnection('core_write');
    	
    	$select = $writeConnection->select('*')
                ->from(array('location' => $tableName))
                ->where("location.location = '$location'");
        
    	$locationDetail = $writeConnection->fetchOne($select);
    	
    	$vendor = Mage::getSingleton('core/session')->getVendor();
    	if($vendor && $vendor->getId()){
    		$vendorId = $vendor->getId();
    	}else{
    		$vendorId = Mage::helper('inventorymanager')->getVendorFromRequest();
    	}
    	
    	if($vendorId > 0 && $locationDetail == 0){
    		
    		$deletedLocationCollection = Mage::getModel('inventorymanager/vendor_deletedlocation')->getCollection();
    		$deletedLocationCollection->addFieldToFilter('location', $location);
    		$deletedLocationCollection->addFieldToFilter('vendor_id', $vendorId);
    		
    		if($deletedLocationCollection && $deletedLocationCollection->count() == 0){
	    		$deletedLocationModel = Mage::getModel('inventorymanager/vendor_deletedlocation');
	    		$deletedLocationModel->setVendorId($vendorId);
	    		$deletedLocationModel->setLocation($location);
	    		try{
	    			$deletedLocationModel->save();
	    		}catch (Exception $e){
	    			Mage::log($e);
	    			Mage::throwException($e->getMessage());
	    		}
    		}
    		return $this;
    	}
    	
    	if($vendorId > 0){
	    	$whereCondition = $writeConnection->quoteInto('vendor_id=? AND location = "'.$location.'"', $vendorId);
	    	try {
	    		$writeConnection->delete($tableName, $whereCondition);
	    	}catch (Exception $e){
	    		Mage::log($e->getMessage());
	    	}
    	}
    }
    
    public function getLocationsForAgent(){
    	$resource = Mage::getSingleton('core/resource');
    	$tableName = $resource->getTableName('inventorymanager_purchaseorder_label_location');
    	$readConnection = $resource->getConnection('core_read');
    	
    	$select = $readConnection->select()
                ->from(array('location' => $tableName));
		$vendor = 0;
        $select->where("location.vendor_id = ?", $vendorId);
    	return $readConnection->fetchAll($select);
    }
    
    public function addLocationFromAgent($data){
    	$resource = Mage::getSingleton('core/resource');
    	$tableName = $resource->getTableName('inventorymanager_purchaseorder_label_location');
    	$writeConnection = $resource->getConnection('core_write');
    	try {
    		$writeConnection->insert($tableName, $data);
    	}catch (Exception $e){
    		Mage::log($e->getMessage());
    	}
    }
    
    public function removeLocationFromAgent($location){
    	$resource = Mage::getSingleton('core/resource');
    	$tableName = $resource->getTableName('inventorymanager_purchaseorder_label_location');
    	$writeConnection = $resource->getConnection('core_write');
    	$whereCondition = $writeConnection->quoteInto('vendor_id=? AND location = "'.$location.'"', 0);
    	try {
    		$writeConnection->delete($tableName, $whereCondition);
    	}catch (Exception $e){
    		Mage::log($e->getMessage());
    	}
    }
}