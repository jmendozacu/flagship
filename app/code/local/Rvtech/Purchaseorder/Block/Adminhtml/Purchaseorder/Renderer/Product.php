<?php
class Rvtech_Purchaseorder_Block_Adminhtml_Purchaseorder_Renderer_Product extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
  public function render(Varien_Object $row)
    {
    	
    	$productcollection = Mage::getModel('catalog/product')->load($row->getProductId());
      $purchase_order = Mage::getModel('Purchase/Order')
                          ->load($row->getPurchaseOrder(),'po_order_id');
      
      if($this->getColumn()->getIndex() == 'id'){

          return $purchase_order->getPoNum();

    	}elseif($this->getColumn()->getIndex() == 'product_id'){

        $po = $row->getPurchaseOrder();
        $collection = Mage::getSingleton('barcodes/barcodes')->getCollection()
                      ->addFieldToFilter('purchase_order',array('eq' => $po));
        $collection ->getSelect()->group('product_id');
        return $collection->count();

      }elseif($this->getColumn()->getIndex() == 'dzv_serial'){

        $po = $row->getPurchaseOrder();
        $collection = Mage::getSingleton('barcodes/barcodes')->getCollection()
                      ->addFieldToFilter('purchase_order',array('eq' => $po));
                     
        return $collection->count();

      }elseif($this->getColumn()->getIndex() == 'factory_id'){

    		$attributes = Mage::getResourceModel('eav/entity_attribute_collection')
                            ->addFieldToFilter('attribute_code', 'factory') 
                            ->load();
          	$attribute = $attributes->getFirstItem();

          	$attr = $attribute->getSource()->getAllOptions(true);
          	foreach ($attr as $attval) {
              if($attval['value']==$row->getFactoryId())
               {
                   $factName = $attval['label'];
                   //$colval->setFactoryId($factName);
                }
            }
    		return $factName;
    	}elseif ($this->getColumn()->getIndex() == 'purchase_order') {

        if($purchase_order->getPoNum())
        {
            $url = $this->getUrl('Purchase/Orders/Edit', array())."po_num/".$purchase_order->getPoNum();
            return '<a href="'.$url.'">'.$row->getPurchaseOrder().'</a>';
        }else{

            return $row->getPurchaseOrder();
        }                          
        

        return print_r($row->getPurchaseOrder());
      }
    }
}