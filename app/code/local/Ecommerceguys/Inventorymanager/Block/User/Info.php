<?php 
class Ecommerceguys_Inventorymanager_Block_User_Info extends Mage_Core_Block_Template
{
	public function getCurrentUser()
	{
		return Mage::getSingleton('core/session')->getUser();
	}
}