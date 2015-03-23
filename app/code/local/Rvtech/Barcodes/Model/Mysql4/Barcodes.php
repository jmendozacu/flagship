<?php
class Rvtech_Barcodes_Model_Mysql4_Barcodes extends Mage_Core_Model_Mysql4_Abstract
{
     public function _construct()
     {
         $this->_init('barcodes/barcodes', 'id');
     }
}