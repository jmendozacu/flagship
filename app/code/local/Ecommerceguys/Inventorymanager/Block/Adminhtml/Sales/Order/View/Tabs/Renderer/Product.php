<?php
class Ecommerceguys_Inventorymanager_Block_Adminhtml_Sales_Order_View_Tabs_Renderer_Product extends 
	Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$value =  $row->getData($this->getColumn()->getIndex());
		$product = Mage::getModel('catalog/product')->load($value);
		if($product && $product->getId()){
			return $product->getName();
		}
	}
}