<?php

class Ecommerceguys_Inventorymanager_Helper_Data extends Mage_Core_Helper_Abstract
{	
	public function getProductInfoFieldTitles(){
		return array(
			"description" => $this->__("Description"),
			"cost"	=> $this->__("Cost"),
			"length" => $this->__("Length"),
			"width"	=> $this->__("Width"),
			"height" => $this->__("Height"),
			"fun_spec" => $this->__("Fun Specs"),
			"material" => $this->__("Material"),
			"lighting" => $this->__("Lighting"),
			"files" => $this->__("Drawings"),
		);
	}
	
	public function getSerial(){
		 $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		 $random_string_length = 14;
		 $string = '';
		 for ($i = 0; $i < $random_string_length; $i++) {
		      $string .= $characters[rand(0, strlen($characters) - 1)];
		 }
		 return $string;
	}
	
	public function getOrderedProductStatusArray(){
		$status = Mage::getResourceModel('inventorymanager/label')->getStatuses();
		$statusArray = array_map(array($this, formatStatusArray), $status);
		return $statusArray;
	}
	
	public function formatStatusArray($value){
		if(isset($value['status']))
			return $value['status'];
		return "";
	}
}