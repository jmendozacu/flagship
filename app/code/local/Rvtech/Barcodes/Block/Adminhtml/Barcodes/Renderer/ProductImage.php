<?php
class Rvtech_Barcodes_Block_Adminhtml_Barcodes_Renderer_ProductImage extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
  public function render(Varien_Object $row)
    {
        $productVal = Mage::getModel('catalog/product')->load($row->getProductId());
        if($productVal->getImage() != "no_selection")
           {
             return $imageUrl = Mage::getModel('catalog/product_media_config')->getMediaUrl($productVal->getImage());
           }else
           {
            return  $imageUrl = $productVal->getImage();
           }
         
    }
}