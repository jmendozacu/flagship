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
class MDN_Scanner_PurchaseOrderController extends Mage_Adminhtml_Controller_Action {

	/**
	 * Select supplier
	 *
	 */
	public function SelectSupplierAction() {
		$this->loadLayout();
		$this->renderLayout();
	}

	/**
	 * Select purchase order
	 *
	 */
	public function SelectPurchaseOrderAction() {
		$this->loadLayout();
		$this->renderLayout();
	}

	/**
	 * Select products for delivery
	 *
	 */
	public function SelectProductDeliveryAction() {
		$this->loadLayout();
		$this->renderLayout();
	}

	/**
	 * Create delivery
	 *
	 */
	public function CreateDeliveryAction() {
		//load datas
		$post = $this->getRequest()->getPost();
		$poId = $this->getRequest()->getPost('po_num');
		
		$purchaseOrder = mage::getModel('Purchase/Order')->load($poId);
		
		$orderNum = $purchaseOrder->getData('po_order_id');
		
		$warehouseId = mage::getStoreConfig('purchase/purchase_order/default_warehouse_for_delivery');
		$warehouse = mage::getModel('AdvancedStock/Warehouse')->load($warehouseId);
		
		foreach ($purchaseOrder->getProducts() as $product) {
			$item_id = $product->getData('pop_num');
			$productId = $product->getData('pop_product_id');
			$qty = $this->getRequest()->getPost('product_' . $productId);
			
			if ($qty > 0) {
				//todo : add a setting to enable to select warehouse at user level
				$description = 'Purchase Order #' . $purchaseOrder->getpo_order_id();
				$purchaseOrder->createDelivery($product, $qty, date('Y-m-d'), $description, $warehouseId);
				
				/*** deprecated until ERP product serial locations are plugged in
				 * For this to work, we would need to add a button to add a warehouse barcode per product.	
				//store location (if set)
				$location = $this->getRequest()->getPost('location_' . $product->getId());
				if ($location != '')
					$warehouse->setProductLocation($product->getpop_product_id(), $location);

				//add barcode (if set)
				$barcode = $this->getRequest()->getPost('barcode_' . $product->getId());
				if ($barcode != '') {
					$productId = $product->getpop_product_id();
					mage::helper('AdvancedStock/Product_Barcode')->addBarcodeIfNotExists($productId, $barcode);
				}
				*/
				
				$serials = $this->getRequest()->getPost('serials_' . $productId);
				$serials = explode(',', $serials);
				
				// mark Rvtech serial locations
				$rvSerials = Mage::getModel('barcodes/barcodes')->getCollection()
					->addFieldToFilter('product_id', $productId)
					->addFieldToFilter('dzv_serial', array('in' => $serials))
				;
				foreach ($rvSerials as $row) {
					$row->setLocation('DOCK');
					
					try {
						// Save it row by row :)
						$row->save();
					} catch (Exception $e) {
						$errors[] = $e->getMessage();
					}
				}
				
				// attach scanned serials to purchase order product
				mage::helper('AdvancedStock/Product_Serial')->addAVSerialsFromDelivery($product->getId(), $purchaseOrder, $serials, null);
			}
		}
		
		//update PO status & progress delivery
		if ($purchaseOrder->isCompletelyDelivered())
			$purchaseOrder->setpo_status(MDN_Purchase_Model_Order::STATUS_COMPLETE);
		
		$purchaseOrder->computeDeliveryProgress();

		$this->loadLayout();
		$this->renderLayout();
	}

}

