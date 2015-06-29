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
    	
    	$vendorId = Mage::getSingleton('core/session')->getVendor()->getId();
    	
    	$select = $readConnection->select()
                ->from(array('status' => $tableName))
                ->where("status.vendor_id = ?", $vendorId);
        return $readConnection->fetchAll($select);
    }
    
    public function addStatus($status){
    	$resource = Mage::getSingleton('core/resource');
    	$tableName = $resource->getTableName('inventorymanager_purchaseorder_label_status');
    	$writeConnection = $resource->getConnection('core_write');
    	
    	$vendorId = Mage::getSingleton('core/session')->getVendor()->getId();
    	
    	$data = array('vendor_id' => $vendorId, 'status' => $status);
    	try {
    		$writeConnection->insert($tableName, $data);
    	}catch (Exception $e){
    		
    	}
    }
    
    
    public function removeStatus($status){
    	$resource = Mage::getSingleton('core/resource');
    	$tableName = $resource->getTableName('inventorymanager_purchaseorder_label_status');
    	$writeConnection = $resource->getConnection('core_write');
    	
    	$vendorId = Mage::getSingleton('core/session')->getVendor()->getId();
    	$whereCondition = $writeConnection->quoteInto('vendor_id=? AND status = "'.$status.'"', $vendorId);
    	try {
    		$writeConnection->delete($tableName, $whereCondition);
    	}catch (Exception $e){
    		
    	}
    }
}