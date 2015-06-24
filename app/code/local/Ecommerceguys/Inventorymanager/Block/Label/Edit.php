<?php 

class Ecommerceguys_Inventorymanager_Block_Label_Edit extends Mage_Core_Block_Template
{
	public function getLabelObject(){
		$serialKey = $this->getRequest()->getParam('serial_key');
		$labelObject = Mage::getModel('inventorymanager/label')->load($serialKey, 'serial');
		if($labelObject && $labelObject->getId()){
			return $labelObject;
		}
		return false;
	}
}