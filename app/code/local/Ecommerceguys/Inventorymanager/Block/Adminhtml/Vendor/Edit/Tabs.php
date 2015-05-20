<?php

class Ecommerceguys_Inventorymanager_Block_Adminhtml_Vendor_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('vendor_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('inventorymanager')->__('Vendor Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('inventorymanager')->__('Vendor Information'),
          'title'     => Mage::helper('inventorymanager')->__('Vendor Information'),
          'content'   => $this->getLayout()->createBlock('inventorymanager/adminhtml_vendor_edit_tab_form')->toHtml(),
      ));
      
      $this->addTab('products', array(
          'label'     => Mage::helper('inventorymanager')->__('Vendor Products'),
          'title'     => Mage::helper('inventorymanager')->__('Vendor Products'),
          //'content'   => $this->getLayout()->createBlock('inventorymanager/adminhtml_vendor_edit_tab_products')->toHtml(),
          'url'   	  => Mage::helper('adminhtml')->getUrl('*/*/products'),
          'class'     => 'ajax',
      ));
     
      return parent::_beforeToHtml();
  }
}