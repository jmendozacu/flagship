<?php
class Ecommerceguys_Inventorymanager_Block_User_Serial_History extends Mage_Core_Block_Template
{
	public function getLabelId(){
		$key = $this->getRequest()->getParam('serial_key');
		$labelObject = Mage::getModel('inventorymanager/label')->load($key, "serial");
		return $labelObject->getId();
	}
	
	public function getHistory(){
		$labelId = $this->getLabelId();
		$historyCollection = Mage::getModel('inventorymanager/label_history')->getCollection();
		$historyCollection->addFieldToFilter('label_id', $labelId);
		$historyCollection->setOrder("history_id", "DESC");
		return $historyCollection;
	}
}