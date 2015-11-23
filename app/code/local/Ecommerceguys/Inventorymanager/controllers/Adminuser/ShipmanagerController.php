<?php
class Ecommerceguys_Inventorymanager_Adminuser_ShipmanagerController extends Mage_Core_Controller_Front_Action
{
	public function indexAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function saveAction(){
		$data = $this->getRequest()->getParams();
		echo "<pre>";
		print_r($data);
	}
}