<?php
class Rvtech_Barcodes_Block_Adminhtml_Barcodes_Renderer_ProductAcWeight extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
  public function render(Varien_Object $row)
    {
        $productVal =  $productcollection = Mage::getModel('catalog/product')->load($row->getProductId());
        
        return $productVal->getActualWeight();
    }
}