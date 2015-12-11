<?php

class MagentoGarden_Transparentwatermark_Model_Mysql4_Twcategory_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('transparentwatermark/twcategory');
    }
}
