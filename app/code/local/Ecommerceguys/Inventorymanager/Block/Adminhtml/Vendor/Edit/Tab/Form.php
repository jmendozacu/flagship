<?php

class Ecommerceguys_Inventorymanager_Block_Adminhtml_Vendor_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('vendor_form', array('legend'=>Mage::helper('inventorymanager')->__('Information')));
     
      $fieldset->addField('name', 'text', array(
          'label'     => Mage::helper('inventorymanager')->__('Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'name',
      ));
      
      $fieldset->addField('email', 'text', array(
          'label'     => Mage::helper('inventorymanager')->__('Vendor Email'),
          'class'     => 'required-entry validate-email',
          'required'  => true,
          'name'      => 'email',
      ));
     
      $fieldset->addField('vendor_code', 'text', array(
          'label'     => Mage::helper('inventorymanager')->__('Vendor Code'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'vendor_code',
      ));
      
      $fieldset->addField('username', 'text', array(
          'label'     => Mage::helper('inventorymanager')->__('Username'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'username',
      ));
      
      $fieldset->addField('password', 'password', array(
          'label'     => Mage::helper('inventorymanager')->__('Password'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'password',
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getVendorData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getVendorData());
          Mage::getSingleton('adminhtml/session')->setVendorData(null);
      } elseif ( Mage::registry('vendor_data') ) {
          $form->setValues(Mage::registry('vendor_data')->getData());
      }
      return parent::_prepareForm();
  }
}