<?php

class MDN_AdvancedStock_Block_Product_Widget_Grid_Column_Renderer_OrderedQty
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    public function render(Varien_Object $row)
    {
		$retour = $row->getstock_ordered_qty();		
		$retour .= ' ('.$row->getstock_ordered_qty_for_valid_orders().')';
		
		return $retour;
    }
    
}