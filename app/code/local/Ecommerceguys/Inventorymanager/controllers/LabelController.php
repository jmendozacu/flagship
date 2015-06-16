<?php
class Ecommerceguys_Inventorymanager_LabelController extends Mage_Core_Controller_Front_Action 
{
	public function generateAction(){
		$orderId = $this->getReqest()->getParam('id');
	}
} 