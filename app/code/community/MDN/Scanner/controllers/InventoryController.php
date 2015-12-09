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
class MDN_Scanner_InventoryController extends Mage_Adminhtml_Controller_Action {

	private $_resultBlock = NULL;

	/**
	 * Display menu
	 *
	 */
	public function IndexAction() {
		$this->loadLayout();
		$this->renderLayout();
	}

	/**
	 * Process search
	 *
	 */
	public function processSearchAction() {
		$this->loadLayout();
		$query = $this->getRequest()->getPost('query');
		
		// needs to search by ERP barcode, Serials module serial, or UPC
		$resultBlock = $this->getLayout()->getBlock('scanner_inventory_result');
		$this->_resultBlock = $resultBlock;

		$resultBlock->initResult($query);
		
		// we only expect one result from barcode, UPC, or serial
		if ($resultBlock->hasOnlyOneResult()) {
			$keyword_type = $resultBlock->getKeywordType();
			$this->_redirect('Scanner/Inventory/ProductInformation', array('product_id' => $resultBlock->getProductId(), 'keyword' => $query, 'keyword_type' => $keyword_type));
		} else {
			$this->renderLayout();
		}
	}

	/**
	 * Return product information
	 *
	 */
	public function ProductInformationAction() {
		$this->loadLayout();
		$this->renderLayout();
	}

	/**
	 * add a barcode to product
	 *
	 */
	public function AddBarcodeAction() {
		//get data
		$productId = $this->getRequest()->getParam('product_id');
		$barcode = $this->getRequest()->getParam('barcode');
		$barcodeHelper = mage::helper('AdvancedStock/Product_Barcode');

		//init vars
		$error = false;
		$message = '';


		//check if barcode exists
		if ($barcodeHelper->barcodeExists($barcode)) {
			$error = true;
			$message = $this->__('Barcode already used');
		} else {
			//add bar code
			$barcodeHelper->addBarcodeIfNotExists($productId, $barcode);
			$message = $this->__('Barcode added');
		}

		//redirect on product page
		$this->_redirect('Scanner/Inventory/ProductInformation', array('product_id' => $productId));
	}

	/**
	 * Edit stock
	 *
	 */
	public function EditStockAction() {
		$this->loadLayout();
		$this->renderLayout();
	}

	/**
	 * Change product location
	 */
	public function ChangeProductLocationAction() {
		//get param
		$loc_type = '';
		$stockId = $this->getRequest()->getParam('stock_id');
		$location = $this->getRequest()->getParam('location');

		$serial = $this->getRequest()->getParam('av_serial');
		$keyword_type = $this->getRequest()->getParam('keyword_type');
		$loc_type = $this->getRequest()->getParam('loc_type');

		if ($keyword_type == 'serial' || $loc_type == 'loc_serial') {
			$modelProductSerial = Mage::getModel('barcodes/barcodes')
					->getCollection()
					->addFieldToFilter('product_id', $stockId)
					->addFieldToFilter('dzv_serial', $serial)
					->getFirstItem();
			$modelProductSerial->setLocation($location)->save();
			$productId = $stockId;
		} else {
			$stock = mage::getModel('cataloginventory/stock_item')->load($stockId);
			$stock->setshelf_location($location)->save();
			$productId = $stock->getProductId();
		}



		//redirect to product sheet

		$this->_redirect('Scanner/Inventory/ProductInformation', array('product_id' => $productId));
	}

	/**
	 * Save stock changes
	 *
	 */
	public function SaveStockQtyAction() {
		//load information
		$stockId = $this->getRequest()->getPost('stock_id');
		$stock = mage::getModel('cataloginventory/stock_item')->load($stockId);
		$productId = $stock->getproduct_id();
		$newQty = $this->getRequest()->getPost('qty');
		$description = $this->getRequest()->getPost('description');
		$oldQty = $stock->getQty();

		//calculate diff and create stock movement
		$diff = $newQty - $oldQty;
		if ($diff <> 0) {
			$targetWarehouse = null;
			$sourceWarehouse = null;
			if ($diff < 0)
				$sourceWarehouse = $stock->getstock_id();
			else
				$targetWarehouse = $stock->getstock_id();

			$additionalData = array('sm_type' => 'adjustment');
			mage::getModel('AdvancedStock/StockMovement')->createStockMovement($productId, $sourceWarehouse, $targetWarehouse, abs($diff), $description, $additionalData);
		}

		//redirect to product sheet
		$this->_redirect('Scanner/Inventory/ProductInformation', array('product_id' => $productId));
	}

	/**
	 * Create a free delivery
	 */
	public function FreeDeliveryAction() {
		$this->loadLayout();

		$mode = $this->getRequest()->getParam('mode');
		if ($mode == 'add_product') {
			$barcode = $this->getRequest()->getParam('barcode');
			$location = $this->getRequest()->getParam('location');
			$block = $this->getLayout()->getBlock('scanner_inventory_freedelivery')->addProduct($barcode, $location);
		}

		$this->renderLayout();
	}

	/**
	 * Return product information through ajax
	 * */
	public function AjaxProductInformationAction() {
		$barcode = $this->getRequest()->getParam('barcode');
		$response = array();

		$product = mage::helper('AdvancedStock/Product_Barcode')->getProductFromBarcode($barcode);
		if ($product) {
			$response['error'] = false;
			$response['message'] = $this->__('Product found : %s', $product->getName());
			$response['product_name'] = $product->getName();
			$response['product_sku'] = $product->getSku();
		} else {
			$response['error'] = true;
			$response['message'] = $this->__('Barcode %s unknown', $barcode);
		}

		//json return
		$response = Zend_Json::encode($response);
		$this->getResponse()->setBody($response);
	}

}
