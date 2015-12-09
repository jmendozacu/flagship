<?php

class MDN_AdvancedStock_Block_Product_Widget_Grid_Column_Renderer_StockSummary
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    public function render(Varien_Object $row)
    {
		$retour = '';		
		
		$collection = mage::helper('AdvancedStock/Product_Base')->getStocks($row->getId());
		foreach ($collection as $item)
		{
			if ($item->ManageStock())
				$retour .= $item->getstock_name().' : '.((int)$item->getqty()).'<br>';
		}
		
		return $retour;
    }
    
}