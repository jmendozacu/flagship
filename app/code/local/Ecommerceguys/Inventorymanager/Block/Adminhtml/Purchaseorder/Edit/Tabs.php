<?php

class Ecommerceguys_Inventorymanager_Block_Adminhtml_Purchaseorder_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('purchaseorder_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('inventorymanager')->__('Purchase Order Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('inventorymanager')->__('Information'),
          'title'     => Mage::helper('inventorymanager')->__('Information'),
          'content'   => $this->getLayout()->createBlock('inventorymanager/adminhtml_purchaseorder_edit_tab_form')->toHtml(),
      ));
      
      $this->addTab('products', array(
          'label'     => Mage::helper('inventorymanager')->__('Products'),
          'title'     => Mage::helper('inventorymanager')->__('Products'),
          'content'   => $this->getLayout()->createBlock('inventorymanager/adminhtml_purchaseorder_edit_tab_Product')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}