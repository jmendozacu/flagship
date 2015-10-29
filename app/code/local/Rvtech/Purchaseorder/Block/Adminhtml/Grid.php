<?php
class Rvtech_Purchaseorder_Block_Adminhtml_Grid extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
     $this->_controller = 'adminhtml_purchaseorder';
     $this->_blockGroup = 'purchaseorder';
     $this->_headerText = 'Purchase Order Serials';
     $this->_addButtonLabel = 'Purchase Order Serials';
     parent::__construct();
     $this->_removeButton('add');
     }
}