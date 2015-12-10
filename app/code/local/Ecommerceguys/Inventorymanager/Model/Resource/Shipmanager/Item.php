<?php

class Ecommerceguys_Inventorymanager_Model_Resource_Shipmanager_Item extends Mage_Core_Model_Resource_Db_Abstract
{
    public function _construct()
    {    
        $this->_init('inventorymanager/shipmanager_item', 'item_id'); 
    }
}