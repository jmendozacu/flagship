<?php
class Ecommerceguys_Inventorymanager_NotificationController extends Mage_Core_Controller_Front_Action
{
	public function indexAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
}