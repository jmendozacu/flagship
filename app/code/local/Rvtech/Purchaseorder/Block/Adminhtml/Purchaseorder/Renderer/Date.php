<?php
class Rvtech_Purchaseorder_Block_Adminhtml_Purchaseorder_Renderer_Date extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
  public function render(Varien_Object $row)
    {
    	
     $date = $row->getPoDate();
    	
      if($date)
      {

        $newDateFormat = Mage::app()->getLocale()->date(strtotime($date), null, null, false)->toString('MMM d, Y');
        return $newDateFormat;
      }else{

        $newDateFormat = Mage::app()->getLocale()->date(strtotime($row->getDate()), null, null, false)->toString('MMM d, Y');
        return $newDateFormat;
      }
      
    	
    }
}