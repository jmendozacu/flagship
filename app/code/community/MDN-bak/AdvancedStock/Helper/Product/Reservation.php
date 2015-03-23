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
class MDN_AdvancedStock_Helper_Product_Reservation extends Mage_Core_Helper_Abstract
{
	
	/**
	 * Store reserved qty for one stock
	 *
	 * @param unknown_type $stock
	 */
	public function storeReservedQtyForStock($stock, $productId)
	{
		if ($stock == null)
			throw new Exception('Stock cant be null in storeReservedQtyForStock');
			
		$value = 0;
		$value = $this->getReservedQtyForStock($stock, $productId);
						
		//store
		$stock->setstock_reserved_qty($value)->save();
	}
	
	/**
	 * Return reserved qty computed from pending order (skip cache)
	 *
	 * @param unknown_type $stock
	 * @param unknown_type $productId
	 */
	public function getReservedQtyForStock($stock, $productId)
	{
		$value = 0;
		
		//collect pending orders matching to stock
		$pendingOrdersIds = mage::helper('AdvancedStock/Sales_PendingOrders')->getPendingOrderIdsForProduct($productId);
		
		//retrieve order items to compute order qty
		$pendingOrderItems = mage::getModel('sales/order_item')
								->getCollection()
								->addFieldToFilter('order_id', array('in' => $pendingOrdersIds))
								->addFieldToFilter('product_id', $productId)
								->addFieldToFilter('preparation_warehouse', $stock->getstock_id());

		foreach ($pendingOrderItems as $orderItem)
		{
			$value += $orderItem->getreserved_qty();
		}
		
		return $value;
	}
	
	/**
	 * Reserved one product for one order
	 *
	 * @param unknown_type $order
	 * @param unknown_type $orderItem
	 */
	public function reserveOrderProduct($order, &$orderItem)
	{
		$debug = 'Reserve product #'.$orderItem->getproduct_id().' for order #'.$order->getincrement_id();
	
		//first check if orders fullfill conditions for reservation
		if (!$order->productReservationAllowed())
		{
			$debug .= 'Reservation is not allowed';
			return false;
		}
		
		//todo : check if function returns nothing
		$preparationWarehouse = $orderItem->getPreparationWarehouse();
	
		//init vars
		$alreadyReservedQy = $orderItem->getreserved_qty();
		$remainToShipQty = $orderItem->getRemainToShipQty();
		$qtyToReserve = $remainToShipQty - $alreadyReservedQy;
		$debug .= ', remaintToShip='.$remainToShipQty.', qtyToReserve='.$qtyToReserve.', warehouse='.$preparationWarehouse->getId();
		if ($qtyToReserve == 0)
			return true;
		$productId = $orderItem->getproduct_id();
		$reservableQty = $this->getReservableQty($preparationWarehouse, $productId);
		$debug .= ', reservableQty='.$reservableQty;
		if ($reservableQty < $qtyToReserve)
			$qtyToReserve = $reservableQty;
		
		//reserve qty if positive
		if ($qtyToReserve > 0)
		{
			//save reserved qty in order_item
			$orderItem->setreserved_qty($orderItem->getreserved_qty() + $qtyToReserve)->save();
			
			//update reserved qty for stock
			$stockItem = mage::getModel('cataloginventory/stock_item')->loadByProductWarehouse($productId, $preparationWarehouse->getId());
			$this->storeReservedQtyForStock($stockItem, $productId);
			
			$debug .= ' ---> Reserve '.$qtyToReserve.' units';
		}

		return $debug;
	}
	
	/**
	 * Release one product for one order
	 *
	 * @param unknown_type $order
	 * @param unknown_type $orderItem
	 */
	public function releaseProduct($order, $orderItem)
	{
		//init vars
		$productId = $orderItem->getproduct_id();
		$websiteId = $order->getStore()->getwebsite_id();
		
		if ($orderItem->getreserved_qty() > 0)
		{
			//reset reserved qty
			$orderItem->setreserved_qty(0)->save();
			
			//update reserved qty for stock
			$orderPreparationWarehouse = $orderItem->getPreparationWarehouse();
			$stockItem = mage::getModel('cataloginventory/stock_item')->loadByProductWarehouse($productId, $orderPreparationWarehouse->getId());
			$this->storeReservedQtyForStock($stockItem, $productId);
		}
	}
	
	/**
	 * Return reservable qty for one product and one website
	 *
	 * @param unknown_type $website
	 * @param unknown_type $productId
	 */
	public function getReservableQty($warehouse, $productId)
	{
		//init vars
		$value = 0;

		$stock = mage::getModel('cataloginventory/stock_item')->loadByProductWarehouse($productId, $warehouse->getId());
		if ($stock)
		{
			$value = $stock->getqty() - $stock->getstock_reserved_qty();
		}
		
		return $value;
	}
	
	/**
	 * Reserve product for pending orders
	 *
	 * @param unknown_type $productId
	 */
	public function reserveProductForPendingOrders($productId)
	{
		$debug = '';
		
		//get an array with available qty per warehouse
		$availableStocks = $this->getAvailableQtyArray($productId);
		
		//get pending orders ids
		$pendingOrderIds = mage::helper('AdvancedStock/Sales_PendingOrders')->getPendingOrderIdsForProduct($productId);

		//collection sales order items
		$salesOrderItems = mage::getModel('sales/order_item')
								->getCollection()
								->addFieldToFilter('order_id', array('in' => $pendingOrderIds))
								->addFieldToFilter('product_id', $productId);
		foreach($salesOrderItems as $orderItem)
		{
			//check if warehouse has available qty
			$preparationWarehouse = $orderItem->getpreparation_warehouse();
			if (isset($availableStocks[$preparationWarehouse]) && ($availableStocks[$preparationWarehouse] > 0))
			{
				$order = mage::getModel('sales/order')->load($orderItem->getorder_id());
				$debug .= "\n".$this->reserveOrderProduct($order, $orderItem);
				$availableStocks = $this->getAvailableQtyArray($productId);
			}
		}
		
		mage::log('reserveProductForPendingOrders for proudct #'.$productId);
	}
	
	/** 
	 * Return an array with key = stock id, value = available qty
	 */
	protected function getAvailableQtyArray($productId)
	{
		$retour = array();
		
		$stocks = mage::helper('AdvancedStock/Product_Base')->getStocks($productId);
		foreach($stocks as $stock)
		{
			$stockId = $stock->getstock_id();
			$availableQty = $stock->getqty() - $stock->getstock_reserved_qty();
			$retour[$stockId] = $availableQty;
		}
		
		return $retour;
	}
	
}