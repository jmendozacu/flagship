<?php

class Ecommerceguys_Inventorymanager_Model_Resource_Shipmanager extends Mage_Core_Model_Resource_Db_Abstract
{
    public function _construct()
    {    
        $this->_init('inventorymanager/shipmanager', 'history_id'); 
    }
}