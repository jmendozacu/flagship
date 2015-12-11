<?php
class Rvtech_Barcodes_Block_Adminhtml_Barcodes_Renderer_Upc extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
  public function render(Varien_Object $row)
    {
        $productId = $row->getProductId();
        $productUpc = $productId->getUpc();
        return $productUpc;
	}
}