<?php 

class Ecommerceguys_Inventorymanager_Block_User_Shipmanager_Settings extends Mage_Core_Block_Template
{
	public function getShipAccount(){
		return Mage::getStoreConfig('inventorymanager/fedex_config/shipaccount');
	}
	
	public function getFrightAccount(){
		return Mage::getStoreConfig('inventorymanager/fedex_config/freightaccount');
	}
	
	public function getMeterNumber(){
		return Mage::getStoreConfig('inventorymanager/fedex_config/meter_number');
	}
	
	public function getKey(){
		return Mage::getStoreConfig('inventorymanager/fedex_config/key');
	}
	
	public function getFedexPassword(){
		return Mage::getStoreConfig('inventorymanager/fedex_config/password');
	}
	
	public function getShipperAddress(){
		return Mage::getStoreConfig('inventorymanager/fedex_config/shipper_address');
	}
}