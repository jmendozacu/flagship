<?php
class Ecommerceguys_Inventorymanager_PurchaseorderController extends Mage_Core_Controller_Front_Action
{
	public function gridAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
}