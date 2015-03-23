<?php

class MDN_Scanner_Block_PurchaseOrder_SelectProductDelivery extends Mage_Adminhtml_Block_Widget_Form {

	private $_order;
	private $_products;
	private $_serials;

	/**
	 * Return purchase order
	 *
	 */
	public function getOrder() {
		if (!$this->_order) {
			if (($orderId = $this->getRequest()->getParam('po_num'))) {
				$this->_order = mage::getModel('Purchase/Order')->load($orderId);
			} else
				die("No order specified!");
		}

		return $this->_order;
	}

	/**
	 * Return products
	 *
	 */
	public function getProducts() {
		if (!$this->_products) {
			$this->_products = $this->getOrder()->getProducts();
		}

		return $this->_products;
	}

	public function getSerials() {
		if (!$this->_serials) {
			$this->_serials = Mage::getModel('barcodes/barcodes')->getCollection()
					->addFieldToFilter('purchase_order', array('eq' => $this->getOrder()->getData('po_order_id')))
					->setOrder('dzv_serial', 'ASC');
			;
		}

		return $this->_serials;
	}

	/**
	 * Return barcodes for 1 product
	 *
	 * @param unknown_type $productId
	 */
	public function getBarcodes($productId) {
		return mage::helper('AdvancedStock/Product_Barcode')->getBarcodesForProduct($productId);
	}

	/**
	 * Form submit url
	 *
	 * @return unknown
	 */
	public function getSubmitUrl() {
		return $this->getUrl('Scanner/PurchaseOrder/CreateDelivery');
	}

}
