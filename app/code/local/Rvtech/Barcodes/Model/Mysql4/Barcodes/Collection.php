<?php
class Rvtech_Barcodes_Model_Mysql4_Barcodes_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
 {
     public function _construct()
     {
         parent::_construct();
         $this->_init('barcodes/barcodes');
     }
}