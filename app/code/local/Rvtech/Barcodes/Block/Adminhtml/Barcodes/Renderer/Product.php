<?php
class Rvtech_Barcodes_Block_Adminhtml_Barcodes_Renderer_Product extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
  public function render(Varien_Object $row)
    {
    	
    	$productcollection = Mage::getModel('catalog/product')->load($row->getProductId());
        if($this->getColumn()->getIndex() == 'sku'){
        	return $productcollection->getSku();
    	}elseif($this->getColumn()->getIndex() == 'upc'){
    		return $productcollection->getUpc();
    	}elseif($this->getColumn()->getIndex() == 'date'){
    		$newDateFormat = Mage::app()->getLocale()->date(strtotime($row->getDate()), null, null, false)->toString('MMM d, Y');
    		return $newDateFormat;
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

        $purchase_order = Mage::getModel('Purchase/Order')
                          ->load($row->getPurchaseOrder(),'po_order_id');
        if($purchase_order->getPoNum())
        {
            $url = $this->getUrl('Purchase/Orders/Edit', array())."po_num/".$purchase_order->getPoNum();
            return '<a href="'.$url.'" target="_blank">'.$row->getPurchaseOrder().'</a>';
        }else{

            return $row->getPurchaseOrder();
        }                          
        

        return print_r($row->getPurchaseOrder());
      }
    }
}