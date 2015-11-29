<?php 
class Ecommerceguys_Inventorymanager_Block_User_Shipmanager_History extends Mage_Core_Block_Template
{
	public function getHistory(){
		$historyCollection = Mage::getModel('inventorymanager/shipmanager')->getCollection();
		return $historyCollection;
	}
}