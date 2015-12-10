<?php
class Ecommerceguys_Inventorymanager_Block_Help_View extends Mage_Core_Block_Template
{
	public function getCurrentVideoPath(){
		$path = $this->getRequest()->getParam("path");
		return Mage::getBaseUrl('media'). "inventorymanager/video/" . $path;
	}
}