<?php

class MDN_Scanner_Block_ShipmentScanner_Unshipped extends Mage_Adminhtml_Block_Widget_Form {

	private $_orders = array();
	private $_products = array();
	private $_serials = array();

	public function getOrders() {
		if (!$this->_orders) {
			$this->_orders = Mage::getModel('sales/order')->getCollection()
				->addFieldToFilter('status', array('nin' => array('complete', 'closed', 'canceled')))
				->setOrder('entity_id', 'DESC')
			;
		}
		$orders = array();
		foreach ($this->_orders as $orderId => $order) {
			$orders[$orderId] = $order->getData();
			$items = $order->getItemsCollection();
			foreach ($items as $itemId => $item) {
				$orders[$orderId]['items'][$itemId] = $item->getData();

				// set unique product info
				$productId = $item->getData('product_id');
				if (!isset($this->_products[$productId])) {
					// TODO: should we select the Magento product instead?
					//$product = Mage::getModel('catalog/product')->load($productId);
					$this->_products[$productId] = array(
						'product_id' => $productId,
						'name' => $item->getData('name'),
						'sku' => $item->getData('sku'),
						'product_type' => $item->getData('product_type'),
						'store_id' => $item->getData('store_id'),
						'weight' => $item->getData('weight'),
					);
				}
			}
		}
		ksort($this->_products);
		return $orders;
	}

	public function getProducts() {
		return $this->_products;
	}
	
	public function getSerials() {
		if (!$this->_serials && $this->_products) {
			$serialColl = Mage::getModel('barcodes/barcodes')
				->getCollection()
				->addFieldToFilter('product_id', array('in' => array_keys($this->_products)))
				->setOrder('id')
				->load();
			foreach ($serialColl as $serialId => $serial) {
				$this->_serials[$serialId] = $serial->getData();
			}
		}
		return $this->_serials;
	}

	public function getSubmitUrl() {
		return Mage::getUrl('Scanner/ShipmentScanner/saveShipment');
	}

	public function saveShippedItems($post) {
		//echo "<pre>running saveShippedItems with post: " . print_r($post, true) . "</pre>\n";
		/** EXAMPLE POST
		  $postArr = array(
		  'selected_order' => '1000000223',
		  'selected_order_type' => 'magento',
		  'product_serials' => 'FS20140127-0002,FS20140127-0003,FS20140127-0004'
		  );
		 */
		$selectedOrder = $post['selected_order'];
		//echo "selected order = $selectedOrder<br>";
		$orderTypes = array('magento' => 'MG', 'zen_cart' => 'ZC', 'amazon' => 'AM');
		$orderType = $post['order_type'];
		//echo "order type is $orderType<br>";
		$productSerials = explode(',', $post['product_serials']);
		//echo "<pre>product serials: " . print_r($productSerials, true) . "</pre>\n";
		$updateCount = 0;
		
		// fetch Rvtech serial records
		$rvSerials = Mage::getModel('barcodes/barcodes')
			->getCollection()
			->addFieldToFilter('dzv_serial', array('in' => $productSerials))
			->load();
		//echo "<pre>rvSerials: " . print_r($rvSerials->getData(), true) . "</pre>\n";
		$productIds = array();
		if ($orderType == 'magento') {
			//echo "order type is magento<br>\n";
			// get order items and increment shipped quantity
			//echo "fetching magento order where order number = $selectedOrder<br>\n";
			$order = Mage::getModel('sales/order')->load($selectedOrder, 'increment_id');
			//echo "<pre>order: " . print_r($order->getData(), true) . "</pre>\n";
			$items = $order->getItemsCollection();
			//echo "<pre>items: " . print_r($items->getData(), true) . "</pre>\n";
			foreach ($rvSerials->getData() as $index => $rvSerial) {
				foreach($items as $itemId => $item) {
					if($item['product_id'] == $rvSerial['product_id']) {
						$oldStockQty = (int) $item->getQtyShipped();
						$newStockQty = (int) ($oldStockQty + 1);
						$orderedQty = $item->getQtyOrdered();
						$item->setQtyShipped($newStockQty);
						//echo "set item {$item->getId()} quantity shipped to {$item->getQtyShipped()}<br>\n";
						$item->save();
					}
				}
			}
		}
		
		foreach ($rvSerials as $rvSerial) {
			$rvSerial->setLocation($orderTypes[$orderType] . '-' . $selectedOrder);
			//echo "saving serial {$rvSerial->getDZVSerial()} location to {$rvSerial->getLocation()}<br>\n";
			$rvSerial->save();
			$updateCount++;
		}
		//die("updated $updateCount serial locations");
		return $updateCount;
	}

}
