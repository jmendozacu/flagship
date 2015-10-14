<?php
class Ecommerceguys_Inventorymanager_Block_User_Serial_Locations extends Mage_Core_Block_Template
{
	public function getLocations(){
		$resourceLabel = Mage::getResourceModel('inventorymanager/label');
		$locations = $resourceLabel->getLocationsForAgent();
		return $locations;
	}
}