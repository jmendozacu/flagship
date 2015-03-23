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
class MDN_Scanner_OrderPreparationController extends Mage_Adminhtml_Controller_Action {

	/**
	 * Picking
	 *
	 */
	protected $_location = 'DOCK';

	public function indexAction() {
		$this->loadLayout();
		$this->renderLayout();
	}

	/**
	 * This function should take the product id's/serials submitted,
	 * mark the submitted serials' products as "picked" (display_in_picking_list = 0),
	 * then mark the serial location as "DOCK" in Rvtech Serials.
	 * SECONDARY: save serial to item on order for product:
	 * see MDN/OrderPreparation/controllers/OrderPreparationController->SaveOrderAction $preparationData section.
	 */
	public function savePickingAction() {
		echo "running MDN_Scanner_OrderPreparationController savePickingAction<br>\n";
		$postData = $this->getRequest()->getParams();
		echo "postData:<pre>" . print_r($postData, true)."</pre>\n";
		$postData = array(
			'product_253' => 2, 'serials_253' => 'FS20140127-0074,FS20140127-0071,',
			'product_269' => 0, 'serials_269' => '',
			'product_493' => 3, 'serials_493' => 'FS20140127-0094,FS20140127-0093,FS20140127-0099,',
		);
		echo "test postData:<pre>" . print_r($postData, true)."</pre>\n";
		$productSerials = array();
		foreach ($postData as $key => $value) {
			if (strpos($key, 'serials') !== false) {
				$arr = explode('_', $key);
				if (!empty($arr[1]) && !empty($value)) {
					$id = $arr[1];
					echo "found serials product id $id<br>\n";
					$serials = explode(",", $value);
					echo "serials:<pre>".print_r($serials, true)."</pre>\n";
					foreach ($serials as $serial) {
						$serial = str_replace("\r", '', $serial);
						$serial = str_replace("\n", '', $serial);
						if (!empty($serial)) {
							$productSerials[$id][] = $serial;
						}
					}
				}
			}
		}
		$picked = 0;
		echo "product serials:<pre>".print_r($productSerials, true)."</pre>\n";
		if (!empty($productSerials)) {
			echo "array keys product ids:<pre>".print_r(array_keys($productSerials), true)."</pre>\n";
			// select products in picking list
			$warehouseId = mage::helper('Orderpreparation')->getPreparationWarehouse();
			echo "warehouse id: $warehouseId<br>\n";
			$orderItems = Mage::getModel('Orderpreparation/ordertoprepareitem')
				->getCollection()
				->addFieldToFilter('display_in_picking_list', 1)
				->addFieldToFilter('preparation_warehouse', $warehouseId)
				->addFieldToFilter('product_id', array_keys($productSerials))
				->setOrder('order_id', 'ASC')
			;
			echo "Orderpreparation/ordertoprepareitem->getSelect(): ".$orderItems->getSelect()."<br>\n";
			echo "order items class ".get_class($orderItems)." data:<pre>".print_r($orderItems->getData(), true)."</pre>\n";
			//exit;
			/* FIX: this needs to set display_in_picking_list = 0 order item that had a scanned dzv_serial
			 * Example: [serials_493] => FS20140127-0094 (where 493 is product id)
			  FS20140127-0095
			  FS20140127-0096
			 */
			$setSerials = array();
			foreach ($orderItems as $index => $item) {
				echo "order items index $index is of type ".gettype($item)." ".get_class($item)."<br>\n";
				echo "parsing order item id = {$item['order_item_id']}<br>\n";
				if($item->getQtyPicked() >= $item->getQty()) {
					echo "item quantity picked is greater than or equal to item quantity; skipping<br>\n";
					continue;
				}
				$product_id = $item->getProductId();
				echo "item product id = $product_id<br>\n";
				if (array_key_exists($product_id, $productSerials)) {
					echo "array key $product_id exists in product ids array<br>\n";
					foreach ($productSerials[$product_id] as $index => $serial) {
						echo "product ids $product_id index $index serial = $serial<br>\n";
						if (!empty($serial)) {
							$item->setQtyPicked($item->getQtyPicked() + 1);
							echo "set quantity picked to {$item->getQtyPicked()}<br>\n";
							$picked++;
							$setSerials[] = $serial;
							
							/**
							 * TODO: SECONDARY: append $serial to same place Magento admin > ERP > Order Preparation > 
							 * Prepare Orders > any order > Preparation tab > Serials text box saves to
							 */
							
							if($item->getQtyPicked() >= $item->getQty()) {
								echo "item qty_picked {$item->getQtyPicked()} >= item qty {$item->getQty()}<br>\n";
								$item->setData('display_in_picking_list', 0);
								echo "set item display_in_picking_list = {$item->getDisplayInPickingList()} (should be 0)<br>\n";
							}
						}
					}
				}
				echo "<pre>final item data: ".print_r($item->getData(), true)."</pre>\n";
			}
			echo "<pre>setSerials: ".print_r($setSerials, true)."</pre>\n";
			// DO NOT SAVE UNLESS display_in_picking_list = 0 IS SET FOR SUBMITTED SERIALS!!!
			//echo "saving order data:<pre>".print_r($orderItems->getData(), true)."</pre>\n";
			//exit;
			//$orderItems->save();
			
			$rvSerials = Mage::getModel('barcodes/barcodes')->getCollection()
				->addFieldToFilter('dzv_serial', array('in' => $setSerials))
				//->getSelect()
			;
			echo "rvSerials Select: ".$rvSerials->getSelect()."<br>\n";
			echo "rvSerials Data count ".count($rvSerials->getData()).":<pre>".print_r($rvSerials->getData(), true)."</pre>\n";
			echo "setting serial locations to {$this->_location}<br>\n";
			foreach ($rvSerials as $row) {
				$row->setLocation($this->_location);
				echo "set serial {$row->getData('dzv_serial')} to {$row->getLocation()}<br>\n";
				try {
					// Save it row by row :)
					echo "saving serial data:<pre>".print_r($row->getData(), true)."</pre>\n";
					//$row->save();
				} catch (Exception $e) {
					$errors[] = $e->getMessage();
				}
			}
		}
		//die("quantity picked: $picked");
		// TODO: need to send errors to redirected scanner page if possible
		if ($errors) {
			die(implode("\n", $errors));
		} else {
			Mage::getSingleton('adminhtml/session')->addSuccess($this->__("Picked $picked products."));
			$this->_redirect('Scanner/OrderPreparation/index');
		}
	}

}