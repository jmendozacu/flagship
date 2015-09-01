<?php 

class Ecommerceguys_Inventorymanager_Model_Resource_Label_History extends Mage_Core_Model_Resource_Db_Abstract
{
	public function _construct()
    {    
        $this->_init('inventorymanager/label_history', 'history_id');
    }
}