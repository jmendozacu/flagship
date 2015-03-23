<?php

/*
* retourne les �l�ments � envoyer pour une commande s�lectionn�e pour la pr�paration de commandes
*/
class MDN_Orderpreparation_Block_Adminhtml_Widget_Grid_Column_Renderer_ContentToShip
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    public function render(Varien_Object $row)
    {
    	$retour = "";
    	
    	$order = $row;
    	$OrderToPrepare = mage::getModel('Orderpreparation/ordertoprepare')->load($order->getId(), 'order_id');
    	
    	//Build string with content to ship
    	$order = $row;
    	$collection = mage::getModel('Orderpreparation/ordertoprepare')->GetItemsToShip($order->getId());
    	foreach ($collection as $item)
    	{
            $orderItem = mage::getModel('sales/order_item')->load($item->getorder_item_id());
            $productId = $orderItem->getproduct_id();
			$retour .= $item->getqty();
			$name = $orderItem->getName();
			$name .= mage::helper('AdvancedStock/Product_ConfigurableAttributes')->getDescription($productId);
	    	$retour .= 'x '.$name.'<br>';

    	}
    	
    	return $retour;
    }
    
}