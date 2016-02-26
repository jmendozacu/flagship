<?php
class Ecommerceguys_Inventorymanager_Block_User_Serial_Bulklocation extends Mage_Core_Block_Template
{
	public function getLocations(){
		$resourceLabel = Mage::getResourceModel('inventorymanager/label');
		$locations = $resourceLabel->getAllLocation();
		return $locations;
	}
}