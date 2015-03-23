<?php

class MagentoGarden_Transparentwatermark_Block_Adminhtml_Twcategory_Renderer_Categoryid extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	public function render(Varien_Object $row) {
		$_idx = $row->getData($this->getColumn()->getIndex());
		$_category = Mage::getModel('catalog/category')->load($_idx);
		return $_category->getName();
	}
}
