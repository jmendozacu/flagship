<?php
class Ecommerceguys_Inventorymanager_Block_Adminhtml_Purchaseorder extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_purchaseorder';
    $this->_blockGroup = 'inventorymanager';
    $this->_headerText = Mage::helper('inventorymanager')->__('Purchase Orders');
    $this->_addButtonLabel = Mage::helper('inventorymanager')->__('Add');
    parent::__construct();
  }
}