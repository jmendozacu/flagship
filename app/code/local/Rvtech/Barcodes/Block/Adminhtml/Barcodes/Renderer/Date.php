<?php
class Rvtech_Barcodes_Block_Adminhtml_Barcodes_Renderer_Date extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
  public function render(Varien_Object $row)
    {
    	
      $oder = $row->getPurchaseOrder();
    	$productcollection = mage::getModel('Purchase/Order')->getCollection()
                            ->addFieldToFilter('po_order_id',array('eq' => $oder))
                            ->getFirstItem();
      if($productcollection->getPoDate())
      {

        $newDateFormat = Mage::app()->getLocale()->date(strtotime($productcollection->getPoDate()), null, null, false)->toString('MMM d, Y');
        return $newDateFormat;
      }else{

        $newDateFormat = Mage::app()->getLocale()->date(strtotime($row->getDate()), null, null, false)->toString('MMM d, Y');
        return $newDateFormat;
      }
      
    	
    }
}