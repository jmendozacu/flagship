<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_AdvancedStock_Helper_Product_Ordered extends Mage_Core_Helper_Abstract
{
	/**
	 * return ordered qty for one product and one website
	 * return array : 
	 * 					'total' => total ordered qty
	 *					'valid'	=> total ordered qty for paid orders
	 * @param unknown_type $productId
	 * @param unknown_type $websiteId
	 */
	public function computeOrderedQty($productId, $stock)
	{
		$total = 0;
		$totalValid = 0;

		$product = mage::getModel('catalog/product')->load($productId);
		if ($product->getId())
		{		
			//parse pending orders to fill pending orders ids
			$pendingOrders = mage::helper('AdvancedStock/Product_Base')->GetPendingOrders($product->getId(), false);
			$pendingOrdersIds = array();
			$validPendingOrderIds = array();
			foreach ($pendingOrders as $order)
			{
				$pendingOrdersIds[] = $order->getId();
				if ($order->getis_valid() == 1)
					$validPendingOrderIds[] = $order->getId();
			}
			
			//retrieve order items to compute order qty
			$pendingOrderItems = mage::getModel('sales/order_item')
									->getCollection()
									->addFieldToFilter('order_id', array('in' => $pendingOrdersIds))
									->addFieldToFilter('product_id', $productId)
									->addFieldToFilter('preparation_warehouse', $stock->getstock_id());
			foreach ($pendingOrderItems as $orderItem)
			{
				$remainToShip = $orderItem->getRemainToShipQty();
				if ($remainToShip > 0)
				{
					$total += $remainToShip;
					if (in_array($orderItem->getorder_id(), $validPendingOrderIds))
						$totalValid += $remainToShip;
				}
			}
		}
		
		$retour = array();
		$retour['total'] = $total;
		$retour['valid'] = $totalValid;
		
		return $retour;
	}
	
	/**
	 * Store ordered qty for stock
	 *
	 * @param unknown_type $stock
	 * @param unknown_type $productId
	 */
	public function storeOrderedQtyForStock($stock, $productId)
	{
		if ($stock == null)
			throw new Exception('Stock cant be null in storeOrderedQtyForStock');
		$values = $this->computeOrderedQty($productId, $stock);			
		$stock->setstock_ordered_qty($values['total'])->setstock_ordered_qty_for_valid_orders($values['valid'])->save();
	}

}