<?php
class Rvtech_Barcodes_Block_Adminhtml_Grid extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
     $this->_controller = 'adminhtml_barcodes';
     $this->_blockGroup = 'barcodes';
     $this->_headerText = 'Serials Management';
     $this->_addButtonLabel = 'Generate Serials';
     parent::__construct();
     }
}