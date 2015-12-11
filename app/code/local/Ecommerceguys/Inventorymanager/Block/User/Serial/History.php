<?php
class Ecommerceguys_Inventorymanager_Block_User_Serial_History extends Mage_Core_Block_Template
{
	public function getLabelId(){
		$key = $this->getRequest()->getParam('serial_key');
		$labelObject = Mage::getModel('inventorymanager/label')->load($key, "serial");
		return $labelObject->getId();
	}
	
	public function getSerial(){
		$key = $this->getRequest()->getParam('serial_key');
		return Mage::getModel('inventorymanager/label')->load($key, "serial");
	}
	
	public function getHistory(){
		$labelId = $this->getLabelId();
		$historyCollection = Mage::getModel('inventorymanager/label_history')->getCollection();
		$historyCollection->addFieldToFilter('label_id', $labelId);
		$historyCollection->setOrder("history_id", "DESC");
		return $historyCollection;
	}
	
	public function getUserName($userId){
		$userVars = explode("-", $userId);
		if(isset($userVars[1]) && $userVars[1] > 0){
			if(isset($userVars[0]) && $userVars[0] == 1){
				$agent = Mage::getModel('admin/user')->load($userVars[1]);
				return $agent->getFirstname() . " " . $agent->getLastname();
			}else{
				$vendor = Mage::getModel('inventorymanager/vendor')->load($userVars[1]);
				return $vendor->getName();
			}
		}
	}
}