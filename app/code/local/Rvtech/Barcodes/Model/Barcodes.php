<?php
class Rvtech_Barcodes_Model_Barcodes extends Mage_Core_Model_Abstract
{
     public function _construct()
     {
         parent::_construct();
         $this->_init('barcodes/barcodes');
     }
}