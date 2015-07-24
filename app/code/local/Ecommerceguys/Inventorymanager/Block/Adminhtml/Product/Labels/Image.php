<?php
class Ecommerceguys_Inventorymanager_Block_Adminhtml_Product_Labels_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$value =  $row->getData($this->getColumn()->getIndex());
		if($value != "")
		return '<span style="padding:5px;"><img src="'.Mage::helper("inventorymanager")->resizeImage($value, 100, 100, "label/").'" alt="'.$value.'" /></span>'; 
		return "";
	}
}