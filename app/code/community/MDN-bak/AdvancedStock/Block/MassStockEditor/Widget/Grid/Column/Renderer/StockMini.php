<?php

class MDN_AdvancedStock_Block_MassStockEditor_Widget_Grid_Column_Renderer_StockMini
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    public function render(Varien_Object $row)
    {
        $inventoryGroupName = mage::helper('AdvancedStock/MagentoVersionCompatibility')->getStockOptionsGroupName();
    	$DefaultNotifyStockQty = Mage::getStoreConfig('cataloginventory/'.$inventoryGroupName.'/notify_stock_qty');
    	if ($DefaultNotifyStockQty == '')
    		$DefaultNotifyStockQty = 0;
    		
    	$stockMini = (int)$row->getnotify_stock_qty();
    	$useConfig = $row->getuse_config_notify_stock_qty();
    	if ($useConfig == 1)
    		$stockMini = $DefaultNotifyStockQty;	
   	
    	$stockId = $row->getId();
    	
    	$retour = '<input type="text name="stockmini_'.$stockId.'" id="stockmini_'.$stockId.'" value="'.$stockMini.'" size="4" disabled>';
		$retour .= '&nbsp;<input type="checkbox" name="ch_stockmini_'.$stockId.'" id="ch_stockmini_'.$stockId.'" value="1" onclick="toggleStockMiniInput('.$stockId.');">';
		return $retour;
    }
    
}