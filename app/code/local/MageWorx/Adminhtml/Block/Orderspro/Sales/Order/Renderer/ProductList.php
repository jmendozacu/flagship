<?php
class MageWorx_Adminhtml_Block_Orderspro_Sales_Order_Renderer_ProductList extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
  public function render(Varien_Object $row)
    {
     
    	$product_collection = Mage::getResourceModel('sales/order_item_collection')
                             ->addFieldToFilter('order_id', $row->getEntityId())
                             ->addFieldToSelect('name');
      foreach ($product_collection as $product) {
        $htmlArr[] = $product->getName();
      }
      $html = implode(',',$htmlArr);
    	return $html;
    }
}

