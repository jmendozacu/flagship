<?php
class Custom_Rvtech_Block_Sales_Order_Renderer_UpcList extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
  public function render(Varien_Object $row)
    {
  
    	$order_collection = Mage::getResourceModel('sales/order_item_collection')
                             ->addFieldToFilter('order_id', $row->getEntityId());
       
      foreach ($order_collection as $product) 
      {
        $product_collection = Mage::getModel('catalog/product')->load($product->getProductId());
          if($product_collection->getUpc()){

          $htmlArr[] = $product_collection->getUpc();       
        }
      }

      $html = implode(',  ',$htmlArr);
    	return $html;
    }
}

