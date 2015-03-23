<?php
class Rvtech_Barcodes_Block_Adminhtml_Barcodes_Renderer_ProductRender extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
  public function render(Varien_Object $row)
    {
        $productVal =  $productcollection = Mage::getModel('catalog/product')->load($row->getProductId());
        $productId = $row->getProductId();
        $value = $productVal->getName();
        $productUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK).'AdvancedStock/Products/Edit/product_id/'.$productId;
        return '<a href="'.$productUrl.'" target="_blank">'.$value.'</a>';
    }
}