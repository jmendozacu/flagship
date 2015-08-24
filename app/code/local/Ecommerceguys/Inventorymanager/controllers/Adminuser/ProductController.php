<?php
class Ecommerceguys_Inventorymanager_Adminuser_ProductController extends Mage_Core_Controller_Front_Action
{
	public function stockgridAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function serialgridAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
}