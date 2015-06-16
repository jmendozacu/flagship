<?php 

class Ecommerceguys_Inventorymanager_Model_Resource_Label_Comment extends Mage_Core_Model_Resource_Db_Abstract
{
	public function _construct()
    {    
        $this->_init('inventorymanager/comment_label', 'comment_id');
    }
}