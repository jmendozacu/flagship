<?php
class Rvtech_Barcodes_Block_Adminhtml_Barcodes_Edit extends
                    Mage_Adminhtml_Block_Widget_Form_Container{
   public function __construct()
   {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'barcodes';
        $this->_controller = 'adminhtml_barcodes';
        //define the label for the save and delete button
        $this->_updateButton('save', 'label','Save Serial');
        $this->_updateButton('delete', 'label', 'Delete Serial');
    }
    public function getHeaderText()
    {
        if( Mage::registry('barcodes_data')&&Mage::registry('barcodes_data')->getId())
         {
              return 'Edit serial '.$this->htmlEscape(
              Mage::registry('barcodes_data')->getTitle()).'<br />';
         }
         else
         {
             return 'Generate Serials';
         }
    }
}