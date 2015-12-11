<?php 

class Ecommerceguys_Inventorymanager_Model_Resource_Vendor_Deletedlocation extends Mage_Core_Model_Resource_Db_Abstract
{
	public function _construct()
    {    
        $this->_init('inventorymanager/vendor_deletedlocation', 'deleted_location_id');
    }
}