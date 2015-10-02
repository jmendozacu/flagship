<?php
class Ecommerceguys_Inventorymanager_Block_Vendor_Locations extends Mage_Core_Block_Template
{
	public function getLocations(){
		$resourceLabel = Mage::getResourceModel('inventorymanager/label');
		$locations = $resourceLabel->getLocations();
		return $locations;
	}
}