<?php

class MDN_AdvancedStock_Block_Product_Widget_Grid_Column_Filter_Barcode	extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Text 
{

    public function getCondition()
    {
    	$searchString = $this->getValue();
    	
    	$barcodes = mage::getModel('AdvancedStock/ProductBarcode')
    				->getCollection()
    				->addFieldToFilter('ppb_barcode', array('like' => '%'.$searchString.'%'));
    	$productIds = array();
    	foreach ($barcodes as $barcode)
    	{
    		$productIds[] = $barcode->getppb_product_id();
    	}
    	
    	
		return array('in' => $productIds);
    }
    
}