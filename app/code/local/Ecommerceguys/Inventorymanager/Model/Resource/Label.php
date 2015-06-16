<?php
class Ecommerceguys_Inventorymanager_Model_Resource_Label extends Mage_Core_Model_Resource_Db_Abstract
{
	public function _construct()
    {    
        $this->_init('inventorymanager/label', 'label_id');
    }
}