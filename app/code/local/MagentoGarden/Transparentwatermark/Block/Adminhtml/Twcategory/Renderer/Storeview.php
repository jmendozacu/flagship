<?php

class MagentoGarden_Transparentwatermark_Block_Adminhtml_Twcategory_Renderer_Storeview extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	public function render(Varien_Object $row) {
		return Mage::helper('transparentwatermark')->renderStoreviewColumn($row, $this->getColumn()->getIndex());
	}
}
