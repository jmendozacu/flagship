<?php
class Ecommerceguys_Inventorymanager_Block_User_Purchaseorder_Comment extends Mage_Core_Block_Template
{
	public function getCurrentOrder(){
		$id = $this->getCurrentOrderId();
		$commentObject = Mage::getModel('inventorymanager/purchaseorder')->load($id);
		return $commentObject;
	}
	
	public function getCurrentOrderId(){
		$id = $this->getRequest()->getParam('id');
		return $id;
	}
	
	public function getPurchaseOrderComments(){
		$purchaseorderId = $this->getCurrentOrderId();
		$commentCollection = Mage::getModel('inventorymanager/comment')->getCollection();
		$commentCollection->addFieldToFilter('po_id', $purchaseorderId);
		$commentCollection->setOrder('history_id','DESC');
		return $commentCollection;
	}
}