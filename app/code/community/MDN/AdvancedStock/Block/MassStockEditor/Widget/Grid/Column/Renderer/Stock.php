<?php

class MDN_AdvancedStock_Block_MassStockEditor_Widget_Grid_Column_Renderer_Stock
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    public function render(Varien_Object $row)
    {
    	$stockId = $row->getId();
    	$qty = (int)$row->getqty();
    	
    	$retour = '<input type="text name="stock_'.$stockId.'" id="stock_'.$stockId.'" value="'.$qty.'" size="4" disabled>';
		$retour .= '&nbsp;<input type="checkbox" name="ch_stock_'.$stockId.'" id="ch_stock_'.$stockId.'" value="1" onclick="toggleStockInput('.$stockId.');">';
		return $retour;
    }
    
}