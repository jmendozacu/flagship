<?php
class Ecommerceguys_Inventorymanager_Block_Vendor_Product_File extends Ecommerceguys_Inventorymanager_Block_Vendor_Product_Edit
{
	public function getFiles(){
		if($productInfoObject = $this->getProductInfoObject()){
			$filesJson = $productInfoObject->getFiles();
			if($filesJson && $filesJson != ""){
				return Mage::helper('core')->jsonDecode($filesJson);
			}else{
				return false;
			}
		}
		return false;
	}
}