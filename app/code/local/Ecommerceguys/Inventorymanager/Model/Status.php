<?php

class Ecommerceguys_Inventorymanager_Model_Status
{
	// PURCHASE ORDER STATUS ARRAY
	public function toOptionArray(){
		return array(
			array('value'=>'processing', 'label'=>Mage::helper('inventorymanager')->__("Processing")),
			array('value'=>'partial', 'label'=>Mage::helper('inventorymanager')->__("Partially Shipped")),
			array('value'=>'complete', 'label'=>Mage::helper('inventorymanager')->__("Completely Shipped")),
			array('value'=>'received', 'label'=>Mage::helper('inventorymanager')->__("Received")),
			array('value'=>'returned', 'label'=>Mage::helper('inventorymanager')->__("Returned")),
		);
	}
	
	public function getValues(){
		return array(
			'processing'=> Mage::helper('inventorymanager')->__("Processing"),
			'partial'=> Mage::helper('inventorymanager')->__("Partially Shipped"),
			'complete'=> Mage::helper('inventorymanager')->__("Completely Shipped"),
			'received'=> Mage::helper('inventorymanager')->__("Received"),
			'returned'=> Mage::helper('inventorymanager')->__("Returned"),
		);
	}
}