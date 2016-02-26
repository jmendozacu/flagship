<?php
class Ecommerceguys_Inventorymanager_Block_Adminhtml_Vendor extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_vendor';
    $this->_blockGroup = 'inventorymanager';
    $this->_headerText = Mage::helper('inventorymanager')->__('Manage Vendor');
    $this->_addButtonLabel = Mage::helper('inventorymanager')->__('Add');
    parent::__construct();
  }
}