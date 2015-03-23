<?php

//die("error level: ".error_reporting());
class MDN_Scanner_Block_ShipmentScanner_Unshipped extends Mage_Adminhtml_Block_Widget_Form {

	private $_orders = array();
	private $_items = array();
	private $_unshippedItems = array();
	private $_products = array();
	private $_unshippedProductIds = array();
	private $_serials = array();

	public function getOrders() {
		$orders = $this->getUnshippedOrders();
		//echo "orders class " . get_class($orders) . " count: " . count($orders) . "<br>\n";
		foreach ($orders as $orderId => $order) {
			$this->_orders[$orderId] = $order->getData();
			//echo "<pre>order id {$order->getData('order_id')}: ".print_r($order->getData(), true)."</pre>\n";
			$items = $this->getOrderItems($order);
			//echo "items class ".get_class($items)." count: ".count($items)."<br>\n";
			$this->_orders[$orderId]['items'] = array();
			$this->_orders[$orderId]['unshipped_items'] = array();
			foreach ($items as $itemId => $item) {
				//echo "<pre>item {$item->getData('item_id')}: ".print_r($item->getData(), true)."</pre>\n";
				$this->_orders[$orderId]['items'][] = $itemId;
				$this->_items[$itemId] = $item->getData();
				//echo "added item $itemId to order $orderId<br>\n";
				$productId = $item->getData('product_id');
				// only include unshipped items
				if ($item->getData('qty_ordered') > $item->getData('qty_shipped')) {
					$this->_orders[$orderId]['unshipped_items'][] = $itemId;
					$this->_unshippedItems['item_id'] = $item->getData('qty_ordered') - $item->getData('qty_shipped');
				}
				// set unique product info
				if (!array_key_exists($productId, $this->_products)) {
					//echo "creating _product {$productId} for item $itemId<br>\n";
					// TODO: do we need to select Magento product info?
					$this->_products[$productId] = array(
						'product_id' => $productId,
						'name' => $item->getData('name'),
						'sku' => $item->getData('sku'),
						'product_type' => $item->getData('product_type'),
						'store_id' => $item->getData('store_id'),
						'weight' => $item->getData('weight'),
						'items' => array()
					);
				}
				//echo "adding item $itemId to _products array[$productId]['items']<br>\n";
				$this->_products[$productId]['items'][] = $itemId;
			}
		}
		//echo "returning this _orders with count ".count($this->_orders)."<br>\n";
		return $this->_orders;
	}

	public function getOrderItems($order = null) {
		//echo "running getOrderItems with order type ".gettype($order)."<br>\n";
		if ($order instanceof MDN_AdvancedStock_Model_Sales_Order) {
			//echo "order is instance of MDN_AdvancedStock_Model_Sales_Order<br>\n";
			if (isset($this->_orders[$order->getData('entity_id')]['items'])) {
				//echo "order {$order->getData('entity_id')} has items array<br>\n";
				return $this->_orders[$order->getData('entity_id')]['items'];
			}
			//echo "returning order getItemsCollection<br>\n";
			return $order->getItemsCollection();
		}
		//echo "returning this->_items with count ".count($this->_items)."<br>\n";
		return $this->_items;
	}

	public function getProducts() {
		return $this->_products;
	}

	public function getSerials() {
		if (empty($this->_serials)) {
			$serialColl = Mage::getModel('barcodes/barcodes')
					->getCollection()
					->addFieldToFilter('product_id', array('in' => array_keys($this->_products)))
					->load();
			foreach ($serialColl as $serialId => $serial) {
				$productId = $serial->getData('product_id');
				$dzv_serial = $serial->getData('dzv_serial');
				$location = $serial->getData('location');

				if ($dzv_serial) {
					$this->_serials[$dzv_serial]['product_id'] = $serial->getProductId();
					$this->_serials[$dzv_serial]['location'] = $serial->getLocation();
				}
			}
		}

		return $this->_serials;
	}

