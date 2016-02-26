<?php

class Ecommerceguys_Inventorymanager_Block_Adminhtml_Vendor_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'inventorymanager';
        $this->_controller = 'adminhtml_vendor';
        
        $this->_updateButton('save', 'label', Mage::helper('inventorymanager')->__('Save Vendor'));
        $this->_updateButton('delete', 'label', Mage::helper('inventorymanager')->__('Delete Vendor'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('vendor_data') && Mage::registry('vendor_data')->getId() ) {
            return Mage::helper('inventorymanager')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('vendor_data')->getName()));
        } else {
            return Mage::helper('inventorymanager')->__('Add Vendor');
        }
    }
}