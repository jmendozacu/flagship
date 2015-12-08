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
	
	public function refreshserialsAction(){
		
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function addlocationAction(){
		$location = $this->getRequest()->getParam('location');
		try {
			$labelResourceModel = Mage::getResourceModel('inventorymanager/label')->addLocationFromAgent(array("location"=>$location));
		}catch (Exception $e){
			echo $e->getMessage();
		}
	}
	
	public function removelocationAction(){
		$location = $this->getRequest()->getParam('location');
		try {
			$labelResourceModel = Mage::getResourceModel('inventorymanager/label')->removeLocationFromAgent($location);
		}catch (Exception $e){
			echo $e->getMessage();
		}
	}
}