	public function getInfo() {
		//echo "running getInfo...<br>\n";
		$productLoc = array();
		$finalInfoArr = array();
		$unshippedItems = $this->getUnshippedItems();
		//echo "unshipped items count: ".count($unshippedItems)."<br>\n";
		$product_ids = array_unique($this->_unshippedProductIds);
		//echo "<pre>product ids: " . print_r($product_ids, true) . "</pre>\n";
		if (!empty($product_ids)) {
			$serialColl = Mage::getModel('barcodes/barcodes')
					->getCollection()
					->addFieldToFilter('product_id', array('in' => $product_ids))
					->load();
			foreach ($serialColl as $serial) {
				$productId = $serial->getData('product_id');
				$dzv_serial = $serial->getData('dzv_serial');
				$location = $serial->getData('location');

				if ($dzv_serial) {
					$unshippedItems[$productId]['serials'][] = $dzv_serial;
					$unshippedItems[$productId]['locations'][] = $location;
				}
			}
		}

		return $unshippedItems;
	}

	/**
	 * @return array(
	 * 		order_id => 'Unshipped sales Order Id',
	 * 		item_id => 'Unshipped Sales Order item Id',
	 * 		sku => 'Unshipped Product SKU',
	 * 		name => 'Unshipped Product Name',
	 * 		product_id => 'Unshipped Product Id'
	 * 	)
	 */
	public function getUnshippedItems() {
		//echo "running getUnshippedItems...<br>\n";
		// fetch Selected Orders (ERP > Orders > Order Preparation) that have not shipped
		$orders = $this->getUnshippedOrders();
		//echo "orders class ".get_class($orders)." count: ".count($orders)."<br>\n";
		//die("<pre>orders: ".print_r($orders->getData(), true));
		$unshippedItems = array();
		foreach ($orders as $index => $order) {
			//echo "order data:<pre>".print_r($order->getData(), true)."</pre>\n";
			// don't know why we need shipments
			//$shipments = $order->getShipmentsCollection();
			//echo "shipments count: ".count($shipments)."<br>\n";
			//foreach ($shipments as $shipment) {
			//	echo "<pre>shipment:".print_r($shipment->getData(), true)."</pre>\n";
			//}
			$items = $order->getItemsCollection();
			//echo "order items class ".get_class($items)." count: ".count($items)."<br>\n";
			foreach ($items as $index => $order) {
				//echo "<pre>order item $index: ".print_r($order->getData(), true)."</pre>\n";
				$productId = $order->getData('product_id');
				$qty_ordered = $order->getData('qty_ordered');
				$qty_shipped = $order->getData('qty_shipped');
				if ($qty_shipped < $qty_ordered) {
					//echo "quantity shipped is less than quantity ordered<br>\n";
					$unshippedItems[$productId]['order_id'] = $order->getData('order_id');
					$unshippedItems[$productId]['item_id'] = $order->getData('item_id');
					$unshippedItems[$productId]['sku'] = $order->getData('sku');
					$unshippedItems[$productId]['name'] = $order->getData('name');
					$unshippedItems[$productId]['product_id'] = $order->getData('product_id');
					$unshippedItems[$productId]['serials'] = array();
					if (!in_array($order->getData('product_id'), $this->_unshippedProductIds)) {
						$this->_unshippedProductIds[$productId] = $order->getData('product_id');
					}
				}
				//echo "<pre>unshipped item $productId: " . print_r($unshippedItems, true) . "</pre>\n";
			}
		}

		return $unshippedItems;
	}

	/**
	 * @return object Mage_Sales_Model_Resource_Order_Collection
	 *
	 */
	public function getUnshippedOrders() {
		//echo "running getUnshippedOrders...<br>\n";
		return Mage::getModel('sales/order')->getCollection()
			->addFieldToFilter('status', array('nin' => array('complete', 'closed', 'canceled')))
			->setOrder('entity_id')
		;
	}

	public function getSubmitUrl()
	{
		return Mage::getUrl('Scanner/ShipmentScanner/saveShippedItems');
	}

