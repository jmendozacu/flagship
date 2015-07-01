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
	
	public function resizeImage($_file_name, $width = 139, $height = 139){
		
		
		$_media_dir = Mage::getBaseDir('media') . DS . 'label' . DS . 'comments' . DS;
        $cache_dir = $_media_dir . 'resize' . DS; // Here i create a resize folder. for upload new category image

		if (!file_exists($cache_dir . $_file_name) && file_exists($_media_dir . $_file_name)) {
			
             if (!is_dir($cache_dir)) {
                 mkdir($cache_dir);
             }
             $_image = new Varien_Image($_media_dir . $_file_name);
             $_image->constrainOnly(true);
             $_image->keepAspectRatio(true);
             $_image->keepFrame(false);
             $_image->keepTransparency(true);
             $_image->resize($width, $height); // change image height, width
             $_image->save($cache_dir . $_file_name);
             
         }
         $catImg =Mage::getBaseUrl('media') .  'label/comments/resize/' . $_file_name;
 		return  $catImg ; 
	}
}