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
}