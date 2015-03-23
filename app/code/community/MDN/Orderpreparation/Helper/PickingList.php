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
class MDN_Orderpreparation_Helper_PickingList extends Mage_Core_Helper_Abstract {

	/**
	 *
	 * Define if we display product in picking list
	 *
	 * @param <type> $item
	 * @param <type> $order
	 * @return <type>
	 */
	public function isItemDisplayedInPickingList($item, $order) {
		//manage setting orderpreparation/picking_list/display_product_without_stock_management
		if (mage::getStoreConfig('orderpreparation/picking_list/display_product_without_stock_management') == 0) {
			$productId = $item->getproduct_id();
			$stockItem = mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
			if ($stockItem->getId()) {
				if (!$stockItem->ManageStock())
					return 0;
			}
		}

		//todo implement 'orderpreparation/picking_list/display_sub_products' logic
		return 1;
	}

	/*
	 * Return products
	 * associative array : key = product_id, value = qty
	 *
	 */

	public function GetProductsSummary() {
		//echo "running GetProductsSummary<br>";
		$products = array();

		$warehouseId = mage::helper('Orderpreparation')->getPreparationWarehouse();
		//echo "order preparation warehouse id = $warehouseId<br>";
		$warehouse = mage::getModel('AdvancedStock/Warehouse')->load($warehouseId);
		//die("warehouse:<pre>".print_r($warehouse, true));
		// fetch items in picking list
		$picklist = Mage::getModel('Orderpreparation/ordertoprepareitem')
				->getCollection()
				->addFieldToFilter('display_in_picking_list', 1)
				->addFieldToFilter('preparation_warehouse', $warehouseId)
				//->addFieldToFilter('user', mage::helper('Orderpreparation')->getOperator())
				->setOrder('order_id', 'ASC')
				->setOrder('product_id', 'ASC')
		;
		//echo "order preparation picklist count " . count($picklist) . "<br>\n";
		//echo "<pre>picklist: ".print_r($picklist->getData(), true)."</pre>\n";
		//exit;
		foreach ($picklist as $index => $item) {
			//echo "item $index:<pre>" . print_r($item->getData(), true) . "</pre>\n";
			//exit;
			//continue;
			// load product
			$product_id = $item->getproduct_id();
			//echo "product_id = $product_id<br>\n";
			$qty = $item->getqty();
			//echo "qty = $qty<br>\n";
			$qtyPicked = $item->getQtyPicked();
			//echo "qty_picked = $qtyPicked<br>\n";
			// add product
			if (isset($products[$product_id])) {
				//echo "product already in array<br>\n";
				$products[$product_id]->setqty($products[$product_id]->getqty() + $qty);
				//echo "upped qty to {$products[$product_id]->getqty()}<br>\n";
				$products[$product_id]->setData('qty_picked', $products[$product_id]->getData('qty_picked') + $qtyPicked);
				//echo "upped qty_picked to {$products[$product_id]->getData('qty_picked')}<br>\n";
			} else {
				//echo "adding product to array<br>\n";
				$product = mage::getmodel('catalog/product')->load($product_id);
				//echo "<pre>product: ".print_r($product->getData(), true)."</pre>\n";
				//echo "product {$product->getId()} stock item:<pre>".print_r($product->getData('stock_item')->getData(), true)."</pre>\n";
				//echo "product qty = {$product->getQty()}<br>\n";
				$product->setqty($qty);
				//echo "set product quantity to {$product->getQty()}<br>\n";
				$product->setData('qty_picked', $item->getQtyPicked());
				//echo "set product quantity picked to {$product->getData('qty_picked')}<br>\n";
				$products[$product_id] = $product;
				// define additional information
				$picturePath = '';
				if ($product->getSmallImage())
					$picturePath = Mage::getBaseDir() . DS . 'media' . DS . 'catalog' . DS . 'product' . $product->getSmallImage();
				//echo "picture path = $picturePath<br>\n";
				$product->setpicture_path($picturePath);
				$product->setbarcode(mage::helper('AdvancedStock/Product_Barcode')->getBarcodeForProduct($product));
				//echo "set barcode image to {$product->getBarcodeImage()}<br>\n";
				// BOF get and set the Serials and Locations for the product : Arun
				$serial_location = array();
				//echo "fetching rvProducts serials where product id = $product_id<br>\n";
				$rvProducts = Mage::getSingleton('barcodes/barcodes')->getCollection()
						->addFieldToFilter('product_id', $product_id);
				//echo "<pre>rvProducts: ".print_r($rvProducts->getData(), true)."</pre>\n";
				foreach ($rvProducts as $serialAndLocation) {
					$serial_location['serials'][] = $serialAndLocation->getData('dzv_serial');
					$serial_location['locations'][] = $serialAndLocation->getData('location');
					//echo "added serial {$serialAndLocation->getData('dzv_serial')} and location {$serialAndLocation->getData('location')}<br>\n";
				}

				if (!empty($serial_location)) {
					$product->setData('av_serials', $serial_location['serials']);
					$product->setData('av_locations', $serial_location['locations']);
					//echo "set serials and locations to product<br>\n";
				}

				// set ERP location and manufacturer
				$product->setlocation($warehouse->getProductLocation($product->getId()));
				$product->setmanufacturer($product->getAttributeText('manufacturer'));
				//echo "set product location to {$product->getlocation()} and manufacturer to {$product->getmanufacturer()}<br>\n";
				//echo "product values:<pre>\n";
				foreach ($product->getData() as $key => $value) {
					if (is_string($value)) {
						//echo "	[$key] => $value\n";
					}
				}
				//echo "</pre>\n";
			}
		}
		//echo "products count: ".count($products)."<br>\n";
		//exit;
		//tri la liste
		//usort($products, array("MDN_Orderpreparation_Helper_PickingList", "sortProductPerLocationAndManufacturer"));
		usort($products, array("MDN_Orderpreparation_Helper_PickingList", "sortProductByName"));
		//die("products:<pre>" . print_r($products, true));
		return $products;
	}

