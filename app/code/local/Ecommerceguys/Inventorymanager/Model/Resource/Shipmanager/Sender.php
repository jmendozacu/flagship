<?php

class Ecommerceguys_Inventorymanager_Model_Resource_Shipmanager_Sender extends Mage_Core_Model_Resource_Db_Abstract
{
    public function _construct()
    {    
        $this->_init('inventorymanager/shipmanager_sender', 'address_id'); 
    }
}