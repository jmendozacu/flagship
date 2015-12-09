<?php

class MagentoGarden_Transparentwatermark_Block_Adminhtml_Twcategory_Renderer_Watermark extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	public function render(Varien_Object $row) {
		$_watermark_filename = $row->getData($this->getColumn()->getIndex());
		$_watermark_filename = Mage::helper('transparentwatermark')->getImagePath($_watermark_filename);
		$_html = '<img height="100px" src="'.$_watermark_filename.'"></img>';
		return $_html;
	}
}
