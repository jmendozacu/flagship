<?php

class Ecommerceguys_Inventorymanager_Model_label_Status
{
	// PURCHASE ORDER STATUS ARRAY
	public function toOptionArray(){
		return array(
			array('value'=>'1', 'label'=>Mage::helper('inventorymanager')->__("Arrived in warehouse")),
			array('value'=>'2', 'label'=>Mage::helper('inventorymanager')->__("Ready in the factory")),
			array('value'=>'3', 'label'=>Mage::helper('inventorymanager')->__("Shipped")),			
			array('value'=>'4', 'label'=>Mage::helper('inventorymanager')->__("Sent to client")),
		);
	}
	
	public function getValues(){
		return array(
			'1'=> Mage::helper('inventorymanager')->__("Arrived in warehouse"),
			'2'=> Mage::helper('inventorymanager')->__("Ready in the factory"),
			'3'=> Mage::helper('inventorymanager')->__("Shipped"),
			'4'=> Mage::helper('inventorymanager')->__("Sent to client"),
		);
	}
}