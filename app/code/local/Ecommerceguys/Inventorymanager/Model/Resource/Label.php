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
                ->where("status.vendor_id = ? OR status.vendor_id = 0", $vendorId);
        return $readConnection->fetchAll($select);
    }
}