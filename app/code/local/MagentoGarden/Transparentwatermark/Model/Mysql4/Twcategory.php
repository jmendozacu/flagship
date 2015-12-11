<?php

class MagentoGarden_Transparentwatermark_Model_Mysql4_Twcategory extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the smartlabel_id refers to the key field in your database table.
        $this->_init('transparentwatermark/twcategory', 'entity_id');
    }
}