	public function saveShippedItems($post) {
		//echo "<pre>running saveShippedItems with post: ".print_r($post, true)."</pre>\n";
		/** EXAMPLE POST
		$postArr = array(
			'selected_order' => '1000000223',
			'selected_order_type' => 'magento',
			'product_serials' => 'FS20140127-0002,FS20140127-0003,FS20140127-0004'
		);
		 */
		$selectedOrder = $post['selected_order'];
		//echo "selected order = $selectedOrder<br>";
		$orderType = $post['order_type'];
		//echo "order type is $orderType<br>";
		//echo "order type: $orderType<br>\n";
		$productSerials = explode(',', $post['product_serials']);
		//echo "<pre>product serials: ".print_r($productSerials, true)."</pre>\n";
		if ($orderType == 'magento') {
			// Get the order items and increment shipped qty 
			// this will increment ALL items regardless of scanned serial; not good enough
			// $shipOrder = $this->getShipThisOrders($selectedOrder);

			$order = Mage::getModel('sales/order')->load($selectedOrder, 'increment_id');
			//echo "<pre>order: ".print_r($order->getData(), true)."</pre>\n";
			// filter through items and increment shipped by scanned serial
			$items = $order->getItemsCollection();
			//echo "<pre>items: ".print_r($items->getData(), true)."</pre>\n";
			// fetch products by serials

			$serialColl = Mage::getModel('barcodes/barcodes')
				->getCollection()
				->addFieldToFilter('dzv_serial', array('in' => $productSerials))
				->load();
			//echo "<pre>serials: ".print_r($serialColl->getData(), true)."</pre>\n";
			//exit;
			//echo"<pre>";print_r($items->getData());exit;
			if (count($items)) {
				foreach ($items as $index => $item) {
					$item_id = $item->getId();
					$oldStockQty = (int) $item->getQtyShipped();
					$newStockQty = (int) ($oldStockQty + 1);
					$orderedQty = $item->getQtyOrdered();
					if ($oldStockQty < $orderedQty) {
						$item->setQtyShipped($newStockQty);
						$item->save();
					}
				}
				return count($shipItems);
			} else {
				return 0;
			}

			if ($shipOrder) {
				$returnVal = true;
			} else {
				$returnVal = false;
			}

			// Update the serials location with magento order
			$serialsList = $this->updateSerialsOrder($productSerials, $selectedOrder);
			if ($serialsList) {
				$returnVal = true;
			} else {
				$returnVal = false;
			}
			return $returnVal;
		}

		// save the order number to the serials
		// TODO: select order with items
		// TODO: select Rvtech serial records
		// TODO: for each serial of product that is an order item, increment the item quantity shipped by 1
		// REFERENCE: look up any order > Preparation tab for code on how items are marked shipped
		// TODO: for each serial that was shipped, mark its location as the Magento order
	}

	/**
	 * @return Select order and items for saveShippedItems
	 *
	 */
	public function getShipThisOrders($load) {

		$orderShipped = Mage::getModel('sales/order')->load($load, 'increment_id');

		$shipItems = $orderShipped->getItemsCollection();
		//echo"<pre>";print_r($shipItems->getData());exit;
		if (count($shipItems)) {
			foreach ($shipItems as $index => $item) {
				$item_id = $item->getId();
				$oldStockQty = (int) $item->getQtyShipped();
				$newStockQty = (int) ($oldStockQty + 1);
				$orderedQty = $item->getQtyOrdered();
				if ($oldStockQty < $orderedQty) {
					$item->setQtyShipped($newStockQty);
					$item->save();
				}
			}
			return count($shipItems);
		} else {
			return 0;
		}
	}

	/**
	 * @return Return serials list for saveShippedItems
	 *
	 */
	public function updateSerialsOrder($serials, $selectOrder) {

		//echo"<pre>";print_r($serials);

		$countVal = count($serials);
		for ($i = 0; $i < $countVal; $i++) {
			$filter_a[] = array('eq' => $serials[$i]);
		}

		$filter_all = array($filter_a);

		$serialsUpdate = Mage::getModel('barcodes/barcodes')
				->getCollection()
				->addFieldToFilter('dzv_serial', $filter_all);


		foreach ($serialsUpdate as $index => $serial) {

			$serial->setLocation($selectOrder);
			$serial->save();
		}
		return true;
	}

}