	/**
	 * Create picking list from a list of orders
	 *
	 */
	public function getProductsSummaryFromOrderIds(array $orderIds, $warehouseId) {
		$comments = '';
		$products = array();

		//parse orders collection
		$collection = mage::getModel('sales/order')
				->getCollection()
				->addAttributeToSelect('*')
				->addFieldToFilter('entity_id', array('in' => $orderIds));
		foreach ($collection as $order) {
			$comments .= mage::helper('Orderpreparation')->__('Order #%s : %s', $order->getIncrementId(), $order->getCustomerName()) . "\n";
			foreach ($order->getAllItems() as $orderItem) {

				//skip product if doesn't belong to the right warehouse
				if ($orderItem->getpreparation_warehouse() != $warehouseId)
					continue;

				//add product
				$qty = (int) $orderItem->getqty_ordered();
				$product_id = $orderItem->getproduct_id();
				if (isset($products[$product_id]))
					$products[$product_id]->setqty($products[$product_id]->getqty() + $qty);
				else {
					$product = mage::getmodel('catalog/product')->load($product_id);

					//check orderpreparation/picking_list/display_product_without_stock_management setting
					if (mage::getStoreConfig('orderpreparation/picking_list/display_product_without_stock_management') == 0) {
						$stockItem = mage::getModel('cataloginventory/stock_item')->loadByProduct($product_id);
						if ($stockItem->getId()) {
							if (!$stockItem->ManageStock())
								continue;
						}
					}

					$product->setqty($qty);
					$products[$product_id] = $product;

					//define additional information
					$picturePath = '';
					if ($product->getSmallImage())
						$picturePath = Mage::getBaseDir() . DS . 'media' . DS . 'catalog' . DS . 'product' . $product->getSmallImage();
					$product->setpicture_path($picturePath);
					$product->setbarcode(mage::helper('AdvancedStock/Product_Barcode')->getBarcodeForProduct($product));

					$warehouse = $orderItem->getPreparationWarehouse();
					$product->setlocation($warehouse->getProductLocation($product->getId()));
					$product->setmanufacturer($product->getAttributeText('manufacturer'));
				}
			}
		}

		//return datas
		$products = array();
		$products['comments'] = $comments;
		$products['products'] = $products;

		return $products;
	}

	/**
	 * Sort product by name
	 *
	 */
	public static function sortProductByName($a, $b) {
		if ($a->getname() != $b->getname()) {
			if ($a->getname() < $b->getname())
				return -1;
			else
				return 1;
		}
	}

}
