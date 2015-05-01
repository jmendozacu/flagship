<?php

class Ecommerceguys_Inventorymanager_Block_Adminhtml_Purchaseorder_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('purchaseorder_form', array('legend'=>Mage::helper('inventorymanager')->__('Information')));
     
      $vendors = array();
      $vendorsCollection = Mage::getModel('inventorymanager/vendor')->getCollection();
      foreach ($vendorsCollection as $vendor){
      	$vendors[$vendor->getId()] = $vendor->getName();
      }
      
      $fieldset->addField('vendor_id', 'select', array(
          'label'     => Mage::helper('inventorymanager')->__('Vendors'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'vendor_id',
          'values'	=> $vendors,
      ));
      
      $methods = Mage::getSingleton('shipping/config')->getActiveCarriers();
      
	  $shippingMethods = array();
	  foreach($methods as $_code => $_method)
	 	{
	 		if(!$_title = Mage::getStoreConfig("carriers/$_code/title")){
	            $_title = $_code;
	 		}
	      	$shippingMethods[$_code] = $_title;
	 	}
      $fieldset->addField('shipping_method', 'select', array(
          'label'     => Mage::helper('inventorymanager')->__('Shipping Method'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'shipping_method',
          'values'	=> $shippingMethods,
      ));
     
      $paymentTerms = Mage::getModel('inventorymanager/paymentterms')->toOptionArray();
      $fieldset->addField('payment_terms', 'select', array(
          'label'     => Mage::helper('inventorymanager')->__('Payment Terms'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'payment_terms',
          'values'	=>	$paymentTerms,
      ));
      
      $fieldset->addField('po_notes', 'textarea', array(
          'label'     => Mage::helper('inventorymanager')->__('Notes'),
         // 'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'po_notes',
      ));
     
      //$dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
      
      $dateFormatIso = "MM/dd/yyyy";
      
	  $fieldset->addField('date_of_po', 'date', array(
		  'name'   => 'date_of_po',
		  'label'  => Mage::helper('inventorymanager')->__('Date Of Purchase Order'),
		  'title'  => Mage::helper('inventorymanager')->__('Date Of Purchase Order'),
		  'image'  => $this->getSkinUrl('images/grid-cal.gif'),
		  'input_format' => $dateFormatIso,
		  'format'       => $dateFormatIso,
		  'time' => false,
	  ));
		
	  $fieldset->addField('expected_date', 'date', array(
		  'name'   => 'expected_date',
		  'label'  => Mage::helper('inventorymanager')->__('Expted Date'),
		  'title'  => Mage::helper('inventorymanager')->__('Expted Date'),
		  'image'  => $this->getSkinUrl('images/grid-cal.gif'),
		  'input_format' => $dateFormatIso,
		  'format'       => $dateFormatIso,
		  'time' => false
	  ));
      
      if ( Mage::getSingleton('adminhtml/session')->getPurchaseorderData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getPurchaaseorderData());
          Mage::getSingleton('adminhtml/session')->setPurchaaseorderData(null);
      } elseif ( Mage::registry('purchaseorder_data') ) {
      		$data = Mage::registry('purchaseorder_data')->getData();
      		if(!isset($data['date_of_po']) || $data['date_of_po'] == ""){
      			$data['date_of_po'] = date('m/d/Y');
      		}
          $form->setValues($data);
      }
     
      return parent::_prepareForm();
  }
}